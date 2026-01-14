@extends('backend.layouts.master')

@section('main-content')

    <div class="card">
        <h5 class="card-header">Chỉnh sửa bài viết</h5>
        <div class="card-body">
            <form method="post" action="{{route('post.update',$post->id)}}">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Tiêu đề <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="title" placeholder="Nhập tiêu đề bài viết" value="{{$post->title}}" class="form-control">
                    @error('title')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputSlug" class="col-form-label">Slug</label>
                    <input id="inputSlug" type="text" name="slug" placeholder="Slug" value="{{$post->slug}}" class="form-control" readonly>
                    <small class="form-text text-muted">Slug sẽ được cập nhật tự động khi thay đổi tiêu đề</small>
                </div>

                <div class="form-group">
                    <label for="quote" class="col-form-label">Trích dẫn</label>
                    <textarea class="form-control" id="quote" name="quote" placeholder="Nhập trích dẫn nếu có">{{$post->quote}}</textarea>
                    @error('quote')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="summary" class="col-form-label">Tóm tắt <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="summary" name="summary" placeholder="Viết tóm tắt ngắn về bài viết">{{$post->summary}}</textarea>
                    @error('summary')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="col-form-label">Mô tả chi tiết</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Viết mô tả chi tiết về bài viết">{{$post->description}}</textarea>
                    @error('description')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="post_cat_id">Danh mục <span class="text-danger">*</span></label>
                    <select name="post_cat_id" class="form-control">
                        <option value="">--Chọn danh mục--</option>
                        @foreach($categories as $key=>$data)
                            <option value='{{$data->id}}' {{(($data->id==$post->post_cat_id)? 'selected' : '')}}>{{$data->title}}</option>
                        @endforeach
                    </select>
                    @error('post_cat_id')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <!-- Ẩn trường tác giả -->
                <input type="hidden" name="added_by" value="{{$post->added_by}}">

                <div class="form-group">
                    <label for="inputPhoto" class="col-form-label">Ảnh bài viết <span class="text-danger">*</span></label>
                    <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                  <i class="fa fa-picture-o"></i> Chọn ảnh
                  </a>
              </span>
                        <input id="thumbnail" class="form-control" type="text" name="photo" value="{{$post->photo}}" readonly>
                    </div>
                    <div id="holder" style="margin-top:15px;max-height:200px;">
                        @if($post->photo)
                            <img src="{{$post->photo}}" style="height:150px; margin:5px;" alt="Post Image">
                        @endif
                    </div>
                    @error('photo')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <button class="btn btn-success" type="submit">Cập nhật bài viết</button>
                    <a href="{{route('post.index')}}" class="btn btn-secondary">Hủy</a>
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
            var originalTitle = $('#inputTitle').val();

            // Tự động tạo slug từ title khi thay đổi
            $('#inputTitle').on('keyup', function() {
                var title = $(this).val();
                if(title !== originalTitle) {
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
                }
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
