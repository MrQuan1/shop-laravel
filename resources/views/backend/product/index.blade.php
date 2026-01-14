@extends('backend.layouts.master')

@section('main-content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('backend.layouts.notification')
            </div>
        </div>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Danh sách sản phẩm</h6>
            <a href="{{route('product.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Thêm sản phẩm"><i class="fas fa-plus"></i> Thêm sản phẩm</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($products)>0)
                    <table class="table table-bordered" id="product-dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tiêu đề</th>
                            <th>Danh mục</th>
                            <th>Thương hiệu</th>
                            <th>Giá</th>
                            <th>Giảm giá</th>
                            <th>Kho</th>
                            <th>Nổi bật</th>
                            <th>Tình trạng</th>
                            <th>Ảnh</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>STT</th>
                            <th>Tiêu đề</th>
                            <th>Danh mục</th>
                            <th>Thương hiệu</th>
                            <th>Giá</th>
                            <th>Giảm giá</th>
                            <th>Kho</th>
                            <th>Nổi bật</th>
                            <th>Tình trạng</th>
                            <th>Ảnh</th>
                            <th>Thao tác</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{$product->id}}</td>
                                <td>{{$product->title}}</td>
                                <td>{{$product->cat_info->title ?? 'Không có danh mục'}}</td>
                                <td>{{$product->brand->title ?? 'Không có thương hiệu'}}</td>
                                <td>{{number_format($product->price, 0)}}đ</td>
                                <td>{{$product->discount ?? 0}}%</td>
                                <td>
                                    @if($product->stock > 0)
                                        <span class="badge badge-success">{{$product->stock}}</span>
                                    @else
                                        <span class="badge badge-danger">Hết hàng</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->is_featured == 1)
                                        <span class="badge badge-primary">Có</span>
                                    @else
                                        <span class="badge badge-secondary">Không</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->condition == 'new')
                                        <span class="badge badge-success">Mới</span>
                                    @elseif($product->condition == 'hot')
                                        <span class="badge badge-danger">Hot</span>
                                    @else
                                        <span class="badge badge-info">Mặc định</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->photo)
                                        @php
                                            $photo = explode(',', $product->photo);
                                        @endphp
                                        <img src="{{$photo[0]}}" class="img-fluid zoom" style="max-width:80px" alt="{{$product->title}}">
                                    @else
                                        <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid" style="max-width:80px" alt="No image">
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('product.edit',$product->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="Sửa" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('product.destroy',[$product->id])}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn" data-id={{$product->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Xóa"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <span style="float:right">{{$products->links()}}</span>
                @else
                    <h6 class="text-center">Không có sản phẩm nào! Vui lòng thêm sản phẩm mới.</h6>
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
        .zoom {
            transition: transform .2s;
        }
        .zoom:hover {
            transform: scale(3);
        }
    </style>
@endpush

@push('scripts')
    <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>

    <script>
        $('#product-dataTable').DataTable({
            "scrollX": false,
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[9, 10]
                }
            ]
        });
    </script>

    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.dltBtn').click(function(e){
                var form = $(this).closest('form');
                var dataID = $(this).data('id');
                e.preventDefault();

                swal({
                    title: "Bạn có chắc không?",
                    text: "Khi xóa sẽ không thể khôi phục dữ liệu!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        } else {
                            swal("Dữ liệu an toàn!");
                        }
                    });
            });
        });
    </script>
@endpush
