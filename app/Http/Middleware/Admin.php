<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
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
            if (Auth::user()->role == 'admin') {
                return $next($request);
            } else {
                return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang admin!');
            }
        } else {
            return redirect()->route('login.form')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
        }
    }
}
