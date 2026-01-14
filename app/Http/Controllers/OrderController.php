<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Notifications\StatusNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Notification;
use PDF;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('id', 'DESC')->paginate(10);
        return view('backend.order.index')->with('orders', $orders);
    }

    public function store(Request $request)
    {
        // Sửa lại validate đúng chuẩn Laravel
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'required|numeric',
            'address' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        // Kiểm tra giỏ hàng có sản phẩm không
        $cart = Cart::where('user_id', auth()->user()->id)->first();
        if (!$cart) {
            request()->session()->flash('error', 'Giỏ hàng trống!');
            return back();
        }

        $cartDetails = CartDetail::where('cart_id', $cart->id)->get();
        if ($cartDetails->isEmpty()) {
            request()->session()->flash('error', 'Giỏ hàng trống!');
            return back();
        }

        DB::beginTransaction();
        try {
            // Tạo đơn hàng với dữ liệu đơn giản
            $order_data = [
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => $request->user()->id,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'note' => $request->note,
                'total_amount' => Helpers::totalCartPrice(),
                'payment_method' => $request->payment_method ?? 'cod',
                'payment_status' => 'unpaid',
                'status' => 'new',
            ];

            // Áp dụng coupon nếu có
            if (session('coupon')) {
                $order_data['total_amount'] -= session('coupon')['value'];
                if($order_data['note']) {
                    $order_data['note'] .= ' | Áp dụng mã giảm giá: ' . session('coupon')['code'];
                } else {
                    $order_data['note'] = 'Áp dụng mã giảm giá: ' . session('coupon')['code'];
                }
            }

            // Xử lý thanh toán VNPay
            if ($request->payment_method === 'vnpay') {
                session()->put('pending_order', $order_data);
                session()->put('pending_cart_details', $cartDetails->toArray());
                return redirect()->route('vnpay.payment');
            }

            $order = Order::create($order_data);

            // Chuyển sản phẩm từ giỏ hàng sang chi tiết đơn hàng
            foreach ($cartDetails as $cartDetail) {
                $product = $cartDetail->product;

                // Kiểm tra tồn kho
                if ($product->stock < $cartDetail->quantity) {
                    DB::rollback();
                    request()->session()->flash('error', "Sản phẩm {$product->title} không đủ hàng trong kho");
                    return back();
                }

                // Tạo chi tiết đơn hàng
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $cartDetail->product_id,
                    'quantity' => $cartDetail->quantity,
                    'price' => $cartDetail->price
                ]);
            }

            // Xóa giỏ hàng
            CartDetail::where('cart_id', $cart->id)->delete();
            $cart->delete();

            // Thông báo cho admin
            $users = User::where('role', 'admin')->first();
            if ($users) {
                $details = [
                    'title' => 'Có đơn hàng mới',
                    'actionURL' => route('order.show', $order->id),
                    'fas' => 'fa-file-alt'
                ];
                Notification::send($users, new StatusNotification($details));
            }

            // Xóa session
            session()->forget('cart');
            session()->forget('coupon');

            DB::commit();
            request()->session()->flash('success', 'Bạn đã đặt hàng thành công');
            return redirect()->route('home');

        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage());
            return back();
        }
    }

    public function show($id)
    {
        $order = Order::with('orderDetails.product')->find($id);
        return view('backend.order.show')->with('order', $order);
    }

    public function edit($id)
    {
        $order = Order::find($id);
        return view('backend.order.edit')->with('order', $order);
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        $old_status = $order->status;
        $old_payment_status = $order->payment_status;

        $this->validate($request, [
            'status' => 'required|in:new,process,delivered,cancel',
            'payment_status' => 'nullable|in:paid,unpaid'
        ]);

        $data = $request->only(['status', 'payment_status']);

        DB::beginTransaction();
        try {
            // Kiểm tra nếu đơn hàng chuyển sang delivered và đã thanh toán
            if ($request->status == 'delivered' && $request->payment_status == 'paid') {
                // Chỉ giảm stock nếu trước đó chưa delivered hoặc chưa paid
                if ($old_status != 'delivered' || $old_payment_status != 'paid') {
                    $this->reduceProductStock($order);
                }
            }

            // Nếu đơn hàng bị hủy hoặc chuyển từ delivered về trạng thái khác, hoàn lại stock
            if (($old_status == 'delivered' && $old_payment_status == 'paid') &&
                ($request->status != 'delivered' || $request->payment_status != 'paid')) {
                $this->restoreProductStock($order);
            }

            $order->fill($data)->save();

            DB::commit();
            request()->session()->flash('success', 'Cập nhật đơn hàng thành công');

        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Có lỗi khi cập nhật đơn hàng: ' . $e->getMessage());
        }

        return redirect()->route('order.index');
    }

    // Hàm giảm số lượng sản phẩm
    private function reduceProductStock($order)
    {
        foreach ($order->orderDetails as $orderDetail) {
            $product = $orderDetail->product;
            if ($product && $product->stock >= $orderDetail->quantity) {
                $product->stock -= $orderDetail->quantity;
                $product->save();
            }
        }
    }

    // Hàm hoàn lại số lượng sản phẩm
    private function restoreProductStock($order)
    {
        foreach ($order->orderDetails as $orderDetail) {
            $product = $orderDetail->product;
            if ($product) {
                $product->stock += $orderDetail->quantity;
                $product->save();
            }
        }
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if ($order) {
            DB::beginTransaction();
            try {
                // Hoàn lại stock nếu đơn hàng đã delivered và paid
                if ($order->status == 'delivered' && $order->payment_status == 'paid') {
                    $this->restoreProductStock($order);
                }

                // Xóa chi tiết đơn hàng trước
                OrderDetail::where('order_id', $order->id)->delete();

                // Xóa đơn hàng
                $order->delete();

                DB::commit();
                request()->session()->flash('success', 'Xóa đơn hàng thành công');

            } catch (\Exception $e) {
                DB::rollback();
                request()->session()->flash('error', 'Đơn hàng không thể xóa: ' . $e->getMessage());
            }

            return redirect()->route('order.index');
        } else {
            request()->session()->flash('error', 'Không tìm thấy đơn hàng');
            return redirect()->back();
        }
    }

    public function orderTrack()
    {
        return view('frontend.pages.order-track');
    }

    public function productTrackOrder(Request $request)
    {
        $order = Order::where('user_id', auth()->user()->id)->where('order_number', $request->order_number)->first();

        if ($order) {
            switch ($order->status) {
                case 'new':
                    request()->session()->flash('success', 'Đơn hàng của bạn đã được đặt. Vui lòng chờ.');
                    break;
                case 'process':
                    request()->session()->flash('success', 'Đơn hàng của bạn đang được xử lý. Vui lòng chờ.');
                    break;
                case 'delivered':
                    request()->session()->flash('success', 'Đơn hàng của bạn đã được giao. Xin chân thành cảm ơn.');
                    break;
                default:
                    request()->session()->flash('error', 'Đơn hàng của bạn đã bị hủy, vui lòng thử lại.');
                    break;
            }
            return redirect()->route('order.track');
        } else {
            request()->session()->flash('error', 'Mã đơn hàng không hợp lệ, vui lòng thử lại.');
            return back();
        }
    }

    public function pdf(Request $request)
    {
        $order = Order::with('orderDetails.product')->find($request->id);
        $file_name = $order->order_number . '-' . $order->name . '.pdf';
        $pdf = PDF::loadview('backend.order.pdf', compact('order'));
        return $pdf->download($file_name);
    }
}
