@extends('backend.layouts.master')

@section('main-content')

    <div class="card">
        <h5 class="card-header">Thêm danh mục sản phẩm</h5>
        <div class="card-body">
            <form method="post" action="{{route('category.store')}}">
                {{csrf_field()}}
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Tiêu đề <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="title" placeholder="Nhập tiêu đề"  value="{{old('title')}}" class="form-control">
                    @error('title')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputSlug" class="col-form-label">Slug</label>
                    <input id="inputSlug" type="text" name="slug" placeholder="Slug sẽ được tạo tự động" value="{{old('slug')}}" class="form-control" readonly>
                    @error('slug')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                    <small class="form-text text-muted">Slug sẽ được tạo tự động từ tiêu đề</small>
                </div>

                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">Làm lại</button>
                    <button class="btn btn-success" type="submit">Thêm danh mục</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Hàm chuyển đổi tiếng Việt có dấu thành không dấu
            function removeVietnameseTones(str) {
                str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
                str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
                str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
                str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
                str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
                str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
                str = str.replace(/đ/g, "d");
                str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
                str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
                str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
                str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
                str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
                str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
                str = str.replace(/Đ/g, "D");
                return str;
            }

            // Tự động tạo slug từ title
            $('#inputTitle').on('keyup', function() {
                var title = $(this).val();
                var slug = removeVietnameseTones(title)
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '') // Chỉ giữ lại chữ cái, số, khoảng trắng và dấu gạch ngang
                    .replace(/\s+/g, '-') // Thay khoảng trắng bằng dấu gạch ngang
                    .replace(/-+/g, '-') // Loại bỏ nhiều dấu gạch ngang liên tiếp
                    .replace(/^-|-$/g, ''); // Loại bỏ dấu gạch ngang ở đầu và cuối
                $('#inputSlug').val(slug);
            });
        });
    </script>
@endpush
