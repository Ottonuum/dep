<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
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
    protected $redirectTo = RouteServiceProvider::HOME;

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
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'login';
    }

    public function login(Request $request)
    {
        $input = $request->all();
 
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $loginField = $credentials['email'];
        $password = $credentials['password'];

        // Check for hardcoded admin credentials
        if (($loginField === 'admin' || $loginField === 'admin@gmail.com') && $password === 'admin') {
            $admin = \App\Models\User::firstOrCreate(
                ['email' => 'admin@gmail.com'],
                [
                    'name' => 'Admin',
                    'password' => bcrypt('admin'),
                    'role' => 'admin'
                ]
            );

            Auth::login($admin);
            $request->session()->regenerate();
            return redirect()->intended($this->redirectPath());
        }

        // Check for hardcoded regular user credentials
        if (($loginField === 'user' || $loginField === 'user@gmail.com') && $password === 'user') {
            $user = \App\Models\User::firstOrCreate(
                ['email' => 'user@gmail.com'],
                [
                    'name' => 'Regular User',
                    'password' => bcrypt('user'),
                    'role' => 'user'
                ]
            );

            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended($this->redirectPath());
        }

        return back()->withErrors([
            'email' => 'Invalid credentials. Use admin/admin or user/user to login.',
        ])->onlyInput('email');
    }
}
