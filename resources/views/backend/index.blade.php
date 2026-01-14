@extends('backend.layouts.master')
@section('title','văn phòng phẩm || DASHBOARD')
@section('main-content')
    <div class="container-fluid">
        @include('backend.layouts.notification')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Bảng Thống Kê</h1>
        </div>

        <!-- Content Row -->
        <div class="row">
            <!-- Danh mục -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Danh mục</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{$category_count}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-sitemap fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sản phẩm -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sản phẩm</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{$product_count}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-cubes fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Đơn hàng -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Đơn hàng</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{$order_count}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Người dùng -->
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ route('admin.users.index') }}" style="text-decoration:none">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Người dùng</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{$user_count}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
                </a>
            </div>
            <!-- Thương hiệu -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Thương hiệu</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{$brand_count}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tags fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bài viết -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Bài viết</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{$post_count}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-blog fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Danh mục bài viết -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Danh mục bài viết</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{$post_category_count}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-folder fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tổng doanh thu -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng doanh thu</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{number_format(\App\Models\Order::where('payment_status', 'paid')->where('status', 'delivered')->sum('total_amount'), 0)}}đ</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <!-- Area Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <!-- Card Header -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Tổng quan doanh thu</h6>
                        <div class="dropdown no-arrow">
                            <select id="yearFilter" class="form-control" style="width: auto;">
                                @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{$i}}" {{$i == date('Y') ? 'selected' : ''}}>{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="myAreaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <!-- Card Header -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Đơn hàng gần đây</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        @foreach($recent_orders as $order)
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-shopping-cart text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-weight-bold">Đơn hàng #{{$order->order_number}}</div>
                                    <div class="text-muted small">{{$order->name ?? 'Khách'}} - {{number_format($order->total_amount, 0)}}đ</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- New Statistics Row -->
        <div class="row">
            <!-- Best Selling Products -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Sản phẩm bán chạy</h6>
                        <div class="d-flex">
                            <select id="productMonth" class="form-control mr-2" style="width: auto;">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{$i}}" {{$i == date('m') ? 'selected' : ''}}>Tháng {{$i}}</option>
                                @endfor
                            </select>
                            <select id="productYear" class="form-control" style="width: auto;">
                                @for($i = date('Y'); $i >= date('Y') - 3; $i--)
                                    <option value="{{$i}}" {{$i == date('Y') ? 'selected' : ''}}>{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="bestSellingProducts">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin"></i> Đang tải...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue Chart -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Doanh thu theo ngày</h6>
                        <div class="d-flex">
                            <select id="revenueMonth" class="form-control mr-2" style="width: auto;">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{$i}}" {{$i == date('m') ? 'selected' : ''}}>Tháng {{$i}}</option>
                                @endfor
                            </select>
                            <select id="revenueYear" class="form-control" style="width: auto;">
                                @for($i = date('Y'); $i >= date('Y') - 3; $i--)
                                    <option value="{{$i}}" {{$i == date('Y') ? 'selected' : ''}}>{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="monthlyRevenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .chart-area {
            position: relative;
            height: 320px;
            width: 100%;
        }
        .icon-circle {
            height: 2.5rem;
            width: 2.5rem;
            border-radius: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .product-item:last-child {
            border-bottom: none;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
        }
        .product-info {
            flex: 1;
        }
        .product-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .product-stats {
            font-size: 12px;
            color: #666;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize charts
            var ctx = document.getElementById("myAreaChart");
            var monthlyCtx = document.getElementById("monthlyRevenueChart");
            var myLineChart;
            var monthlyChart;

            function loadChart(year = new Date().getFullYear()) {
                $.get('{{route("admin.income.chart")}}', {year: year}, function(data) {
                    var chartData = Object.values(data);

                    if (myLineChart) {
                        myLineChart.destroy();
                    }

                    myLineChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
                            datasets: [{
                                label: "Doanh thu",
                                lineTension: 0.3,
                                backgroundColor: "rgba(78, 115, 223, 0.05)",
                                borderColor: "rgba(78, 115, 223, 1)",
                                pointRadius: 3,
                                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                                pointBorderColor: "rgba(78, 115, 223, 1)",
                                pointHoverRadius: 3,
                                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                                pointHitRadius: 10,
                                pointBorderWidth: 2,
                                data: chartData,
                            }],
                        },
                        options: {
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    left: 10,
                                    right: 25,
                                    top: 25,
                                    bottom: 0
                                }
                            },
                            scales: {
                                x: {
                                    gridLines: {
                                        display: false,
                                        drawBorder: false
                                    },
                                    ticks: {
                                        maxTicksLimit: 7
                                    }
                                },
                                y: {
                                    ticks: {
                                        maxTicksLimit: 5,
                                        padding: 10,
                                        callback: function(value, index, values) {
                                            return number_format(value) + 'đ';
                                        }
                                    },
                                    gridLines: {
                                        color: "rgb(234, 236, 244)",
                                        zeroLineColor: "rgb(234, 236, 244)",
                                        drawBorder: false,
                                        borderDash: [2],
                                        zeroLineBorderDash: [2]
                                    }
                                },
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                });
            }

            function loadBestSellingProducts() {
                var month = $('#productMonth').val();
                var year = $('#productYear').val();

                $.get('{{route("admin.best-selling-products")}}', {month: month, year: year}, function(data) {
                    var html = '';
                    if (data.length > 0) {
                        data.forEach(function(product, index) {
                            var photo = product.photo ? product.photo.split(',')[0].trim() : '';
                            var photoUrl = photo
                                ? (photo.startsWith('http') ? photo : '{{asset('')}}' + photo)
                                : '{{asset('backend/img/no-image.jpg')}}';
                            html += '<div class="product-item">';
                            html += '<img src="' + photoUrl + '" alt="' + product.title + '" class="product-image">';
                            html += '<div class="product-info">';
                            html += '<div class="product-name">' + (index + 1) + '. ' + product.title + '</div>';
                            html += '<div class="product-stats">Đã bán: ' + product.total_sold + ' | Tồn kho: ' + product.stock + ' | Doanh thu: ' + number_format(product.total_revenue) + 'đ</div>';
                            html += '</div>';
                            html += '</div>';
                        });
                    } else {
                        html = '<div class="text-center text-muted">Không có dữ liệu</div>';
                    }
                    $('#bestSellingProducts').html(html);
                });
            }

            function loadMonthlyRevenue() {
                var month = $('#revenueMonth').val();
                var year = $('#revenueYear').val();

                $.get('{{route("admin.monthly-revenue")}}', {month: month, year: year}, function(data) {
                    var labels = [];
                    var chartData = [];
                    var daysInMonth = new Date(year, month, 0).getDate();
                    for (var i = 1; i <= daysInMonth; i++) {
                        labels.push('Ngày ' + i);
                        chartData.push(data[i] ? data[i] : 0);
                    }

                    if (monthlyChart) {
                        monthlyChart.destroy();
                    }

                    monthlyChart = new Chart(monthlyCtx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: "Doanh thu",
                                backgroundColor: "rgba(54, 162, 235, 0.8)",
                                borderColor: "rgba(54, 162, 235, 1)",
                                borderWidth: 1,
                                data: chartData,
                            }],
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value, index, values) {
                                            return number_format(value) + 'đ';
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                });
            }

            // Load initial data
            loadChart();
            loadBestSellingProducts();
            loadMonthlyRevenue();

            // Event listeners
            $('#yearFilter').change(function() {
                loadChart($(this).val());
            });

            $('#productMonth, #productYear').change(function() {
                loadBestSellingProducts();
            });

            $('#revenueMonth, #revenueYear').change(function() {
                loadMonthlyRevenue();
            });

            // Number formatting function
            function number_format(number, decimals, dec_point, thousands_sep) {
                number = (number + '').replace(',', '').replace(' ', '');
                var n = !isFinite(+number) ? 0 : +number,
                    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                    s = '',
                    toFixedFix = function(n, prec) {
                        var k = Math.pow(10, prec);
                        return '' + Math.round(n * k) / k;
                    };
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                }
                if ((s[1] || '').length < prec) {
                    s[1] = s[1] || '';
                    s[1] += new Array(prec - s[1].length + 1).join('0');
                }
                return s.join(dec);
            }
        });
    </script>
@endpush
