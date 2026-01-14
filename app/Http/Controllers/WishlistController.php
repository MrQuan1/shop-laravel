<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Auth;

class WishlistController extends Controller
{
    protected $product = null;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    // Thêm sản phẩm vào wishlist
    public function wishlist(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Bạn cần đăng nhập để sử dụng chức năng này!');
        }
        if (empty($request->slug)) {
            return back()->with('error', 'Sản phẩm không hợp lệ');
        }
        $product = Product::where('slug', $request->slug)->first();
        if (empty($product)) {
            return back()->with('error', 'Sản phẩm không hợp lệ');
        }

        // Bỏ cart_id, chỉ kiểm tra user_id và product_id
        $already_wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();
        if ($already_wishlist) {
            return back()->with('error', 'Bạn đã thêm sản phẩm này vào danh sách yêu thích rồi!');
        }

        // Tính giá sau discount
        $price = $product->price;
        $discount = $product->discount ?? 0;
        $final_price = $price - ($price * $discount / 100);

        if ($product->stock < 1) {
            return back()->with('error', 'Hàng không đủ!');
        }

        $wishlist = new Wishlist;
        $wishlist->user_id = auth()->id();
        $wishlist->product_id = $product->id;
        $wishlist->price = $final_price;
        $wishlist->save();

        return back()->with('success', 'Sản phẩm đã thêm vào danh sách yêu thích');
    }

    // Xóa sản phẩm khỏi wishlist
    public function wishlistDelete($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Bạn cần đăng nhập để sử dụng chức năng này!');
        }
        $wishlist = Wishlist::find($id);
        if ($wishlist && $wishlist->user_id == auth()->id()) {
            $wishlist->delete();
            return back()->with('success', 'Xóa sản phẩm yêu thích thành công');
        }
        return back()->with('error', 'Có lỗi, vui lòng thử lại');
    }
}
