<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('backend.category.index')->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.category.create');
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
        $count = Category::where('slug', $slug)->count();
        if($count > 0){
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $data['slug'] = $slug;

        $status = Category::create($data);
        if($status){
            request()->session()->flash('success', 'Thêm danh mục thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi xảy ra!');
        }
        return redirect()->route('category.index');
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
        $category = Category::findOrFail($id);
        return view('backend.category.edit')->with('category', $category);
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
        $category = Category::findOrFail($id);
        $this->validate($request, [
            'title' => 'string|required',
            'slug' => 'string|nullable',
        ]);

        $data = $request->all();

        // Tạo slug mới nếu title thay đổi
        if($request->title != $category->title) {
            $slug = Str::slug($request->title);
            $count = Category::where('slug', $slug)->where('id', '!=', $id)->count();
            if($count > 0){
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }
            $data['slug'] = $slug;
        }

        $status = $category->fill($data)->save();
        if($status){
            request()->session()->flash('success', 'Cập nhật danh mục thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi xảy ra!');
        }
        return redirect()->route('category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Kiểm tra xem có sản phẩm nào đang sử dụng category này không
        $products_count = $category->products()->count();
        if($products_count > 0){
            request()->session()->flash('error', 'Không thể xóa danh mục này vì có ' . $products_count . ' sản phẩm đang sử dụng!');
            return redirect()->back();
        }

        $status = $category->delete();

        if($status){
            request()->session()->flash('success', 'Xóa danh mục thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi xảy ra!');
        }
        return redirect()->route('category.index');
    }
}
