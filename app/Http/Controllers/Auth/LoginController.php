<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginOtpMail;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function redirectTo()
    {
        if(Auth::check() && Auth::user()->role_id == 1) {
            return $this->redirectTo = route('admin.dashboard');
        } 
        elseif(Auth::check() && Auth::user()->role_id == 2) {
            return $this->redirectTo = route('merchant.dashboard');
        }
        else {
            return $this->redirectTo = route('login');
        }
    }
       
    public function logout(Request $request) {
        Auth::logout();
        Session::flush();
        return redirect('/login');
    }
}
