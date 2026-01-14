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
            <h6 class="m-0 font-weight-bold text-primary float-left">Danh sách bài viết</h6>
            <a href="{{route('post.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Thêm bài viết"><i class="fas fa-plus"></i> Thêm bài viết</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($posts)>0)
                    <table class="table table-bordered" id="post-dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tiêu đề & Slug</th>
                            <th>Danh mục</th>
                            <th>Tác giả</th>
                            <th>Ảnh</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>STT</th>
                            <th>Tiêu đề & Slug</th>
                            <th>Danh mục</th>
                            <th>Tác giả</th>
                            <th>Ảnh</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($posts as $post)
                            @php
                                $author_info=DB::table('users')->select('name')->where('id',$post->added_by)->get();
                            @endphp
                            <tr>
                                <td>{{$post->id}}</td>
                                <td>
                                    <div>
                                        <strong>{{$post->title}}</strong>
                                        <br>
                                        <small class="text-muted">Slug: {{$post->slug}}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $post->cat_info->title ?? 'Không có danh mục' }}</span>
                                </td>
                                <td>
                                    @foreach($author_info as $data)
                                        <span class="badge badge-secondary">{{$data->name}}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($post->photo)
                                        <img src="{{$post->photo}}" class="img-fluid zoom" style="max-width:80px; border-radius: 5px;" alt="{{$post->title}}">
                                    @else
                                        <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid" style="max-width:80px; border-radius: 5px;" alt="No image">
                                    @endif
                                </td>
                                <td>
                                    <small>{{$post->created_at->format('d/m/Y H:i')}}</small>
                                </td>
                                <td>
                                    <a href="{{route('post.edit',$post->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="Sửa" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('post.destroy',[$post->id])}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn" data-id={{$post->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Xóa"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <span style="float:right">{{$posts->links()}}</span>
                @else
                    <h6 class="text-center">Không có bài viết nào!!! Vui lòng tạo thêm</h6>
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
            transform: scale(2.5);
        }
    </style>
@endpush

@push('scripts')
    <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>

    <script>
        $('#post-dataTable').DataTable( {
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[6]
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
            $('.dltBtn').click(function(e){
                var form=$(this).closest('form');
                var dataID=$(this).data('id');
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
            })
        })
    </script>
@endpush
