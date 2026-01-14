<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class User
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Kiểm tra user có role 'user' hoặc 'admin'
            if (Auth::user()->role == 'user' || Auth::user()->role == 'admin') {
                return $next($request);
            } else {
                // Nếu không có quyền, chuyển hướng về trang chủ
                return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập!');
            }
        } else {
            // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
            return redirect()->route('login.form')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
        }
    }
}
