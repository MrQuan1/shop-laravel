@extends('frontend.layouts.master')
@section('title','văn phòng phẩm || Trang Chủ')
@section('main-content')
    <!-- Slider Area -->
    <section id="Gslider" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
                <img class="first-slide" src="{{ asset('/storage/photos/33/Banner/banner1.jpg') }}" alt="Banner 1">
            </div>
            <div class="carousel-item">
                <img class="first-slide" src="{{ asset('/storage/photos/33/Banner/banner2.jpg') }}" alt="Banner 2">
            </div>
            <div class="carousel-item">
                <img class="first-slide" src="{{ asset('/storage/photos/33/Banner/banner3.jpg') }}" alt="Banner 3">
            </div>

        </div>
        <a class="carousel-control-prev" href="#Gslider" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#Gslider" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </section>
    <!--/ End Slider Area -->

    <!-- Start Product Area -->
    <div class="product-area section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h2>Mẫu Thịnh Hành</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="product-info">
                        <div class="nav-main">
                            <!-- Tab Nav -->
                            <ul class="nav nav-tabs filter-tope-group" id="myTab" role="tablist">
                                @php
                                    // Bỏ where('status', 'active') vì categories không còn cột status
                                    $categories = DB::table('categories')->get();
                                    $defaultCategory = $categories->first();
                                @endphp

                                @foreach($categories as $cat)
                                    <button class="btn {{ $loop->first ? 'active-filter-btn' : '' }}" style="background:none; color:black;" data-filter=".cat-{{ $cat->id }}">
                                        {{ $cat->title }}
                                    </button>
                                @endforeach
                            </ul>
                        </div>

                        <div class="tab-content isotope-grid" id="myTabContent">
                            @php
                                // Bỏ where('status', 'active') vì categories không còn cột status
                                $categories = DB::table('categories')->get();
                            @endphp

                            @foreach($categories as $cat)
                                @php
                                    // Bỏ where('status', 'active') vì products không còn cột status
                                    $products = DB::table('products')
                                        ->where('cat_id', $cat->id)
                                        ->where('stock', '>', 0)
                                        ->limit(8)
                                        ->get();
                                @endphp

                                @foreach($products as $product)
                                    <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item cat-{{ $cat->id }}">
                                        <div class="single-product">
                                            <div class="product-img">
                                                <a href="{{ route('product-detail', $product->slug) }}">
                                                    @php
                                                        $photo = explode(',', $product->photo);
                                                    @endphp
                                                    <img class="default-img" src="{{ $photo[0] }}" alt="{{ $photo[0] }}">
                                                    <img class="hover-img" src="{{ $photo[0] }}" alt="{{ $photo[0] }}">

                                                    @if($product->stock <= 0)
                                                        <span class="out-of-stock">Sold out</span>
                                                    @elseif($product->condition == 'new')
                                                        <span class="new">New</span>
                                                    @elseif($product->condition == 'hot')
                                                        <span class="hot">Hot</span>
                                                    @elseif($product->discount > 0)
                                                        <span class="price-dec">{{ $product->discount }}% Off</span>
                                                    @endif
                                                </a>
                                                <div class="button-head">
                                                    <div class="product-action">
                                                        <a data-toggle="modal" data-target="#{{ $product->id }}" title="Quick View" href="#">
                                                            <i class="ti-eye"></i><span>Xem qua sản phẩm</span>
                                                        </a>
                                                        <a title="Wishlist" href="{{ route('add-to-wishlist', $product->slug) }}">
                                                            <i class="ti-heart"></i><span>Thêm vào danh sách yêu thích</span>
                                                        </a>
                                                    </div>
                                                    <div class="product-action-2">
                                                        <a title="Add to cart" href="{{ route('add-to-cart', $product->slug) }}">Thêm vào giỏ hàng</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="product-content">
                                                <h3>
                                                    <a href="{{ route('product-detail', $product->slug) }}">{{ $product->title }}</a>
                                                </h3>
                                                <div class="product-price">
                                                    @php
                                                        $price = $product->price;
                                                        $discount = $product->discount;
                                                        $after_discount = $discount > 0 ? ($price - ($price * $discount / 100)) : $price;
                                                    @endphp

                                                    @if($discount > 0)
                                                        <span>{{ number_format($after_discount, 0) }} đ</span>
                                                        <del style="padding-left:4%;">{{ number_format($price, 0) }} đ</del>
                                                    @else
                                                        <span>{{ number_format($price, 0) }} đ</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auto-click first tab (e.g., "Bút") on page load -->
            <script>
                window.addEventListener('DOMContentLoaded', function () {
                    const firstBtn = document.querySelector('.filter-tope-group .btn');
                    if (firstBtn) {
                        firstBtn.click(); // Tự động chọn danh mục đầu tiên khi load trang
                    }

                    // Re-layout isotope sau khi filter
                    setTimeout(function () {
                        if (typeof $ !== 'undefined' && typeof $('.isotope-grid').isotope === 'function') {
                            $('.isotope-grid').isotope('layout');
                        }
                    }, 500); // Đợi 0.5 giây để DOM ổn định rồi gọi layout
                });
            </script>
        </div>
    </div>
    <!-- End Product Area -->

    <!-- Start Shop Home List  -->
    <section class="shop-home-list section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="section-title" style="margin-bottom: 0px">
                                <h2>Mẫu Mới Nhất</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @php
                            // Bỏ where('status','active') vì products không còn cột status
                            $product_lists=DB::table('products')->where('stock', '>', 0)->orderBy('id','DESC')->limit(6)->get();
                        @endphp
                        @foreach($product_lists as $product)
                            <div class="col-md-4">
                                <!-- Start Single List  -->
                                <div class="single-list">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-12">
                                            <div class="list-image overlay">
                                                @php
                                                    $photo=explode(',',$product->photo);
                                                @endphp
                                                <img src="{{$photo[0]}}" alt="{{$photo[0]}}">
                                                <a href="{{route('add-to-cart',$product->slug)}}" class="buy"><i class="fa fa-shopping-bag"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-12 no-padding">
                                            <div class="content">
                                                <h4 class="title"><a href="#">{{$product->title}}</a></h4>
                                                @if($product->discount > 0)
                                                    <p class="price with-discount">Giảm {{ number_format($product->discount, 0) }}%</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Single List  -->
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Shop Home List  -->

    <!-- Start Shop Blog  -->
    <section class="shop-blog section" style="padding-top: 0px; padding-bottom: 50px">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h2>Bài Viết</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                @if($posts)
                    @foreach($posts as $post)
                        <div class="col-lg-4 col-md-6 col-12">
                            <!-- Start Single Blog  -->
                            <div class="shop-single-blog">
                                <img src="{{$post->photo}}" alt="{{$post->photo}}">
                                <div class="content">
                                    <p class="date">{{$post->created_at->format('d M , Y. D')}}</p>
                                    <a href="{{route('blog.detail',$post->slug)}}" class="title">{{$post->title}}</a>
                                    <a href="{{route('blog.detail',$post->slug)}}" class="more-btn" style="text-decoration: underline">Xem Thêm</a>
                                </div>
                            </div>
                            <!-- End Single Blog  -->
                        </div>
                    @endforeach
                @endif

            </div>
        </div>
    </section>
    <!-- End Shop Blog  -->

    <!-- Start Shop Services Area -->
    <section class="shop-services section home" style="padding-top: 50px;padding-bottom: 50px; background-color: #eaeaea">
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

    @include('frontend.layouts.newsletter')

    <!-- Modal -->
    @if($product_lists)
        @foreach($product_lists as $key=>$product)
            <div class="modal fade" id="{{$product->id}}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="ti-close" aria-hidden="true"></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row no-gutters">
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                    <!-- Product Slider -->
                                    <div class="product-gallery">
                                        <div class="quickview-slider-active">
                                            @php
                                                $photo = $product->photo ? explode(',', $product->photo) : [];
                                            @endphp

                                            @if(count($photo) > 1)
                                                <div class="quickview-slider-active owl-carousel owl-theme">
                                                    @foreach($photo as $data)
                                                        <div class="single-slider">
                                                            <img src="{{ $data }}" alt="product image">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif(count($photo) == 1)
                                                <div class="single-slider">
                                                    <img src="{{ $photo[0] }}" alt="product image">
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                    <!-- End Product slider -->
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                    <div class="quickview-content">
                                        <h2>{{$product->title}}</h2>
                                        <div class="quickview-ratting-review">
                                            <div class="quickview-ratting-wrap">
                                                <div class="quickview-ratting">
                                                    @php
                                                        $rate=DB::table('product_reviews')->where('product_id',$product->id)->avg('rate');
                                                        $rate_count=DB::table('product_reviews')->where('product_id',$product->id)->count();
                                                    @endphp
                                                    @for($i=1; $i<=5; $i++)
                                                        @if($rate>=$i)
                                                            <i class="yellow fa fa-star"></i>
                                                        @else
                                                            <i class="fa fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <a href="#"> ({{$rate_count}} Khách hàng đánh giá)</a>
                                            </div>
                                            <div class="quickview-stock">
                                                @if($product->stock >0)
                                                    <span><i class="fa fa-check-circle-o"></i> {{$product->stock}} sản phẩm trong kho</span>
                                                @else
                                                    <span><i class="fa fa-times-circle-o text-danger"></i> {{$product->stock}} Hết hàng</span>
                                                @endif
                                            </div>
                                        </div>
                                        @php
                                            $price = $product->price;
                                            $discount = $product->discount;
                                            $after_discount = $discount > 0 ? ($price - ($price * $discount / 100)) : $price;
                                        @endphp

                                        <h3>
                                            @if($discount > 0)
                                                <small>
                                                    <del class="text-muted">{{ number_format($price, 0) }} đ</del>
                                                </small>
                                                {{ number_format($after_discount, 0) }} đ
                                            @else
                                                {{ number_format($price, 0) }} đ
                                            @endif
                                        </h3>
                                        <div class="quickview-peragraph">
                                            <p>{!! html_entity_decode($product->summary) !!}</p>
                                        </div>
                                        <form action="{{route('single-add-to-cart')}}" method="POST" class="mt-4">
                                            @csrf
                                            <div class="quantity">
                                                <!-- Input Order -->
                                                <div class="input-group">
                                                    <div class="button minus">
                                                        <button type="button" class="btn btn-primary btn-number" disabled="disabled" data-type="minus" data-field="quant[1]">
                                                            <i class="ti-minus"></i>
                                                        </button>
                                                    </div>
                                                    <input type="hidden" name="slug" value="{{$product->slug}}">
                                                    <input type="text" name="quant[1]" class="input-number"  data-min="1" data-max="1000000" value="1">
                                                    <div class="button plus">
                                                        <button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="quant[1]">
                                                            <i class="ti-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <!--/ End Input Order -->
                                            </div>
                                            <div class="add-to-cart">
                                                <button type="submit" class="btn">Thêm vào giỏ hàng</button>
                                                <a href="{{route('add-to-wishlist',$product->slug)}}" class="btn min"><i class="ti-heart"></i></a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
    <!-- Modal end -->
    @include('layouts.chatbot')
