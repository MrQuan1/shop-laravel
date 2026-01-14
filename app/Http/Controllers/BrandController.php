<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('backend.brand.index')->with('brands', $brands);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'string|required',
            'slug' => 'string|nullable',
        ]);

        $data = $request->all();
        $slug = Str::slug($request->title);
        $count = Brand::where('slug', $slug)->count();
        if($count > 0){
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $data['slug'] = $slug;

        $status = Brand::create($data);
        if($status){
            request()->session()->flash('success', 'Thêm thương hiệu thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi xảy ra!');
        }
        return redirect()->route('brand.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('backend.brand.edit')->with('brand', $brand);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        $this->validate($request, [
            'title' => 'string|required',
            'slug' => 'string|nullable',
        ]);

        $data = $request->all();

        // Tạo slug mới nếu title thay đổi
        if($request->title != $brand->title) {
            $slug = Str::slug($request->title);
            $count = Brand::where('slug', $slug)->where('id', '!=', $id)->count();
            if($count > 0){
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }
            $data['slug'] = $slug;
        }

        $status = $brand->fill($data)->save();
        if($status){
            request()->session()->flash('success', 'Cập nhật thương hiệu thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi xảy ra!');
        }
        return redirect()->route('brand.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        // Kiểm tra xem có sản phẩm nào đang sử dụng brand này không
        $products_count = $brand->products()->count();
        if($products_count > 0){
            request()->session()->flash('error', 'Không thể xóa thương hiệu này vì có ' . $products_count . ' sản phẩm đang sử dụng!');
            return redirect()->back();
        }

        $status = $brand->delete();

        if($status){
            request()->session()->flash('success', 'Xóa thương hiệu thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi trong quá trình xóa thương hiệu');
        }
        return redirect()->route('brand.index');
    }
}
