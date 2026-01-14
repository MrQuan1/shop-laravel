<?php

namespace App\Http\Controllers;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostTag;
use App\Models\Product;
use App\User;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Newsletter;
use Session;

class FrontendController extends Controller
{

    public function index(Request $request){
        return redirect()->route($request->user()->role);
    }

    public function home(){
        $featured = Product::where('is_featured', 1)
            ->where('stock', '>', 0)
            ->orderBy('price', 'DESC')
            ->limit(2)
            ->get();

        $posts = Post::orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        $products = Product::where('stock', '>', 0)
            ->orderBy('id', 'DESC')
            ->limit(8)
            ->get();

        $category = Category::orderBy('title', 'ASC')->get();

        return view('frontend.index')
            ->with('featured', $featured)
            ->with('posts', $posts)
            ->with('product_lists', $products)
            ->with('category_lists', $category);
    }

    public function aboutUs(){
        return view('frontend.pages.about-us');
    }

    public function contact(){
        return view('frontend.pages.contact');
    }

    public function productDetail($slug){
        $product_detail = Product::getProductBySlug($slug);
        return view('frontend.pages.product_detail')->with('product_detail', $product_detail);
    }

    public function productGrids(){
        $products = Product::query();

        if(!empty($_GET['category'])){
            $slug = explode(',', $_GET['category']);
            $cat_ids = Category::select('id')
                ->whereIn('slug', $slug)
                ->pluck('id')
                ->toArray();
            $products->whereIn('cat_id', $cat_ids);
        }

        if(!empty($_GET['brand'])){
            $slugs = explode(',', $_GET['brand']);
            $brand_ids = Brand::select('id')
                ->whereIn('slug', $slugs)
                ->pluck('id')
                ->toArray();
            $products->whereIn('brand_id', $brand_ids);
        }

        if(!empty($_GET['sortBy'])){
            if($_GET['sortBy'] == 'title'){
                $products = $products->where('stock', '>', 0)->orderBy('title', 'ASC');
            }
            if($_GET['sortBy'] == 'price'){
                $products = $products->orderBy('price', 'ASC');
            }
        }

        if(!empty($_GET['price'])){
            $price = explode('-', $_GET['price']);
            $products->whereBetween('price', $price);
        }

        $recent_products = Product::where('stock', '>', 0)
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        if(!empty($_GET['show'])){
            $products = $products->where('stock', '>', 0)->paginate($_GET['show']);
        } else {
            $products = $products->where('stock', '>', 0)->paginate(9);
        }

        return view('frontend.pages.product-grids')
            ->with('products', $products)
            ->with('recent_products', $recent_products);
    }

    public function productLists(){
        $products = Product::query();

        if(!empty($_GET['category'])){
            $slug = explode(',', $_GET['category']);
            $cat_ids = Category::select('id')
                ->whereIn('slug', $slug)
                ->pluck('id')
                ->toArray();
            $products->whereIn('cat_id', $cat_ids);
        }

        if(!empty($_GET['brand'])){
            $slugs = explode(',', $_GET['brand']);
            $brand_ids = Brand::select('id')
                ->whereIn('slug', $slugs)
                ->pluck('id')
                ->toArray();
            $products->whereIn('brand_id', $brand_ids);
        }

        if(!empty($_GET['sortBy'])){
            if($_GET['sortBy'] == 'title'){
                $products = $products->where('stock', '>', 0)->orderBy('title', 'ASC');
            }
            if($_GET['sortBy'] == 'price'){
                $products = $products->orderBy('price', 'ASC');
            }
        }

        if(!empty($_GET['price'])){
            $price = explode('-', $_GET['price']);
            $products->whereBetween('price', $price);
        }

        $recent_products = Product::where('stock', '>', 0)
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        if(!empty($_GET['show'])){
            $products = $products->where('stock', '>', 0)->paginate($_GET['show']);
        } else {
            $products = $products->where('stock', '>', 0)->paginate(6);
        }

        return view('frontend.pages.product-lists')
            ->with('products', $products)
            ->with('recent_products', $recent_products);
    }

