@extends('frontend.layouts.master')

@section('title','văn phòng phẩm || Thanh Toán')

@section('main-content')

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Trang Chủ<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0)">Thanh Toán</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Start Checkout -->
    <section class="shop checkout section">
        <div class="container">
            <form class="form" method="POST" action="{{route('cart.order')}}">
                @csrf
                <div class="row">

                    <div class="col-lg-8 col-12">
                        <div class="checkout-form">
                            <h2>Thực Hiện Thanh Toán</h2>
                            <p>Vui lòng điền thông tin để hoàn tất đơn hàng.</p>
                            <!-- Form -->
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Họ và Tên<span>*</span></label>
                                        @php
                                            $user = auth()->user();
                                            $defaultName = $user->name ?? explode('@', $user->email)[0];
                                        @endphp
                                        <input type="text" name="name" placeholder="Nhập họ và tên đầy đủ"
                                               value="{{ old('name', $defaultName) }}"
                                               @if(!$user->is_admin) readonly style="background-color: #f8f9fa; cursor: not-allowed;" @endif
                                               required>
                                        <small class="text-muted">Họ và tên được lấy từ tài khoản đăng nhập</small>
                                        @error('name')
                                        <span class='text-danger'>{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Địa chỉ Email<span>*</span></label>
                                        <input type="email" name="email" placeholder="Email của bạn"
                                               value="{{ auth()->user()->email }}" readonly
                                               style="background-color: #f8f9fa; cursor: not-allowed;">
                                        <small class="text-muted">Email được lấy từ tài khoản đăng nhập</small>
                                        @error('email')
                                        <span class='text-danger'>{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Địa chỉ giao hàng<span>*</span></label>
                                        <input type="text" name="address" placeholder="Nhập địa chỉ chi tiết để giao hàng" value="{{old('address')}}" required>
                                        @error('address')
                                        <span class='text-danger'>{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Số điện thoại<span>*</span></label>
                                        <input type="text" name="phone" pattern="[0-9]{10,15}" title="Vui lòng nhập số điện thoại hợp lệ" placeholder="Nhập số điện thoại" required value="{{ old('phone') }}">
                                        @error('phone')
                                        <span class='text-danger'>{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-12">
                                    <div class="form-group">
                                        <label>Ghi chú đơn hàng</label>
                                        <textarea name="note" rows="4" placeholder="Ghi chú thêm cho đơn hàng (tùy chọn)">{{old('note')}}</textarea>
                                        @error('note')
                                        <span class='text-danger'>{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!--/ End Form -->
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="order-details">
                            <!-- Order Widget -->
                            <div class="single-widget">
                                <h2>Tổng Tiền Giỏ Hàng</h2>
                                <div class="content">
                                    <ul>
                                        <li class="order_subtotal" data-price="{{\App\Helpers::totalCartPrice()}}">Tiền Sản Phẩm<span>{{number_format(\App\Helpers::totalCartPrice(),0)}}đ</span></li>

                                        @if(session('coupon'))
                                            <li class="coupon_price" data-price="{{session('coupon')['value']}}">Bạn tiết kiệm được<span>{{number_format(session('coupon')['value'],0)}}đ</span></li>
                                        @endif
                                        @php
                                            $total_amount=\App\Helpers::totalCartPrice();
                                            if(session('coupon')){
                                                $total_amount=$total_amount-session('coupon')['value'];
                                            }
                                        @endphp
                                        @if(session('coupon'))
                                            <li class="last"  id="order_total_price">Tổng Tiền<span>{{number_format($total_amount,0)}}đ</span></li>
                                        @else
                                            <li class="last"  id="order_total_price">Tổng Tiền<span>{{number_format($total_amount,0)}}đ</span></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <!--/ End Order Widget -->
                            <!-- Order Widget -->
                            <div class="single-widget">
                                <h2>Phương Thức Thanh Toán</h2>
                                <div class="content">
                                    <div class="checkbox">
                                        <div class="form-group">
                                            <input name="payment_method" type="radio" value="cod" checked> <label> Thanh Toán Khi Giao Hàng (COD)</label><br>
                                            <input name="payment_method" type="radio" value="vnpay"> <label> Thanh Toán Qua VNPay</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/ End Order Widget -->
                            <!-- Button Widget -->
                            <div class="single-widget get-button">
                                <div class="content">
                                    <div class="button">
                                        <button type="submit" class="btn">Đặt Hàng</button>
                                    </div>
                                </div>
                            </div>
                            <!--/ End Button Widget -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!--/ End Checkout -->

    <!-- Start Shop Services Area -->
    <section class="shop-services section home">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-rocket"></i>
                        <h4>Miễn Phí Giao Hàng</h4>
                        <p>Cho đơn hàng trên 1.000.000 đ</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-reload"></i>
                        <h4>Miễn Phí Hoàn Trả</h4>
                        <p>Trong vòng 30 ngày</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-lock"></i>
                        <h4>Bảo Mật Thanh Toán</h4>
                        <p>100% Bảo Mật Thanh Toán</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-tag"></i>
                        <h4>Giá Tốt Nhất</h4>
                        <p>Đảm Bảo Giá Tốt Nhất</p>
                    </div>
                    <!-- End Single Service -->
                </div>
            </div>
        </div>
    </section>
    <!-- End Shop Services Area -->

    @include('layouts.chatbot')
@endsection
@push('styles')
    <style>
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
        }
        .form-group input[type="text"],
        .form-group input[type="email"] {
            height: 45px;
        }
        .form-group input[readonly] {
            background-color: #f8f9fa !important;
            cursor: not-allowed !important;
        }
    </style>
@endpush
