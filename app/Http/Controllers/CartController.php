<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function addToCart(Request $request, $slug)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
        }

        $product = Product::where('slug', $slug)->first();
        if (!$product) {
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
        }

        if ($product->stock < 1) {
            return redirect()->back()->with('error', 'Sản phẩm đã hết hàng!');
        }

        $user_id = Auth::user()->id;

        DB::beginTransaction();
        try {
            // Tìm hoặc tạo cart cho user
            $cart = Cart::firstOrCreate(['user_id' => $user_id]);

            // Kiểm tra sản phẩm đã có trong cart chưa
            $cartDetail = CartDetail::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            $price = $product->price;
            if ($product->discount > 0) {
                $price = $product->price - ($product->price * $product->discount / 100);
            }

            if ($cartDetail) {
                // Nếu đã có, tăng số lượng
                $cartDetail->quantity += 1;
                $cartDetail->price = $price; // Cập nhật giá mới nhất
                $cartDetail->save();
            } else {
                // Nếu chưa có, tạo mới
                CartDetail::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $price
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function singleAddToCart(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
        }

        $request->validate([
            'slug' => 'required',
            'quant' => 'required|array',
            'quant.*' => 'required|numeric|min:1'
        ]);

        $product = Product::where('slug', $request->slug)->first();
        if (!$product) {
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
        }

        $quantity = array_sum($request->quant);
        if ($product->stock < $quantity) {
            return redirect()->back()->with('error', 'Không đủ hàng trong kho!');
        }

        $user_id = Auth::user()->id;

        DB::beginTransaction();
        try {
            // Tìm hoặc tạo cart cho user
            $cart = Cart::firstOrCreate(['user_id' => $user_id]);

            // Kiểm tra sản phẩm đã có trong cart chưa
            $cartDetail = CartDetail::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            $price = $product->price;
            if ($product->discount > 0) {
                $price = $product->price - ($product->price * $product->discount / 100);
            }

            if ($cartDetail) {
                // Nếu đã có, tăng số lượng
                $cartDetail->quantity += $quantity;
                $cartDetail->price = $price; // Cập nhật giá mới nhất
                $cartDetail->save();
            } else {
                // Nếu chưa có, tạo mới
                CartDetail::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function cartDelete($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $cartDetail = CartDetail::find($id);
        if (!$cartDetail) {
            return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ hàng!');
        }

        // Kiểm tra quyền sở hữu
        $cart = Cart::where('user_id', Auth::user()->id)->first();
        if (!$cart || $cartDetail->cart_id != $cart->id) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa sản phẩm này!');
        }

        $cartDetail->delete();
        return redirect()->back()->with('success', 'Sản phẩm đã được xóa khỏi giỏ hàng!');
    }

    public function cartUpdate(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $request->validate([
            'quant' => 'required|array',
            'quant.*' => 'required|numeric|min:1',
            'qty_id' => 'required|array'
        ]);

        $cart = Cart::where('user_id', Auth::user()->id)->first();
        if (!$cart) {
            return redirect()->back()->with('error', 'Giỏ hàng trống!');
        }

        DB::beginTransaction();
        try {
            foreach ($request->qty_id as $key => $cart_detail_id) {
                $quantity = $request->quant[$key];

                $cartDetail = CartDetail::where('id', $cart_detail_id)
                    ->where('cart_id', $cart->id)
                    ->first();

                if ($cartDetail) {
                    $product = Product::find($cartDetail->product_id);
                    if ($product && $product->stock >= $quantity) {
                        $cartDetail->quantity = $quantity;
                        $cartDetail->save();
                    } else {
                        DB::rollback();
                        return redirect()->back()->with('error', "Sản phẩm {$product->title} không đủ hàng trong kho!");
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Giỏ hàng đã được cập nhật!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Vui lòng đăng nhập để thanh toán!');
        }

        $cart = Cart::where('user_id', Auth::user()->id)->first();
        if (!$cart) {
            return redirect()->route('home')->with('error', 'Giỏ hàng trống!');
        }

        $cartDetails = CartDetail::with('product')->where('cart_id', $cart->id)->get();
        if ($cartDetails->isEmpty()) {
            return redirect()->route('home')->with('error', 'Giỏ hàng trống!');
        }

        return view('frontend.pages.checkout', compact('cartDetails'));
    }
}
