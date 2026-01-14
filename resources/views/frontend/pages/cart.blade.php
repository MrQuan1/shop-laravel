@extends('frontend.layouts.master')
@section('title','Cart Page')
@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Trang Chủ<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="">Giỏ Hàng</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Shopping Cart -->
    <div class="shopping-cart section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <!-- Shopping Summery -->
                    <table class="table shopping-summery">
                        <thead>
                        <tr class="main-hading">
                            <th>Ảnh Sản Phẩm</th>
                            <th>Tên Sản Phẩm</th>
                            <th class="text-center">Đơn Giá</th>
                            <th class="text-center">Số Lượng</th>
                            <th class="text-center">Tổng Tiền</th>
                            <th class="text-center"><i class="ti-trash remove-icon"></i></th>
                        </tr>
                        </thead>
                        <tbody id="cart_item_list">
                        <form action="{{route('cart.update')}}" method="POST">
                            @csrf
                            @if(\App\Helpers::getAllProductFromCart())
                                @foreach(\App\Helpers::getAllProductFromCart() as $key=>$cartDetail)
                                    <tr>
                                        @php
                                            $photo=explode(',',$cartDetail->product['photo']);
                                        @endphp
                                        <td class="image" data-title="No"><img src="{{$photo[0]}}" alt="{{$photo[0]}}"></td>
                                        <td class="product-des" data-title="Description">
                                            <p class="product-name"><a href="{{route('product-detail',$cartDetail->product['slug'])}}" target="_blank">{{$cartDetail->product['title']}}</a></p>
                                            <p class="product-des">{!!($cartDetail->product['summary']) !!}</p>
                                        </td>
                                        <td class="price" data-title="Price">
                                            <span>{{number_format($cartDetail['price'],0)}}đ</span>
                                        </td>
                                        <td class="qty" data-title="Qty">
                                            <div class="input-group">
                                                <div class="button minus">
                                                    <button type="button" class="btn btn-primary btn-number" data-type="minus" data-field="quant[{{$key}}]">
                                                        <i class="ti-minus"></i>
                                                    </button>
                                                </div>
                                                <input type="text" name="quant[{{$key}}]" class="input-number"  data-min="1" data-max="1000000" value="{{$cartDetail->quantity}}">
                                                <input type="hidden" name="qty_id[]" value="{{$cartDetail->id}}">
                                                <div class="button plus">
                                                    <button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="quant[{{$key}}]">
                                                        <i class="ti-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="total-amount cart_single_price" data-title="Total">
                                            <span class="money">{{number_format($cartDetail->price * $cartDetail->quantity,0)}}đ</span>
                                        </td>
                                        <td class="action" data-title="Remove">
                                            <a href="{{route('cart-delete',$cartDetail->id)}}"><i class="ti-trash remove-icon"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                <track>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="float-right">
                                    <button class="btn float-right" type="submit">Cập Nhật</button>
                                </td>
                                </track>
                            @else
                                <tr>
                                    <td class="text-center" colspan="6">
                                        Không có giỏ hàng nào có sẵn. <a href="{{route('product-grids')}}" style="color:blue;">Tiếp tục mua sắm.</a>
                                    </td>
                                </tr>
                            @endif
                        </form>
                        </tbody>
                    </table>
                    <!--/ End Shopping Summery -->
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <!-- Total Amount -->
                    <div class="total-amount">
                        <div class="row">
                            <div class="col-lg-8 col-md-5 col-12">
                                <div class="left">
                                    <div class="coupon">
{{--                                        <form action="{{route('coupon-store')}}" method="POST">--}}
{{--                                            @csrf--}}
{{--                                            <input name="code" placeholder="Nhập mã giảm giá">--}}
{{--                                            <button class="btn">Áp Dụng</button>--}}
{{--                                        </form>--}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-7 col-12">
                                <div class="right">
                                    <ul>
                                        <li class="order_subtotal" data-price="{{\App\Helpers::totalCartPrice()}}">Tổng Số Tiền <span>{{number_format(\App\Helpers::totalCartPrice(),0)}}đ</span></li>

                                        @if(session()->has('coupon'))
                                            <li class="coupon_price" data-price="{{Session::get('coupon')['value']}}">Bạn tiết kiệm được<span>{{number_format(Session::get('coupon')['value'],0)}}đ</span></li>
                                        @endif
                                        @php
                                            $total_amount=\App\Helpers::totalCartPrice();
                                            if(session()->has('coupon')){
                                                $total_amount=$total_amount-Session::get('coupon')['value'];
                                            }
                                        @endphp
                                        @if(session()->has('coupon'))
                                            <li class="last" id="order_total_price">Bạn cần thanh toán<span>{{number_format($total_amount,0)}}đ</span></li>
                                        @else
                                            <li class="last" id="order_total_price">Bạn cần thanh toán<span>{{number_format($total_amount,0)}}đ</span></li>
                                        @endif
                                    </ul>
                                    <div class="button5">
                                        <a href="{{route('checkout')}}" class="btn">Thanh Toán</a>
                                        <a href="{{route('product-grids')}}" class="btn">Tiếp Tục Mua Sắm</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ End Total Amount -->
                </div>
            </div>
        </div>
    </div>
    <!--/ End Shopping Cart -->

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

    <!-- Start Shop Newsletter  -->
    @include('frontend.layouts.newsletter')
    <!-- End Shop Newsletter -->

    @include('layouts.chatbot')

@endsection
@push('styles')
    <style>
        li.shipping{
            display: inline-flex;
            width: 100%;
            font-size: 14px;
        }
        li.shipping .input-group-icon {
            width: 100%;
            margin-left: 10px;
        }
        .input-group-icon .icon {
            position: absolute;
            left: 20px;
            top: 0;
            line-height: 40px;
            z-index: 3;
        }
        .form-select {
            height: 30px;
            width: 100%;
        }
        .form-select .nice-select {
            border: none;
            border-radius: 0px;
            height: 40px;
            background: #f6f6f6 !important;
            padding-left: 45px;
            padding-right: 40px;
            width: 100%;
        }
        .list li{
            margin-bottom:0 !important;
        }
        .list li:hover{
            background:#004AAD !important;
            color:white !important;
        }
        .form-select .nice-select::after {
            top: 14px;
        }
    </style>
@endpush
@push('scripts')
    <script src="{{asset('frontend/js/nice-select/js/jquery.nice-select.min.js')}}"></script>
    <script src="{{ asset('frontend/js/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() { $("select.select2").select2(); });
        $('select.nice-select').niceSelect();
    </script>
    <script>
        $(document).ready(function(){
            $('.shipping select[name=shipping]').change(function(){
                let cost = parseFloat( $(this).find('option:selected').data('price') ) || 0;
                let subtotal = parseFloat( $('.order_subtotal').data('price') );
                let coupon = parseFloat( $('.coupon_price').data('price') ) || 0;
                // alert(coupon);
                $('#order_total_price span').text('$'+(subtotal + cost-coupon).toFixed(2));
            });

        });

    </script>

@endpush