@endsection

@push('styles')
    <style>
        /* Banner Sliding */
        #Gslider .carousel-inner {
            background: #000000;
            color:black;
        }

        #Gslider .carousel-inner{
            height: 550px;
        }
        #Gslider .carousel-inner img{
            width: 100% !important;
            opacity: 1;
        }

        #Gslider .carousel-inner .carousel-caption {
            bottom: 33%;
        }

        #Gslider .carousel-inner .carousel-caption h1 {
            font-size: 45px;
            font-weight: bold;
            line-height: 100%;
            color: #004AAD;
        }

        #Gslider .carousel-inner .carousel-caption p {
            font-size: 18px;
            color: black;
            margin: 20px 0 20px 0;
        }

        #Gslider .carousel-indicators {
            bottom: 70px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>

        /*==================================================================
        [ Isotope ]*/
        var $topeContainer = $('.isotope-grid');
        var $filter = $('.filter-tope-group');

        // filter items on button click
        $filter.each(function () {
            $filter.on('click', 'button', function () {
                var filterValue = $(this).attr('data-filter');
                $topeContainer.isotope({filter: filterValue});
            });

        });

        // init Isotope
        $(window).on('load', function () {
            var $grid = $topeContainer.each(function () {
                $(this).isotope({
                    itemSelector: '.isotope-item',
                    layoutMode: 'fitRows',
                    percentPosition: true,
                    animationEngine : 'best-available',
                    masonry: {
                        columnWidth: '.isotope-item'
                    }
                });
            });
        });

        var isotopeButton = $('.filter-tope-group button');

        $(isotopeButton).each(function(){
            $(this).on('click', function(){
                for(var i=0; i<isotopeButton.length; i++) {
                    $(isotopeButton[i]).removeClass('how-active1');
                }

                $(this).addClass('how-active1');
            });
        });
    </script>
    <script>
        function cancelFullScreen(el) {
            var requestMethod = el.cancelFullScreen||el.webkitCancelFullScreen||el.mozCancelFullScreen||el.exitFullscreen;
            if (requestMethod) { // cancel full screen.
                requestMethod.call(el);
            } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
                var wscript = new ActiveXObject("WScript.Shell");
                if (wscript !== null) {
                    wscript.SendKeys("{F11}");
                }
            }
        }

        function requestFullScreen(el) {
            // Supports most browsers and their versions.
            var requestMethod = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullscreen;

            if (requestMethod) { // Native full screen.
                requestMethod.call(el);
            } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
                var wscript = new ActiveXObject("WScript.Shell");
                if (wscript !== null) {
                    wscript.SendKeys("{F11}");
                }
            }
            return false
        }
    </script>

@endpush