    public function productFilter(Request $request){
        $data = $request->all();

        $showURL = "";
        if(!empty($data['show'])){
            $showURL .= '&show=' . $data['show'];
        }

        $sortByURL = '';
        if(!empty($data['sortBy'])){
            $sortByURL .= '&sortBy=' . $data['sortBy'];
        }

        $catURL = "";
        if(!empty($data['category'])){
            foreach($data['category'] as $category){
                if(empty($catURL)){
                    $catURL .= '&category=' . $category;
                } else {
                    $catURL .= ',' . $category;
                }
            }
        }

        $brandURL = "";
        if(!empty($data['brand'])){
            foreach($data['brand'] as $brand){
                if(empty($brandURL)){
                    $brandURL .= '&brand=' . $brand;
                } else {
                    $brandURL .= ',' . $brand;
                }
            }
        }

        $priceRangeURL = "";
        if(!empty($data['price_range'])){
            $priceRangeURL .= '&price=' . $data['price_range'];
        }

        if(request()->is('product-grids')){
            return redirect()->route('product-grids', $catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
        } else {
            return redirect()->route('product-lists', $catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
        }
    }

    public function productSearch(Request $request){
        $recent_products = Product::where('stock', '>', 0)
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        $products = Product::where('title', 'like', '%'.$request->search.'%')
            ->orWhere('slug', 'like', '%'.$request->search.'%')
            ->orWhere('description', 'like', '%'.$request->search.'%')
            ->orWhere('summary', 'like', '%'.$request->search.'%')
            ->orWhere('price', 'like', '%'.$request->search.'%')
            ->where('stock', '>', 0)
            ->orderBy('id', 'DESC')
            ->paginate(9);

        return view('frontend.pages.product-grids')
            ->with('products', $products)
            ->with('recent_products', $recent_products);
    }

    public function productBrand(Request $request){
        $brand = Brand::where('slug', $request->slug)->first();
        $recent_products = Product::where('stock', '>', 0)
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        if($brand){
            $products = Product::where('brand_id', $brand->id)
                ->where('stock', '>', 0)
                ->paginate(9);

            return view('frontend.pages.product-grids')
                ->with('products', $products)
                ->with('recent_products', $recent_products);
        } else {
            return redirect()->back()->with('error', 'Thương hiệu không tồn tại');
        }
    }

    public function productCat(Request $request){
        $category = Category::where('slug', $request->slug)->first();
        $recent_products = Product::where('stock', '>', 0)
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        if($category){
            $products = Product::where('cat_id', $category->id)
                ->where('stock', '>', 0)
                ->paginate(9);

            return view('frontend.pages.product-grids')
                ->with('products', $products)
                ->with('recent_products', $recent_products);
        } else {
            return redirect()->back()->with('error', 'Danh mục không tồn tại');
        }
    }

    public function productSubCat(Request $request){
        $category = Category::where('slug', $request->sub_slug)->first();
        $recent_products = Product::where('stock', '>', 0)
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        if($category){
            $products = Product::where('cat_id', $category->id)
                ->where('stock', '>', 0)
                ->paginate(9);

            return view('frontend.pages.product-grids')
                ->with('products', $products)
                ->with('recent_products', $recent_products);
        } else {
            return redirect()->back()->with('error', 'Danh mục con không tồn tại');
        }
    }

    public function blog(){
        $post = Post::query();

        if(!empty($_GET['category'])){
            $slug = explode(',', $_GET['category']);
            $cat_ids = PostCategory::select('id')
                ->whereIn('slug', $slug)
                ->pluck('id')
                ->toArray();
            $post->whereIn('post_cat_id', $cat_ids);
        }

        if(!empty($_GET['show'])){
            $post = $post->orderBy('id', 'DESC')
                ->paginate($_GET['show']);
        } else {
            $post = $post->orderBy('id', 'DESC')
                ->paginate(9);
        }

        $rcnt_post = Post::orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        return view('frontend.pages.blog')
            ->with('posts', $post)
            ->with('recent_posts', $rcnt_post);
    }

    public function blogDetail($slug){
        $post = Post::getPostBySlug($slug);
        $rcnt_post = Post::orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        return view('frontend.pages.blog-detail')
            ->with('post', $post)
            ->with('recent_posts', $rcnt_post);
    }

    public function blogSearch(Request $request){
        $rcnt_post = Post::orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        $posts = Post::where('title', 'like', '%'.$request->search.'%')
            ->orWhere('quote', 'like', '%'.$request->search.'%')
            ->orWhere('summary', 'like', '%'.$request->search.'%')
            ->orWhere('description', 'like', '%'.$request->search.'%')
            ->orWhere('slug', 'like', '%'.$request->search.'%')
            ->orderBy('id', 'DESC')
            ->paginate(8);

        return view('frontend.pages.blog')
            ->with('posts', $posts)
            ->with('recent_posts', $rcnt_post);
    }

    public function blogFilter(Request $request){
        $data = $request->all();

        $catURL = "";
        if(!empty($data['category'])){
            foreach($data['category'] as $category){
                if(empty($catURL)){
                    $catURL .= '&category=' . $category;
                } else {
                    $catURL .= ',' . $category;
                }
            }
        }

        return redirect()->route('blog', $catURL);
    }

    public function blogByCategory(Request $request){
        $post = PostCategory::getBlogByCategory($request->slug);
        $rcnt_post = Post::orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        return view('frontend.pages.blog')
            ->with('posts', $post->posts)
            ->with('recent_posts', $rcnt_post);
    }

    // Login
    public function login(){
        return view('frontend.pages.login');
    }

    public function loginSubmit(Request $request){
        $data = $request->all();
        if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){
            Session::put('user', $data['email']);
            request()->session()->flash('success', 'Đăng nhập thành công');
            return redirect()->route('home');
        } else {
            request()->session()->flash('error', 'Email hoặc mật khẩu không đúng!');
            return redirect()->back();
        }
    }

    public function logout(){
        Session::forget('user');
        Auth::logout();
        request()->session()->flash('success', 'Đăng xuất thành công');
        return back();
    }

    public function register(){
        return view('frontend.pages.register');
    }

    public function registerSubmit(Request $request){
        $this->validate($request, [
            'name' => 'string|required|min:2',
            'email' => 'string|required|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $data = $request->all();
        $check = $this->create($data);
        Session::put('user', $data['email']);

        if($check){
            request()->session()->flash('success', 'Đăng ký thành công');
            return redirect()->route('home');
        } else {
            request()->session()->flash('error', 'Vui lòng thử lại!');
            return back();
        }
    }

    public function create(array $data){
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    // Reset password
    public function showResetForm(){
        return view('auth.passwords.old-reset');
    }

    public function subscribe(Request $request){
        if(!Newsletter::isSubscribed($request->email)){
            Newsletter::subscribePending($request->email);
            if(Newsletter::lastActionSucceeded()){
                request()->session()->flash('success', 'Đã đăng ký! Vui lòng kiểm tra Email của bạn');
                return redirect()->route('home');
            } else {
                Newsletter::getLastError();
                return back()->with('error', 'Có lỗi xảy ra ! Vui lòng thử lại');
            }
        } else {
            request()->session()->flash('error', 'Bạn đã đăng ký rồi !!!');
            return back();
        }
    }
}
