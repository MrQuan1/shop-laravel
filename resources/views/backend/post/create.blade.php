@extends('backend.layouts.master')

@section('main-content')

    <div class="card">
        <h5 class="card-header">Thêm bài viết</h5>
        <div class="card-body">
            <form method="post" action="{{route('post.store')}}">
                {{csrf_field()}}

                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Tiêu đề <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="title" placeholder="Nhập tiêu đề bài viết" value="{{old('title')}}" class="form-control">
                    @error('title')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputSlug" class="col-form-label">Slug</label>
                    <input id="inputSlug" type="text" name="slug" placeholder="Slug sẽ được tạo tự động" value="{{old('slug')}}" class="form-control" readonly>
                    <small class="form-text text-muted">Slug sẽ được tạo tự động từ tiêu đề</small>
                </div>

                <div class="form-group">
                    <label for="quote" class="col-form-label">Trích dẫn</label>
                    <textarea class="form-control" id="quote" name="quote" placeholder="Nhập trích dẫn nếu có">{{old('quote')}}</textarea>
                    @error('quote')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="summary" class="col-form-label">Tóm tắt <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="summary" name="summary" placeholder="Viết tóm tắt ngắn về bài viết">{{old('summary')}}</textarea>
                    @error('summary')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="col-form-label">Mô tả chi tiết</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Viết mô tả chi tiết về bài viết">{{old('description')}}</textarea>
                    @error('description')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="post_cat_id">Danh mục <span class="text-danger">*</span></label>
                    <select name="post_cat_id" class="form-control">
                        <option value="">--Chọn danh mục bài viết--</option>
                        @foreach($categories as $key=>$data)
                            <option value='{{$data->id}}' {{old('post_cat_id') == $data->id ? 'selected' : ''}}>{{$data->title}}</option>
                        @endforeach
                    </select>
                    @error('post_cat_id')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <!-- Ẩn trường tác giả và tự động gán user hiện tại -->
                <input type="hidden" name="added_by" value="{{Auth::user()->id}}">

                <div class="form-group">
                    <label for="inputPhoto" class="col-form-label">Ảnh bài viết <span class="text-danger">*</span></label>
                    <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                  <i class="fa fa-picture-o"></i> Chọn ảnh
                  </a>
              </span>
                        <input id="thumbnail" class="form-control" type="text" name="photo" value="{{old('photo')}}" readonly>
                    </div>
                    <div id="holder" style="margin-top:15px;max-height:200px;"></div>
                    @error('photo')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">Làm lại</button>
                    <button class="btn btn-success" type="submit">Thêm bài viết</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush

@push('scripts')
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="{{asset('backend/summernote/summernote.min.js')}}"></script>

    <script>
        $('#lfm').filemanager('image', {prefix: '/laravel-filemanager'});

        $(document).ready(function() {
            // Tự động tạo slug từ title
            $('#inputTitle').on('keyup', function() {
                var title = $(this).val();
                var slug = title.toLowerCase()
                    .replace(/[^\w ]+/g,'')
                    .replace(/ +/g,'-')
                    .replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a')
                    .replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e')
                    .replace(/ì|í|ị|ỉ|ĩ/g, 'i')
                    .replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o')
                    .replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u')
                    .replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y')
                    .replace(/đ/g, 'd');
                $('#inputSlug').val(slug);
            });

            $('#summary').summernote({
                placeholder: "Viết tóm tắt ngắn về bài viết...",
                tabsize: 2,
                height: 120
            });

            $('#description').summernote({
                placeholder: "Viết mô tả chi tiết về bài viết...",
                tabsize: 2,
                height: 200
            });

            $('#quote').summernote({
                placeholder: "Viết trích dẫn...",
                tabsize: 2,
                height: 100
            });
        });
    </script>
@endpush
