<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Notifications\StatusNotification;
use App\User;
use Illuminate\Http\Request;
use Notification;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function vnpay_payment(Request $request)
    {
        $order_data = session('pending_order');
        $cart_details = session('pending_cart_details');

        if (!$order_data || !$cart_details) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin đơn hàng!');
        }

        DB::beginTransaction();
        try {
            // Tạo đơn hàng
            $order = Order::create($order_data);

            // Tạo chi tiết đơn hàng từ cart_details đã lưu
            foreach ($cart_details as $cartDetail) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $cartDetail['product_id'],
                    'quantity' => $cartDetail['quantity'],
                    'price' => $cartDetail['price']
                ]);
            }

            // Xóa giỏ hàng
            $cart = Cart::where('user_id', auth()->user()->id)->first();
            if ($cart) {
                CartDetail::where('cart_id', $cart->id)->delete();
                $cart->delete();
            }

            DB::commit();

            session()->forget('pending_order');
            session()->forget('pending_cart_details');
            session()->forget('cart');
            session()->forget('coupon');

            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = route('vnpay.return');
            $vnp_TmnCode = "QJZPKEJ8";
            $vnp_HashSecret = "QUSV7K9IIR0DJM3TGRWTJE1AI2REUA3Q";

            $vnp_TxnRef = $order->id;
            $vnp_OrderInfo = 'Thanh toán đơn hàng #' . $order->order_number;
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $order->total_amount * 100;
            $vnp_Locale = 'vn';
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
            );

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

            return redirect($vnp_Url);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('home')->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
        }
    }

    public function vnpay_return(Request $request)
    {
        $vnp_HashSecret = "QUSV7K9IIR0DJM3TGRWTJE1AI2REUA3Q";
        $inputData = $request->all();

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);

        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            $orderId = $inputData['vnp_TxnRef'];
            $order = Order::find($orderId);

            if ($order) {
                if ($inputData['vnp_ResponseCode'] == '00') {
                    // Thanh toán thành công - chỉ cập nhật payment_status
                    $order->payment_status = 'paid';
                    $order->save();

                    $users = User::where('role', 'admin')->first();
                    if ($users) {
                        $details = [
                            'title' => 'Đơn hàng #' . $order->order_number . ' đã được thanh toán',
                            'actionURL' => route('order.show', $order->id),
                            'fas' => 'fa-check-circle'
                        ];
                        Notification::send($users, new StatusNotification($details));
                    }

                    return redirect()->route('home')->with('success', 'Đơn hàng đã được tạo và thanh toán thành công! Mã đơn hàng: ' . $order->order_number);
                } else {
                    // Thanh toán thất bại - xóa đơn hàng
                    DB::beginTransaction();
                    try {
                        // Xóa chi tiết đơn hàng trước
                        OrderDetail::where('order_id', $order->id)->delete();
                        // Xóa đơn hàng
                        $order->delete();

                        DB::commit();
                        return redirect()->route('home')->with('error', 'Thanh toán không thành công! Đơn hàng đã bị hủy. Vui lòng thử lại.');
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->route('home')->with('error', 'Thanh toán không thành công! Mã đơn hàng: ' . $order->order_number . '. Vui lòng liên hệ hỗ trợ.');
                    }
                }
            } else {
                return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng!');
            }
        } else {
            return redirect()->route('home')->with('error', 'Chữ ký không hợp lệ! Vui lòng liên hệ hỗ trợ.');
        }
    }
}
