<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chatbot Sài Đồng</title>
    <link rel="stylesheet" href="chatbot-hybrid.css">

    <!-- Thêm thông tin user với role check -->
    @auth
        <script>
            window.authUser = {
                name: "{{ auth()->user()->name }}",
                email: "{{ auth()->user()->email }}",
                role: "{{ auth()->user()->role ?? 'user' }}",
                isLoggedIn: true,
                isAdmin: {{ auth()->user()->role === 'admin' ? 'true' : 'false' }}
            };
        </script>
    @else
        <script>
            window.authUser = {
                isLoggedIn: false,
                isAdmin: false
            };
        </script>
    @endauth
</head>
<body>
<!-- Chat Toggle Button -->
<div id="chatbot-toggler" class="chat-toggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 2H4C2.9 2 2 2.9 2 4v14c0 1.1.9 2 2 2h4l4 4 4-4h6c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 16h-5.17L12 21.17 9.17 18H4V4h16v14z"/>
            <path d="M7 9h10v2H7zM7 13h7v2H7z"/>
        </svg>
</div>

<!-- Chatbot Popup -->
<div class="chatbot-popup" id="chatbot-popup" style="display:none;">
    <!-- Chat Header -->
    <div class="chat-header">
        <div class="header-info">
            <div class="bot-avatar-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12v1a4 4 0 0 0 4 4v-2a2 2 0 0 1-2-2v-1c0-4.42 3.58-8 8-8s8 3.58 8 8v1a2 2 0 0 1-2 2h-6v2h4v2h-6v2h6a4 4 0 0 0 4-4v-1c0-5.52-4.48-10-10-10z"/>
                    <circle cx="12" cy="12" r="1.5"/>
                </svg>
            </div>
            <div class="header-text">
                <h4>Nhân viên tư vấn</h4>
                <span class="status">Online</span>
            </div>
        </div>
        <button id="close-chatbot" class="chat-close">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
        </button>
    </div>

    <!-- Registration Form (Hiển thị đầu tiên) -->
    <div id="registration-form" class="registration-container">
        <div class="registration-content">


            <!-- Dynamic welcome message based on role -->
            <h3 id="welcome-message">
                @auth
                    @if(auth()->user()->role === 'admin')
                        Xin chào Admin {{ auth()->user()->name }}! 👑 Chào mừng bạn quay trở lại
                    @else
                        Xin chào {{ auth()->user()->name }}! Vui lòng cung cấp thêm thông tin để được hỗ trợ tốt hơn
                    @endif
                @else
                    Xin chào! Vui lòng cung cấp thông tin để được hỗ trợ tốt hơn
                @endauth
            </h3>

            <form id="customer-form" class="customer-form">
                <!-- Name field - always show -->
                <div class="form-group">
                    <label for="customer-name">
                        @auth
                            @if(auth()->user()->role === 'admin')
                                Tên hiển thị *
                            @else
                                Tên hiển thị *
                            @endif
                        @else
                            Họ và tên *
                        @endauth
                    </label>
                    <input
                        type="text"
                        id="customer-name"
                        name="name"
                        @auth
                            @if(auth()->user()->role === 'admin')
                                placeholder="Nhập tên hiển thị"
                        value="{{ auth()->user()->name }}"
                        @else
                            placeholder="Nhập tên hiển thị"
                        value="{{ auth()->user()->name }}"
                        @endif
                        @else
                            placeholder="Nhập họ và tên"
                        @endauth
                        required
                    >
                    <span class="error-message" id="name-error"></span>
                </div>

                <!-- Email field - only show if not logged in -->
                @guest
                    <div class="form-group" id="email-group">
                        <label for="customer-email">Email *</label>
                        <input
                            type="email"
                            id="customer-email"
                            name="email"
                            placeholder="example@gmail.com"
                            required
                        >
                        <span class="error-message" id="email-error"></span>
                    </div>
                @endguest

                <!-- Phone field - hide for admin, show for others -->
                @auth
                    @if(auth()->user()->role !== 'admin')
                        <div class="form-group" id="phone-group">
                            <label for="customer-phone">Số điện thoại *</label>
                            <input
                                type="tel"
                                id="customer-phone"
                                name="phone"
                                placeholder="0987654321"
                                required
                            >
                            <span class="error-message" id="phone-error"></span>
                        </div>
                    @endif
                @else
                    <div class="form-group" id="phone-group">
                        <label for="customer-phone">Số điện thoại *</label>
                        <input
                            type="tel"
                            id="customer-phone"
                            name="phone"
                            placeholder="0987654321"
                            required
                        >
                        <span class="error-message" id="phone-error"></span>
                    </div>
                @endauth

                <button type="submit" id="submit-registration" class="submit-btn">
                    <span class="btn-text">Bắt đầu chat</span>
                    <span class="btn-loading" style="display:none;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25"/>
                                <path d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z">
                                    <animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"/>
                                </path>
                            </svg>
                            Đang xử lý...
                        </span>
                </button>
            </form>

            <p class="privacy-note">
                @auth
                    @if(auth()->user()->role === 'admin')
                        Chào mừng Admin! Bạn có quyền truy cập đầy đủ hệ thống
                    @else
                        Thông tin sẽ được liên kết với tài khoản của bạn và chỉ dùng để hỗ trợ tư vấn
                    @endif
                @else
                    Thông tin của bạn sẽ được bảo mật và chỉ dùng để hỗ trợ tư vấn
                @endauth
            </p>

            <!-- Login suggestion for guests -->
            @guest
                <div class="login-suggestion">
                    <p class="login-note">
                        Đã có tài khoản?
                        <a href="{{ route('login.form') }}" target="_blank" class="login-link">
                            Đăng nhập
                        </a>
                        để trải nghiệm tốt hơn
                    </p>
                </div>
            @endguest

            <!-- Admin badge -->
            @auth
                @if(auth()->user()->role === 'admin')
                    <div class="admin-badge">
                        <div class="badge-content">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <span>Administrator</span>
                        </div>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    <!-- Chat Interface (Ẩn ban đầu) -->
    <div id="chat-interface" style="display:none;">
        <!-- Chat Messages -->
        <div class="chat-messages" id="chat-messages">
            <!-- Messages will be added by JavaScript -->
        </div>

        <!-- Chat Input Area -->
        <div class="chat-input-area">
            <div class="input-container">


                <textarea class="message-input" id="message-input" placeholder="Type something..." rows="1"></textarea>



                <button type="button" id="send-message" class="send-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>


        </div>
    </div>

    <!-- Hidden file input -->
    <input type="file" id="file-input" style="display:none;" />
</div>

<!-- Main chatbot script -->
<script src="chatbot.js"></script>
</body>
</html>
