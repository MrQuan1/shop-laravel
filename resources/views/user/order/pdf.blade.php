<!DOCTYPE html>
<html>
<head>
    <title>Đơn hàng @if($order)- {{$order->order_number}} @endif</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>

@if($order)
    <style type="text/css">
        .invoice-header {
            background: #f7f7f7;
            padding: 10px 20px 10px 20px;
            border-bottom: 1px solid gray;
        }
        .site-logo {
            margin-top: 20px;
        }
        .invoice-right-top h3 {
            padding-right: 20px;
            margin-top: 20px;
            color: green;
            font-size: 30px!important;
            font-family: serif;
        }
        .invoice-left-top {
            border-left: 4px solid green;
            padding-left: 20px;
            padding-top: 20px;
        }
        .invoice-left-top p {
            margin: 0;
            line-height: 20px;
            font-size: 16px;
            margin-bottom: 3px;
        }
        thead {
            background: green;
            color: #FFF;
        }
        .authority h5 {
            margin-top: -10px;
            color: green;
        }
        .thanks h4 {
            color: green;
            font-size: 25px;
            font-weight: normal;
            font-family: serif;
            margin-top: 20px;
        }
        .site-address p {
            line-height: 6px;
            font-weight: 300;
        }
        .table tfoot .empty {
            border: none;
        }
        .table-bordered {
            border: none;
        }
        .table-header {
            padding: .75rem 1.25rem;
            margin-bottom: 0;
            background-color: rgba(0,0,0,.03);
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        .table td, .table th {
            padding: .30rem;
        }
    </style>
    <div class="invoice-header">
        <div class="float-left site-logo">
            <img src="{{asset('backend/img/logo.png')}}" alt="">
        </div>
        <div class="float-right site-address">
            <h4>{{env('APP_NAME')}}</h4>
            <p>{{env('APP_ADDRESS')}}</p>
            <p>Số điện thoại: <a href="tel:{{env('APP_PHONE')}}">{{env('APP_PHONE')}}</a></p>
            <p>Email: <a href="mailto:{{env('APP_EMAIL')}}">{{env('APP_EMAIL')}}</a></p>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="invoice-description">
        <div class="invoice-left-top float-left">
            <h6>Hóa đơn cho khách hàng</h6>
            <h3>{{$order->name}}</h3>
            <div class="address">
                <p>
                    <strong>Địa chỉ: </strong>
                    {{ $order->address }}
                </p>
                <p><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
                <p><strong>Email:</strong> {{ $order->email }}</p>
                @if($order->note)
                    <p><strong>Ghi chú:</strong> {{ $order->note }}</p>
                @endif
            </div>
        </div>
        <div class="invoice-right-top float-right" class="text-right">
            <h3>Invoice #{{$order->order_number}}</h3>
            <p>{{ $order->created_at->format('D d m Y') }}</p>
        </div>
        <div class="clearfix"></div>
    </div>
    <section class="order_details pt-3">
        <div class="table-header">
            <h5>Chi tiết đơn hàng</h5>
        </div>
        <table class="table table-bordered table-stripe">
            <thead>
            <tr>
                <th scope="col" class="col-6">Sản phẩm</th>
                <th scope="col" class="col-2">Số lượng</th>
                <th scope="col" class="col-2">Đơn giá</th>
                <th scope="col" class="col-2">Thành tiền</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order->orderDetails as $detail)
                <tr>
                    <td><span>{{ $detail->product->title ?? 'Sản phẩm đã bị xóa' }}</span></td>
                    <td>{{ $detail->quantity }}</td>
                    <td><span>{{ number_format($detail->price, 0) }}đ</span></td>
                    <td><span>{{ number_format($detail->price * $detail->quantity, 0) }}đ</span></td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th scope="col" class="empty" colspan="3"></th>
                <th scope="col" class="text-right">Tổng tiền:</th>
                <th>
            <span>
                {{number_format($order->total_amount,0)}}đ
            </span>
                </th>
            </tr>
            </tfoot>
        </table>
    </section>
    <div class="thanks mt-3">
        <h4>Cảm ơn bạn đã mua hàng !!</h4>
    </div>
    <div class="authority float-right mt-5">
        <p>-----------------------------------</p>
        <h5>Chữ ký:</h5>
    </div>
    <div class="clearfix"></div>
@else
    <h5 class="text-danger">Không hợp lệ</h5>
@endif
</body>
</html>
