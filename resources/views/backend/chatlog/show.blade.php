@extends('backend.layouts.master')
@section('title','Chi tiết cuộc hội thoại')
@section('main-content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-comments"></i> Chi tiết cuộc hội thoại
            </h1>
            <a href="{{ route('chatlogs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($messages->count() > 0)
            <!-- Thông tin khách hàng -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-user"></i> {{ $messages->first()->customer_name }}
                            </h6>
                            <small>
                                <i class="fas fa-envelope"></i> {{ $messages->first()->customer_email }} |
                                <i class="fas fa-phone"></i> {{ $messages->first()->customer_phone }}
                            </small>
                        </div>
                        <div class="col-md-4 text-right">
                            <small>
                                <strong>Session:</strong> <code>{{ $session_id }}</code><br>
                                <strong>Tổng tin nhắn:</strong> {{ $messages->count() }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cuộc hội thoại -->
            <div class="card shadow">
                <div class="card-body p-0">
                    <div class="chat-container" style="height: 600px; overflow-y: auto; padding: 20px; background: #f8f9fc;">
                        @foreach($messages as $msg)
                            <div class="message-wrapper mb-3">
                                @if($msg->message_type == 'user')
                                    <!-- Tin nhắn từ user -->
                                    <div class="d-flex justify-content-end">
                                        <div class="message-bubble user-message" style="max-width: 70%;">
                                            <div class="message-content bg-primary text-white p-3 rounded-lg">
                                                {{ $msg->message_content }}
                                            </div>
                                            <div class="message-time text-right mt-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i>
                                                    {{ date('d/m/Y H:i:s', strtotime($msg->created_at)) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Tin nhắn từ bot -->
                                    <div class="d-flex justify-content-start">
                                        <div class="message-bubble bot-message" style="max-width: 70%;">
                                            <div class="message-content bg-light border p-3 rounded-lg">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-robot text-success mr-2 mt-1"></i>
                                                    <div>{{ $msg->message_content }}</div>
                                                </div>
                                            </div>
                                            <div class="message-time mt-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-robot"></i> Bot -
                                                    {{ date('d/m/Y H:i:s', strtotime($msg->created_at)) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i>
                                Bắt đầu: {{ date('d/m/Y H:i:s', strtotime($messages->first()->created_at)) }}
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i>
                                Kết thúc: {{ date('d/m/Y H:i:s', strtotime($messages->last()->created_at)) }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không tìm thấy tin nhắn nào</h5>
                    <p class="text-muted">Session ID này không tồn tại hoặc đã bị xóa.</p>
                </div>
            </div>
        @endif
    </div>

    <style>
        .chat-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .message-bubble {
            animation: fadeInUp 0.3s ease-in-out;
        }

        .user-message .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border-radius: 18px 18px 5px 18px !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .bot-message .message-content {
            background: white !important;
            border: 1px solid #e3e6f0 !important;
            border-radius: 18px 18px 18px 5px !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-time {
            font-size: 0.75rem;
        }
    </style>
@endsection
