<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostCategory;
use App\User;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['cat_info'])->orderBy('id', 'DESC')->paginate(10);
        return view('backend.post.index')->with('posts', $posts);
    }

    public function create()
    {
        $categories = PostCategory::getAllPostCategory();
        $users = User::get();
        return view('backend.post.create')
            ->with('categories', $categories)
            ->with('users', $users);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'string|required',
            'quote' => 'string|nullable',
            'summary' => 'string|required',
            'description' => 'string|nullable',
            'photo' => 'string|required',
            'post_cat_id' => 'required|exists:post_categories,id',
            'added_by' => 'required|exists:users,id',
        ]);

        $data = $request->all();

        // Tạo slug từ title
        $slug = Str::slug($request->title);
        $count = Post::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $data['slug'] = $slug;

        $status = Post::create($data);
        if ($status) {
            request()->session()->flash('success', 'Thêm bài viết thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi xảy ra!');
        }
        return redirect()->route('post.index');
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return view('backend.post.show')->with('post', $post);
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $categories = PostCategory::getAllPostCategory();
        $users = User::get();

        return view('backend.post.edit')
            ->with('post', $post)
            ->with('categories', $categories)
            ->with('users', $users);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $this->validate($request, [
            'title' => 'string|required',
            'quote' => 'string|nullable',
            'summary' => 'string|required',
            'description' => 'string|nullable',
            'photo' => 'string|required',
            'post_cat_id' => 'required|exists:post_categories,id',
            'added_by' => 'required|exists:users,id',
        ]);

        $data = $request->all();

        // Cập nhật slug nếu title thay đổi
        if ($request->title != $post->title) {
            $slug = Str::slug($request->title);
            $count = Post::where('slug', $slug)->where('id', '!=', $id)->count();
            if ($count > 0) {
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }
            $data['slug'] = $slug;
        }

        $status = $post->fill($data)->save();
        if ($status) {
            request()->session()->flash('success', 'Cập nhật bài viết thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi xảy ra!');
        }
        return redirect()->route('post.index');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $status = $post->delete();

        if ($status) {
            request()->session()->flash('success', 'Xóa bài viết thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi xảy ra!');
        }
        return redirect()->route('post.index');
    }
}
