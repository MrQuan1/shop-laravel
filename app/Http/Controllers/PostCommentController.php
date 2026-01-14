<?php

namespace App\Http\Controllers;

use App\Models\PostComment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = PostComment::getAllComments();
        return view('backend.comment.index')->with('comments', $comments);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $slug)
    {
        $post = Post::getPostBySlug($slug);
        if (!$post) {
            return redirect()->back()->with('error', 'Bài viết không tồn tại');
        }

        $this->validate($request, [
            'comment' => 'string|required'
        ]);

        $data = $request->all();
        $data['post_id'] = $post->id;
        $data['user_id'] = Auth::user()->id;
        $data['parent_id'] = $request->parent_id;

        $status = PostComment::create($data);
        if ($status) {
            return redirect()->back()->with('success', 'Bình luận thành công');
        } else {
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại');
        }
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = PostComment::findOrFail($id);
        $status = $comment->delete();

        if ($status) {
            request()->session()->flash('success', 'Xóa bình luận thành công');
        } else {
            request()->session()->flash('error', 'Có lỗi xảy ra khi xóa bình luận');
        }
        return redirect()->route('comment.index');
    }
}
