@extends('backend.layouts.master')

@section('title','Order Detail')

@section('main-content')
<div class="card">
<h5 class="card-header">Đơn hàng      <a href="{{route('order.pdf',$order->id)}}" class=" btn btn-sm btn-primary shadow-sm float-right"><i class="fas fa-download fa-sm text-white-50"></i> Generate PDF</a>
  </h5>
  <div class="card-body">
    @if($order)
          <table class="table table-striped table-hover">
              <thead>
              <tr>
                  <th>STT</th>
                  <th>Số hóa đơn</th>
                  <th>Tên khách hàng</th>
                  <th>Email</th>
                  <th>Số lượng</th>
                  <th>Tổng</th>
                  <th>Trạng thái</th>
                  <th>Thao tác</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                  <td>{{$order->id}}</td>
                  <td>{{$order->order_number}}</td>
                  <td>{{ $order->name }}</td>
                  <td>{{$order->email}}</td>
                  <td>{{$order->orderDetails->sum('quantity')}}</td>
                  <td><strong>: {{ number_format($order->total_amount, 0) }}đ</strong></td>
                  <td>
                      @if($order->status == 'new')
                          <span class="badge badge-primary">Mới tiếp nhận</span>
                      @elseif($order->status == 'process')
                          <span class="badge badge-warning">Đang xử lý</span>
                      @elseif($order->status == 'delivered')
                          <span class="badge badge-success">Đã giao hàng</span>
                      @else
                          <span class="badge badge-danger">Hủy đơn</span>
                      @endif
                  </td>
                  <td>
                      <a href="{{route('order.edit',$order->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom">
                          <i class="fas fa-edit"></i>
                      </a>
                  </td>
              </tr>
              </tbody>
          </table>

    <section class="confirmation_part section_padding">
      <div class="order_boxes">
        <div class="row">
          <div class="col-lg-6 col-lx-4">
            <div class="order-info">
              <h4 class="text-center pb-4">Thông tin đơn hàng</h4>
                <table class="table">
                    <tr>
                        <td>Số hóa đơn</td>
                        <td> : {{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td>Ngày đặt hàng</td>
                        <td> : {{ $order->created_at->format('D d M, Y') }} lúc {{ $order->created_at->format('g : i a') }}</td>
                    </tr>
                    <tr>
                        <td>Số lượng</td>
                        <td> : {{ $order->orderDetails->sum('quantity') }}</td>
                    </tr>
                    <tr>
                        <td>Trạng thái đơn hàng</td>
                        <td> :
                            @if($order->status == 'new')
                                <span class="badge badge-primary">Mới tiếp nhận</span>
                            @elseif($order->status == 'process')
                                <span class="badge badge-warning">Đang xử lý</span>
                            @elseif($order->status == 'delivered')
                                <span class="badge badge-success">Đã giao hàng</span>
                            @else
                                <span class="badge badge-danger">Hủy đơn</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Mã giảm giá</td>
                        <td> : {{ number_format($order->coupon, 0) }}đ</td>
                    </tr>
                    <tr>
                        <td><strong>Tổng tiền</strong></td>
                        <td><strong>: {{ number_format($order->total_amount, 0) }}đ</strong></td>
                    </tr>
                    <tr>
                        <td>Phương thức thanh toán</td>
                        <td> :
                            @if($order->payment_method == 'cod')
                                Thanh toán khi nhận hàng
                            @else
                                Thanh toán qua VNpay
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Trạng thái thanh toán</td>
                        <td> :
                            @if($order->payment_status == 'paid')
                                <span class="badge badge-success">Đã thanh toán</span>
                            @else
                                <span class="badge badge-danger">Chưa thanh toán</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
          </div>

          <div class="col-lg-6 col-lx-4">
            <div class="shipping-info">
              <h4 class="text-center pb-4">Thông tin giao hàng</h4>
              <table class="table">
                    <tr class="">
                        <td>Tên đầy đủ</td>
                        <td> : {{ $order->name }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td> : {{ $order->email }}</td>
                    </tr>
                    <tr>
                        <td>Số điện thoại</td>
                        <td> : {{ $order->phone }}</td>
                    </tr>
                    <tr>
                        <td>Địa chỉ</td>
                        <td> : {{ $order->address }}</td>
                    </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Thêm chi tiết sản phẩm giống user -->
    <section class="order_details pt-3">
        <h4 class="text-center pb-4">Chi tiết sản phẩm</h4>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Hình ảnh</th>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order->orderDetails as $detail)
                <tr>
                    <td>
                        @if($detail->product && $detail->product->photo)
                            @php
                                $photos = explode(',', $detail->product->photo);
                            @endphp
                            <img src="{{ $photos[0] }}" alt="{{ $detail->product->title }}" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <img src="{{ asset('backend/img/thumbnail-default.jpg') }}" alt="No image" style="width: 60px; height: 60px; object-fit: cover;">
                        @endif
                    </td>
                    <td>
                        <strong>{{ $detail->product->title ?? 'Sản phẩm đã bị xóa' }}</strong>
                    </td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ number_format($detail->price, 0) }}đ</td>
                    <td>{{ number_format($detail->price * $detail->quantity, 0) }}đ</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4" class="text-right">Tổng cộng:</th>
                <th>{{ number_format($order->total_amount, 0) }}đ</th>
            </tr>
            </tfoot>
        </table>
    </section>
    @endif

  </div>
</div>
@endsection

@push('styles')
<style>
    .order-info,.shipping-info{
        background:#ECECEC;
        padding:20px;
    }
    .order-info h4,.shipping-info h4{
        text-decoration: underline;
    }

</style>
@endpush
