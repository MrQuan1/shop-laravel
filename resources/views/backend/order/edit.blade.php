@extends('backend.layouts.master')

@section('title','Order Detail')

@section('main-content')
<div class="card">
  <h5 class="card-header">Chỉnh sửa đơn hàng</h5>
  <div class="card-body">
    <form action="{{route('order.update',$order->id)}}" method="POST">
      @csrf
      @method('PATCH')
        <div class="form-group">
            <label for="status">Trạng thái đơn hàng :</label>
            <select name="status" class="form-control">
                <option value="new" {{ ($order->status=='delivered' || $order->status=="process" || $order->status=="cancel") ? 'disabled' : '' }}  {{ ($order->status=='new') ? 'selected' : '' }}>Mới tiếp nhận</option>

                <option value="process" {{ ($order->status=='delivered' || $order->status=="cancel") ? 'disabled' : '' }}  {{ ($order->status=='process') ? 'selected' : '' }}>Đang xử lý</option>

                <option value="delivered" {{ ($order->status=="cancel") ? 'disabled' : '' }}  {{ ($order->status=='delivered') ? 'selected' : '' }}>Đã giao hàng</option>

                <option value="cancel" {{ ($order->status=='delivered') ? 'disabled' : '' }}  {{ ($order->status=='cancel') ? 'selected' : '' }}>Hủy đơn</option>

            </select>
        </div>

        <div class="form-group">
            <label for="payment_status">Trạng thái thanh toán :</label>
            <select name="payment_status" class="form-control">
                <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
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
