@extends('frontend.layouts.master')

@section('title','văn phòng phẩm
 || Giới Thiệu')

@section('main-content')

	<!-- Breadcrumbs -->
	<div class="breadcrumbs">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="bread-inner">
						<ul class="bread-list">
							<li><a href="index1.html">Trang Chủ<i class="ti-arrow-right"></i></a></li>
							<li class="active"><a href="blog-single.html">Giới Thiệu</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<!-- About Us -->
	<section class="about-us section">
			<div class="container">
				<div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="about-content">
                            <h3>Văn phòng phẩm <span>Sài Đồng</span></h3>
                            <p>Chào mừng bạn đến với Văn phòng phẩm Sài Đồng – nơi cung cấp đầy đủ các sản phẩm văn phòng, học tập và dụng cụ sáng tạo với chất lượng hàng đầu. Chúng tôi tự hào là người bạn đồng hành tin cậy của học sinh, sinh viên, giáo viên và các doanh nghiệp trên hành trình phát triển tri thức và hiệu suất làm việc.</p>
                            <div class="button">
                                <a href="{{ route('blog') }}" class="btn">Bài Viết</a>
                                <a href="{{ route('contact') }}" class="btn primary">Liên Hệ</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-12">
                        <div class="about-img overlay">

                            <img src="{{ asset('/storage/photos/33/Logo/văn phòng phẩm.png') }}" alt="#">
                        </div>
                    </div>
				</div>
			</div>
	</section>
	<!-- End About Us -->


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

	@include('frontend.layouts.newsletter')
	@include('layouts.chatbot')
@endsection
