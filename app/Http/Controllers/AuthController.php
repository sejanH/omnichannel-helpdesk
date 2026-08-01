<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show Login Form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Process Login Request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if user exists and is disabled
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser && ($existingUser->status === 'disabled' || $existingUser->status === 'inactive')) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact your administrator.'
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (Auth::user()->status === 'disabled' || Auth::user()->status === 'inactive') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact your administrator.'
                ])->onlyInput('email');
            }
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }

    /**
     * Show Registration Form (Disabled for security)
     */
    public function showRegister()
    {
        return redirect()->route('login')->withErrors([
            'email' => 'Public self-registration is disabled on this private helpdesk instance. Please contact your administrator to create your support agent account.'
        ]);
    }

    /**
     * Process Registration Request (Disabled for security)
     */
    public function register(Request $request)
    {
        return redirect()->route('login')->withErrors([
            'email' => 'Public self-registration is disabled on this private helpdesk instance.'
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
