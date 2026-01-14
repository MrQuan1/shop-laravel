<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['cat_info', 'brand'])->orderBy('id', 'desc')->paginate(10);
        return view('backend.product.index')->with('products', $products);
    }

    public function create()
    {
        $brands = Brand::orderBy('title', 'ASC')->get();
        $categories = Category::orderBy('title', 'ASC')->get();
        return view('backend.product.create')
            ->with('categories', $categories)
            ->with('brands', $brands);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title'       => 'string|required',
            'summary'     => 'string|required',
            'description' => 'string|nullable',
            'photo'       => 'string|required',
            'stock'       => 'required|numeric|min:0',
            'cat_id'      => 'required|exists:categories,id',
            'brand_id'    => 'nullable|exists:brands,id',
            'is_featured' => 'sometimes|in:1',
            'condition'   => 'required|in:default,new,hot',
            'price'       => 'required|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0|max:100'
        ]);

        $data = $request->all();

        // Tạo slug từ title
        $slug = Str::slug($request->title);
        $count = Product::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $data['slug'] = $slug;

        // Xử lý is_featured
        $data['is_featured'] = $request->has('is_featured') ? 1 : 0;

        $status = Product::create($data);

        if ($status) {
            request()->session()->flash('success', 'Thêm sản phẩm thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
        }

        return redirect()->route('product.index');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('backend.product.show')->with('product', $product);
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $brands = Brand::orderBy('title', 'ASC')->get();
        $categories = Category::orderBy('title', 'ASC')->get();

        return view('backend.product.edit')
            ->with('product', $product)
            ->with('brands', $brands)
            ->with('categories', $categories);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $this->validate($request, [
            'title'       => 'string|required',
            'summary'     => 'string|required',
            'description' => 'string|nullable',
            'photo'       => 'string|required',
            'stock'       => 'required|numeric|min:0',
            'cat_id'      => 'required|exists:categories,id',
            'brand_id'    => 'nullable|exists:brands,id',
            'is_featured' => 'sometimes|in:1',
            'condition'   => 'required|in:default,new,hot',
            'price'       => 'required|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0|max:100'
        ]);

        $data = $request->all();

        // Cập nhật slug nếu title thay đổi
        if ($request->title != $product->title) {
            $slug = Str::slug($request->title);
            $count = Product::where('slug', $slug)->where('id', '!=', $id)->count();
            if ($count > 0) {
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }
            $data['slug'] = $slug;
        }

        // Xử lý is_featured
        $data['is_featured'] = $request->has('is_featured') ? 1 : 0;

        $status = $product->fill($data)->save();

        if ($status) {
            request()->session()->flash('success', 'Cập nhật sản phẩm thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
        }

        return redirect()->route('product.index');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Kiểm tra xem sản phẩm có trong giỏ hàng hoặc đơn hàng không
        $cart_count = $product->carts()->count();
        if ($cart_count > 0) {
            request()->session()->flash('error', 'Không thể xóa sản phẩm này vì đã có trong đơn hàng!');
            return redirect()->back();
        }

        $status = $product->delete();

        if ($status) {
            request()->session()->flash('success', 'Xóa sản phẩm thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi trong quá trình xóa sản phẩm');
        }

        return redirect()->route('product.index');
    }
}
