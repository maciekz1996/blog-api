<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Add api_token after authentication
     */
    protected function authenticated(Request $request, $user)
    {
        $user->api_token = Str::random(60);
        $user->save();

        return response()->json(['data' => $user->toArray()]);
    }

    /**
     * Override default logout
     */
    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();
        if ($user)
        {
            $user->api_token = null;
            $user->save();
        }

        return response()->json(['data' => 'logged out'], 200);
    }
}
