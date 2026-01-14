@extends('backend.layouts.master')

@section('main-content')

    <div class="card">
        <h5 class="card-header">Thêm sản phẩm</h5>
        <div class="card-body">
            <form method="post" action="{{route('product.store')}}">
                {{csrf_field()}}

                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Tiêu đề <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="title" placeholder="Viết mô tả chi tiết về sản phẩm..." value="{{old('title')}}" class="form-control">
                    @error('title')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="summary" class="col-form-label">Tóm tắt <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="summary" name="summary" placeholder="Viết tóm tắt ngắn về sản phẩm">{{old('summary')}}</textarea>
                    @error('summary')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="col-form-label">Mô tả chi tiết</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Viết mô tả chi tiết về sản phẩm">{{old('description')}}</textarea>
                    @error('description')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="cat_id">Danh mục <span class="text-danger">*</span></label>
                    <select name="cat_id" id="cat_id" class="form-control">
                        <option value="">--Chọn danh mục--</option>
                        @foreach($categories as $category)
                            <option value='{{$category->id}}' {{old('cat_id') == $category->id ? 'selected' : ''}}>{{$category->title}}</option>
                        @endforeach
                    </select>
                    @error('cat_id')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="brand_id">Thương hiệu</label>
                    <select name="brand_id" class="form-control">
                        <option value="">--Chọn thương hiệu--</option>
                        @foreach($brands as $brand)
                            <option value="{{$brand->id}}" {{old('brand_id') == $brand->id ? 'selected' : ''}}>{{$brand->title}}</option>
                        @endforeach
                    </select>
                    @error('brand_id')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="price" class="col-form-label">Giá <span class="text-danger">*</span></label>
                    <input id="price" type="number" name="price" min="0" step="1000" placeholder="Nhập giá" value="{{old('price')}}" class="form-control">
                    @error('price')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="discount" class="col-form-label">Giảm giá (%)</label>
                    <input id="discount" type="number" name="discount" min="0" max="100" placeholder="0" value="{{old('discount')}}" class="form-control">
                    @error('discount')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="stock">Số lượng <span class="text-danger">*</span></label>
                    <input id="stock" type="number" name="stock" min="0" placeholder="Nhập số lượng" value="{{old('stock')}}" class="form-control">
                    @error('stock')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="condition">Tình trạng <span class="text-danger">*</span></label>
                    <select name="condition" class="form-control">
                        <option value="">--Chọn tình trạng--</option>
                        <option value="default" {{old('condition') == 'default' ? 'selected' : ''}}>Mặc định</option>
                        <option value="new" {{old('condition') == 'new' ? 'selected' : ''}}>Mới</option>
                        <option value="hot" {{old('condition') == 'hot' ? 'selected' : ''}}>Hot</option>
                    </select>
                    @error('condition')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputPhoto" class="col-form-label">Ảnh sản phẩm <span class="text-danger">*</span></label>
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

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name='is_featured' id='is_featured' value='1' class="form-check-input" {{old('is_featured') ? 'checked' : ''}}>
                        <label class="form-check-label" for="is_featured">
                            Sản phẩm nổi bật
                        </label>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">Làm lại</button>
                    <button class="btn btn-success" type="submit">Thêm sản phẩm</button>
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
            $('#summary').summernote({
                placeholder: "Viết tóm tắt ngắn về sản phẩm...",
                tabsize: 2,
                height: 120
            });

            $('#description').summernote({
                placeholder: "Viết mô tả chi tiết về sản phẩm...",
                tabsize: 2,
                height: 200
            });
        });
    </script>
@endpush
