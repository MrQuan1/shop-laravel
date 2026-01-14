@extends('backend.layouts.master')
@section('title','Chat Logs')
@section('main-content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('backend.layouts.notification')
            </div>
        </div>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Danh sách Cuộc hội thoại</h6>
            <span class="float-right text-muted">Tổng: {{ $conversations->total() }} cuộc hội thoại</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($conversations)>0)
                    <table class="table table-bordered" id="chatlog-dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Khách hàng</th>
                            <th width="15%">Liên hệ</th>
                            <th width="35%">Tin nhắn đầu tiên</th>
                            <th width="10%">Số tin nhắn</th>
                            <th width="15%">Thời gian cuối</th>
                            <th width="5%">Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($conversations as $index => $conversation)
                            <tr>
                                <td>{{ ($conversations->currentPage()-1) * $conversations->perPage() + $index + 1 }}</td>
                                <td>
                                    <strong>{{$conversation->customer_name}}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($conversation->session_id, 15) }}</small>
                                </td>
                                <td>
                                    <small>
                                        <i class="fas fa-envelope"></i> {{$conversation->customer_email}}<br>
                                        <i class="fas fa-phone"></i> {{$conversation->customer_phone}}
                                    </small>
                                </td>
                                <td>
                                    <div class="message-preview">
                                        <i class="fas fa-user text-primary"></i>
                                        {{ Str::limit($conversation->message_content, 80) }}
                                    </div>
                                </td>
                                <td>
                        <span class="badge badge-info">
                            {{ $conversation->message_count }}
                        </span>
                                    <br>
                                    <small class="text-muted">tin nhắn</small>
                                </td>
                                <td>
                                    <small>
                                        {{ date('d/m/Y', strtotime($conversation->last_message_time)) }}<br>
                                        {{ date('H:i:s', strtotime($conversation->last_message_time)) }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{route('chatlogs.show',$conversation->session_id)}}"
                                       class="btn btn-primary btn-sm"
                                       data-toggle="tooltip"
                                       title="Xem cuộc hội thoại"
                                       data-placement="top">
                                        <i class="fas fa-comments"></i>
                                    </a>
                                    <br><br>
                                    <form method="POST" action="{{route('chatlogs.destroy',[$conversation->session_id])}}" style="display:inline;">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$conversation->session_id}}"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                title="Xóa cuộc hội thoại">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <span style="float:right">{{$conversations->links()}}</span>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Chưa có cuộc hội thoại nào!</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
    <style>
        .message-preview {
            max-height: 50px;
            overflow: hidden;
            line-height: 1.4;
        }
        .table td {
            vertical-align: middle;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Tooltip
            $('[data-toggle="tooltip"]').tooltip();

            // Sweet alert for delete
            $('.dltBtn').click(function(e){
                var form=$(this).closest('form');
                var dataID=$(this).data('id');
                e.preventDefault();
                swal({
                    title: "Xóa cuộc hội thoại?",
                    text: "Tất cả tin nhắn trong cuộc hội thoại này sẽ bị xóa vĩnh viễn!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        } else {
                            swal("Đã hủy!", "Cuộc hội thoại được giữ lại.", "info");
                        }
                    });
            })
        })
    </script>
@endpush
