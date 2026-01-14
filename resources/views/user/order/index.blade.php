@extends('user.layouts.master')

@section('main-content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('user.layouts.notification')
            </div>
        </div>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Danh sách đơn hàng</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($orders)>0)
                    <table class="table table-bordered" id="order-dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>STT</th>
                            <th>Số hóa đơn</th>
                            <th>Tên khách hàng</th>
                            <th>Email</th>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Tổng </th>
                            <th>Trạng thái</th>
                            <th>TT Thanh toán</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>STT</th>
                            <th>Số hóa đơn</th>
                            <th>Tên khách hàng</th>
                            <th>Email</th>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Tổng </th>
                            <th>Trạng thái</th>
                            <th>TT Thanh toán</th>
                            <th>Thao tác</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->name }}</td>
                                <td>{{ $order->email }}</td>
                                <td>
                                    <div class="product-list">
                                        @foreach($order->orderDetails->take(2) as $detail)
                                            <div class="product-item mb-1">
                                                <small>
                                                    <strong>{{ $detail->product->title ?? 'Sản phẩm đã bị xóa' }}</strong>
                                                    <br>
                                                    <span class="text-muted">{{ $detail->quantity }} x {{ number_format($detail->price, 0) }}đ</span>
                                                </small>
                                            </div>
                                        @endforeach
                                        @if($order->orderDetails->count() > 2)
                                            <small class="text-info">và {{ $order->orderDetails->count() - 2 }} sản phẩm khác...</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $order->orderDetails->sum('quantity') }}</td>
                                <td><strong>{{ number_format($order->total_amount, 0) }}đ</strong></td>
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
                                    @if($order->payment_status == 'paid')
                                        <span class="badge badge-success">Đã thanh toán</span>
                                    @else
                                        <span class="badge badge-danger">Chưa thanh toán</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('user.order.show',$order->id)}}" class="btn btn-warning btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="Xem chi tiết" data-placement="bottom"><i class="fas fa-eye"></i></a>

                                    @if($order->status == 'new')
                                        <form method="POST" action="{{route('user.order.cancel', $order->id)}}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-danger btn-sm cancelBtn" data-id="{{$order->id}}" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Hủy đơn hàng"><i class="fas fa-times"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <span style="float:right">{{$orders->links()}}</span>
                @else
                    <h6 class="text-center">Không có đơn hàng nào!!!</h6>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
    <style>
        div.dataTables_wrapper div.dataTables_paginate{
            display: none;
        }
        .product-list {
            max-width: 200px;
        }
        .product-item {
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .product-item:last-child {
            border-bottom: none;
        }
    </style>
@endpush

@push('scripts')
    <!-- Page level plugins -->
    <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>
    <script>
        $('#order-dataTable').DataTable( {
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[9]
                }
            ]
        } );
    </script>
    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.cancelBtn').click(function(e){
                var form=$(this).closest('form');
                var dataID=$(this).data('id');
                e.preventDefault();
                swal({
                    title: "Bạn có chắc muốn hủy đơn hàng?",
                    text: "Đơn hàng sẽ được hủy và không thể khôi phục!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willCancel) => {
                        if (willCancel) {
                            form.submit();
                        } else {
                            swal("Đơn hàng được giữ nguyên!");
                        }
                    });
            })
        })
    </script>
@endpush
