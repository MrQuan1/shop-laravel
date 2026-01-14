<?php
// Controller này chỉ dành cho các chức năng tổng quan admin (dashboard, profile, thống kê...), không dùng cho quản lý user

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Category;
use App\Models\Product;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\Brand;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(){
        $category_count = Category::count();
        $product_count = Product::count();
        $brand_count = Brand::count();
        $order_count = Order::count();
        $user_count = User::where('role','user')->count();
        $post_category_count = PostCategory::count();
        $post_count = Post::count();

        // Lấy đơn hàng gần đây
        $recent_orders = Order::orderBy('id','desc')->limit(5)->get();

        return view('backend.index')
            ->with('category_count', $category_count)
            ->with('product_count', $product_count)
            ->with('brand_count', $brand_count)
            ->with('order_count', $order_count)
            ->with('user_count', $user_count)
            ->with('post_category_count', $post_category_count)
            ->with('post_count', $post_count)
            ->with('recent_orders', $recent_orders);
    }

    public function profile(){
        $profile = auth()->user();
        return view('backend.users.profile')->with('profile', $profile);
    }

    public function profileUpdate(Request $request, $id){
        $user = User::findOrFail($id);
        $data = $request->all();
        $status = $user->fill($data)->save();
        if($status){
            request()->session()->flash('success','Cập nhật hồ sơ thành công');
        }
        else{
            request()->session()->flash('error','Vui lòng thử lại!');
        }
        return redirect()->back();
    }

    public function settings(){
        $data = DB::table('settings')->get();
        return view('backend.setting')->with('data', $data);
    }

    public function settingsUpdate(Request $request){
        $data = $request->all();
        $status = DB::table('settings')->where('id', 1)->update($data);
        if($status){
            request()->session()->flash('success','Cài đặt cập nhật thành công');
        }
        else{
            request()->session()->flash('error','Vui lòng thử lại');
        }
        return redirect()->route('admin');
    }

    public function changePassword(){
        return view('backend.layouts.changePassword');
    }

    public function changPasswordStore(Request $request){
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        $current_password = auth()->user()->password;

        if(Hash::check($request->current_password, $current_password)){
            $user_id = auth()->user()->id;
            $obj_user = User::find($user_id);
            $obj_user->password = Hash::make($request->new_password);
            $obj_user->save();
            return redirect()->route('admin')->with('success','Mật khẩu thay đổi thành công!');
        }
        else{
            return redirect()->back()->with('error','Mật khẩu hiện tại không đúng!');
        }
    }

    public function incomeChart(Request $request){
        $year = $request->year ?? date('Y');

        $items = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('payment_status', 'paid')
            ->where('status', 'delivered')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data = [];
        for($i = 1; $i <= 12; $i++){
            $data[$i] = 0;
        }

        foreach($items as $item){
            $data[$item->month] = $item->total;
        }

        return response()->json($data);
    }

    public function bestSellingProducts(Request $request){
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        $products = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.title',
                'products.photo',
                'products.stock',
                DB::raw('SUM(order_details.quantity) as total_sold'),
                DB::raw('SUM(order_details.quantity * order_details.price) as total_revenue')
            )
            ->where('orders.payment_status', 'paid')
            ->where('orders.status', 'delivered')
            ->whereMonth('orders.created_at', $month)
            ->whereYear('orders.created_at', $year)
            ->groupBy('products.id', 'products.title', 'products.photo', 'products.stock')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    public function monthlyRevenue(Request $request){
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        $revenues = Order::select(
            DB::raw('DAY(created_at) as day'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('payment_status', 'paid')
            ->where('status', 'delivered')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $data = [];
        foreach($revenues as $revenue){
            $data[$revenue->day] = $revenue->total;
        }

        return response()->json($data);
    }
}
