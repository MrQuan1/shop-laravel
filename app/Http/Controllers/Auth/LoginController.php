<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Loại bỏ điều kiện status, chỉ kiểm tra email và password
        if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'])))
        {
            $user = auth()->user();
            if($user->role == 'admin'){
                return redirect()->route('admin');
            }
            else{
                return redirect()->route('home');
            }
        }
        else{
            return redirect()->route('login')
                ->with('error','Email hoặc mật khẩu không đúng.');
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
}
