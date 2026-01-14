<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    // ==================== ADMIN PANEL METHODS ====================

    public function index()
    {
        // Lấy cuộc hội thoại với tin nhắn đầu tiên và đếm chính xác số tin nhắn
        $conversations = DB::table('chat_logs as c1')
            ->select(
                'c1.session_id',
                'c1.customer_name',
                'c1.customer_email',
                'c1.customer_phone',
                'c1.message_content',
                'c1.created_at as first_message_time',
                DB::raw('(SELECT COUNT(*) FROM chat_logs c2 WHERE c2.session_id = c1.session_id) as message_count'),
                DB::raw('(SELECT MAX(created_at) FROM chat_logs c3 WHERE c3.session_id = c1.session_id) as last_message_time')
            )
            ->whereRaw('c1.created_at = (SELECT MIN(created_at) FROM chat_logs c4 WHERE c4.session_id = c1.session_id)')
            ->orderBy('last_message_time', 'DESC')
            ->paginate(10);

        return view('backend.chatlog.index')->with('conversations', $conversations);
    }

    public function show($session_id)
    {
        $messages = DB::table('chat_logs')
            ->where('session_id', $session_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('backend.chatlog.show')->with('messages', $messages)->with('session_id', $session_id);
    }

    public function destroy($session_id)
    {
        $deleted = DB::table('chat_logs')
            ->where('session_id', $session_id)
            ->delete();

        if($deleted) {
            request()->session()->flash('success','Xóa chat log thành công');
        } else {
            request()->session()->flash('error','Có lỗi xảy ra trong quá trình xóa chat log');
        }
        return redirect()->route('chatlogs.index');
    }

    // ==================== CHATBOT API METHODS ====================

    public function saveMessage(Request $request)
    {
        try {
            $data = [
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'message_type' => $request->message_type,
                'message_content' => $request->message_content,
                'session_id' => $request->session_id,
                'created_at' => now(),
            ];

            DB::table('chat_logs')->insert($data);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Chat save error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    public function getChatHistory(Request $request)
    {
        try {
            $sessionId = $request->get('session_id');

            if (!$sessionId) {
                return response()->json(['messages' => []]);
            }

            $messages = DB::table('chat_logs')
                ->where('session_id', $sessionId)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json(['messages' => $messages]);
        } catch (\Exception $e) {
            Log::error('Chat history error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Lỗi lấy lịch sử chat'], 500);
        }
    }

    // NEW: Search products by keyword
    public function searchProducts(Request $request)
    {
        try {
            $keyword = $request->get('keyword', '');

            if (empty($keyword)) {
                return response()->json(['products' => []]);
            }

            $products = DB::select("
                SELECT id, title, price, stock, summary, description, discount, status, condition
                FROM products
                WHERE (title LIKE ? OR summary LIKE ? OR description LIKE ?)
                AND status = 'active'
                ORDER BY
                    CASE
                        WHEN title LIKE ? THEN 1
                        WHEN summary LIKE ? THEN 2
                        ELSE 3
                    END,
                    title ASC
                LIMIT 5
            ", [
                "%{$keyword}%", "%{$keyword}%", "%{$keyword}%",
                "%{$keyword}%", "%{$keyword}%"
            ]);

            return response()->json(['products' => $products]);
        } catch (\Exception $e) {
            Log::error('Product search error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Lỗi tìm kiếm sản phẩm'], 500);
        }
    }

    // NEW: Get product details by ID
    public function getProductDetails(Request $request)
    {
        try {
            $productId = $request->get('id');

            $product = DB::select("
                SELECT p.*, c.title as category_name, b.title as brand_name
                FROM products p
                LEFT JOIN categories c ON p.cat_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.id = ? AND p.status = 'active'
            ", [$productId]);

            if (empty($product)) {
                return response()->json(['error' => 'Không tìm thấy sản phẩm'], 404);
            }

            return response()->json(['product' => $product[0]]);
        } catch (\Exception $e) {
            Log::error('Product details error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Lỗi lấy thông tin sản phẩm'], 500);
        }
    }

    // NEW: Get product by exact name match
    public function getProductByName(Request $request)
    {
        try {
            $name = $request->get('name', '');

            if (empty($name)) {
                return response()->json(['error' => 'Thiếu tên sản phẩm'], 400);
            }

            // Tìm kiếm chính xác trước
            $product = DB::select("
                SELECT id, title, price, stock, summary, description, discount, status, condition
                FROM products
                WHERE title LIKE ? AND status = 'active'
                ORDER BY
                    CASE
                        WHEN LOWER(title) = LOWER(?) THEN 1
                        WHEN title LIKE ? THEN 2
                        ELSE 3
                    END
                LIMIT 1
            ", ["%{$name}%", $name, "{$name}%"]);

            if (empty($product)) {
                return response()->json(['error' => 'Không tìm thấy sản phẩm'], 404);
            }

            $p = $product[0];
            $finalPrice = $p->discount ? $p->price - ($p->price * $p->discount / 100) : $p->price;

            return response()->json([
                'name' => $p->title,
                'price' => number_format($p->price, 0) . 'đ',
                'stock' => $p->stock,
                'summary' => $p->summary,
                'description' => $p->description,
                'discount' => $p->discount,
                'status' => $p->status,
                'condition' => $p->condition,
                'is_active' => $p->status === 'active',
                'final_price' => number_format($finalPrice, 0) . 'đ',
            ]);
        } catch (\Exception $e) {
            Log::error('Product by name error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Lỗi tìm sản phẩm'], 500);
        }
    }

    // NEW: Get popular products
    public function getPopularProducts()
    {
        try {
            $products = DB::select("
                SELECT id, title, price, discount, condition
                FROM products
                WHERE status = 'active'
                AND (condition = 'hot' OR condition = 'new' OR is_featured = 1)
                ORDER BY
                    CASE condition
                        WHEN 'hot' THEN 1
                        WHEN 'new' THEN 2
                        ELSE 3
                    END,
                    is_featured DESC,
                    title ASC
                LIMIT 8
            ");

            return response()->json(['products' => $products]);
        } catch (\Exception $e) {
            Log::error('Popular products error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Lỗi lấy sản phẩm nổi bật'], 500);
        }
    }
}
