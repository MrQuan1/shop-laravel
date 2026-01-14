<?php

namespace App;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Cart;
use App\Models\CartDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Helpers {
    public static function getAllCategory(){
        return Category::orderBy('title', 'ASC')->get();
    }

    // Cập nhật getHeaderCategory - bỏ phân cấp
    public static function getHeaderCategory(){
        $categories = Category::orderBy('title', 'ASC')->get();

        if($categories->count() > 0){
            ?>
            <li>
                <a href="javascript:void(0);">Danh Mục<i class="ti-angle-down"></i></a>
                <ul class="dropdown border-0 shadow">
                    <?php
                    foreach($categories as $cat_info){
                        ?>
                        <li><a href="<?php echo route('product-cat',$cat_info->slug);?>"><?php echo $cat_info->title; ?></a></li>
                        <?php
                    }
                    ?>
                </ul>
            </li>
            <?php
        }
    }

    // Cart Count - Updated for new structure
    public static function cartCount($user_id=''){
        if(Auth::check()){
            if($user_id=="") $user_id=auth()->user()->id;
            $cart = Cart::where('user_id', $user_id)->first();
            if($cart){
                return CartDetail::where('cart_id', $cart->id)->sum('quantity');
            } else {
                return 0;
            }
        }
        else{
            return 0;
        }
    }

    // Get all products from cart - Updated for new structure
    public static function getAllProductFromCart($user_id=''){
        if(Auth::check()){
            if($user_id=="") $user_id=auth()->user()->id;
            $cart = Cart::where('user_id', $user_id)->first();
            if($cart){
                return CartDetail::with('product')
                    ->where('cart_id', $cart->id)
                    ->get();
            } else {
                return collect();
            }
        }
        else{
            return collect();
        }
    }

    // Total cart price - Updated for new structure
    public static function totalCartPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == '') $user_id = auth()->user()->id;

            $cart = Cart::where('user_id', $user_id)->first();
            if(!$cart) return 0;

            $cartDetails = CartDetail::where('cart_id', $cart->id)->get();

            $total = 0;
            foreach ($cartDetails as $cartDetail) {
                $total += $cartDetail->price * $cartDetail->quantity;
            }

            return $total;
        }

        return 0;
    }

    public static function wishlistCount($user_id=''){
        if(Auth::check()){
            if($user_id=="") $user_id=auth()->user()->id;
            return Wishlist::where('user_id',$user_id)->count();
        }
        else{
            return 0;
        }
    }

    public static function getAllProductFromWishlist($user_id=''){
        if(Auth::check()){
            if($user_id=="") $user_id=auth()->user()->id;
            return Wishlist::with('product')->where('user_id',$user_id)->get();
        }
        else{
            return collect();
        }
    }

    public static function totalWishlistPrice($user_id=''){
        if(Auth::check()){
            if($user_id=="") $user_id=auth()->user()->id;
            $wishlists = Wishlist::with('product')->where('user_id',$user_id)->get();
            $total = 0;
            foreach($wishlists as $wishlist){
                if($wishlist->product){
                    $price = $wishlist->product->price;
                    $discount = $wishlist->product->discount ?? 0;
                    $discounted_price = $price - ($price * $discount / 100);
                    $total += $discounted_price;
                }
            }
            return $total;
        }
        else{
            return 0;
        }
    }

    public static function productCategoryList($option='all'){
        if ($option === 'all') {
            return Category::orderBy('id','DESC')->get();
        }
        return Category::has('products')->orderBy('id','DESC')->get();
    }

    public static function postTagList($option='all'){
        if ($option === 'all') {
            return PostTag::orderBy('id','desc')->get();
        }
        return PostTag::has('posts')->orderBy('id','desc')->get();
    }

    public static function postCategoryList($option="all"){
        if ($option === 'all') {
            return PostCategory::orderBy('id','DESC')->get();
        }
        return PostCategory::has('posts')->orderBy('id','DESC')->get();
    }

    // Admin home - cập nhật để sử dụng OrderDetail
    public static function earningPerMonth(){
        $month_data = Order::where('status','delivered')
            ->where('payment_status', 'paid')
            ->get();

        $price = 0;
        foreach($month_data as $data){
            $price += $data->orderDetails->sum(function($detail) {
                return $detail->price * $detail->quantity;
            });
        }
        return number_format((float)($price),2,'.','');
    }
}
?>
