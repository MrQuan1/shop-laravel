<?php
// Controller này chỉ dành cho user thường (khách hàng), không dùng cho admin quản lý user

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductReview;
use App\Models\PostComment;

class UsersController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->user()->id)
            ->with('orderDetails.product')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('user.index')->with('orders', $orders);
    }

    public function orderIndex()
    {
        $orders = Order::where('user_id', auth()->user()->id)
            ->with('orderDetails.product')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('user.order.index')->with('orders', $orders);
    }

    public function orderShow($id)
    {
        $order = Order::where('user_id', auth()->user()->id)
            ->where('id', $id)
            ->with('orderDetails.product')
            ->first();

        if (!$order) {
            return redirect()->route('user.order.index')->with('error', 'Không tìm thấy đơn hàng!');
        }

        return view('user.order.show')->with('order', $order);
    }

    public function cancelOrder(Request $request, $id)
    {
        $order = Order::where('user_id', auth()->user()->id)
            ->where('id', $id)
            ->where('status', 'new')
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng này!');
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái đơn hàng
            $order->status = 'cancel';
            $order->save();

            DB::commit();
            return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage());
        }
    }

    public function profile()
    {
        $user = auth()->user();
        return view('user.profile')->with('user', $user);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;

            // Xử lý upload ảnh
            if ($request->hasFile('photo')) {
                // Xóa ảnh cũ nếu có
                if ($user->photo && file_exists(public_path($user->photo))) {
                    unlink(public_path($user->photo));
                }

                $file = $request->file('photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('backend/img/users'), $filename);
                $user->photo = 'backend/img/users/' . $filename;
            }

            $user->save();

            DB::commit();
            return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Product Review
    public function productReviewIndex(){
        $reviews=ProductReview::getAllUserReview();
        return view('user.review.index')->with('reviews',$reviews);
    }
    public function productReviewEdit($id)
    {
        $review=ProductReview::find($id);
        return view('user.review.edit')->with('review',$review);
    }
    public function productReviewUpdate(Request $request, $id)
    {
        $review=ProductReview::find($id);
        if($review){
            $data=$request->all();
            $status=$review->fill($data)->update();
            if($status){
                request()->session()->flash('success','Cập nhật đánh giá thành công');
            }
            else{
                request()->session()->flash('error','Có lỗi xảy ra! Vui lòng thử lại!!');
            }
        }
        else{
            request()->session()->flash('error','Đánh giá không tồn tại!!');
        }
        return redirect()->route('user.productreview.index');
    }
    public function productReviewDelete($id)
    {
        $review=ProductReview::find($id);
        $status=$review->delete();
        if($status){
            request()->session()->flash('success','Xóa đánh giá thành công');
        }
        else{
            request()->session()->flash('error','Có lỗi xảy ra! Vui lòng thử lại');
        }
        return redirect()->route('user.productreview.index');
    }
    // Post Comment
    public function userComment()
    {
        $comments=PostComment::with(['user_info', 'post'])->where('user_id', auth()->id())->orderBy('id', 'DESC')->paginate(10);
        return view('user.comment.index')->with('comments',$comments);
    }
    public function userCommentDelete($id){
        $comment=PostComment::find($id);
        if($comment){
            $status=$comment->delete();
            if($status){
                request()->session()->flash('success','Bình luận bài viết xóa thành công');
            }
            else{
                request()->session()->flash('error','Có lỗi xảy ra, vui lòng thử lại');
            }
            return back();
        }
        else{
            request()->session()->flash('error','Bình luận bài viết không tồn tại');
            return redirect()->back();
        }
    }
    public function userCommentEdit($id)
    {
        $comments=PostComment::find($id);
        if($comments){
            return view('user.comment.edit')->with('comment',$comments);
        }
        else{
            request()->session()->flash('error','Bình luận không tồn tại');
            return redirect()->back();
        }
    }
    public function userCommentUpdate(Request $request, $id)
    {
        $comment=PostComment::find($id);
        if($comment){
            $data=$request->all();
            $status=$comment->fill($data)->update();
            if($status){
                request()->session()->flash('success','Cập nhật bình luận thành công');
            }
            else{
                request()->session()->flash('error','Có lỗi xảy ra! Vui lòng thử lại!!');
            }
            return redirect()->route('user.post-comment.index');
        }
        else{
            request()->session()->flash('error','Bình luận không tồn tại');
            return redirect()->back();
        }
    }

    public function changPasswordStore(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        $current_password = auth()->user()->password;

        if(\Hash::check($request->current_password, $current_password)){
            $user_id = auth()->user()->id;
            $obj_user = \App\User::find($user_id);
            $obj_user->password = \Hash::make($request->new_password);
            $obj_user->save();
            if(auth()->user()->role == 'admin'){
                return redirect()->route('admin')->with('success','Đổi mật khẩu thành công!');
            } else {
                return redirect()->route('user')->with('success','Đổi mật khẩu thành công!');
            }
        }
        else{
            return redirect()->back()->with('error','Mật khẩu hiện tại không đúng!');
        }
    }
}
