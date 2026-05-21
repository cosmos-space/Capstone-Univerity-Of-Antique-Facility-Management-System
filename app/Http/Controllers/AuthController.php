<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();

            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'college_staff' => redirect()->route('college.dashboard'),
                'org_staff' => redirect()->route('org.dashboard'),
                default => redirect()->route('home'),
            };
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $key = 'login:' . $request->ip();
        $attempts = app('cache')->get($key, 0);

        if ($attempts >= 5) {
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again later.',
            ])->onlyInput('email');
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            app('cache')->increment($key);
            app('cache')->put($key, app('cache')->get($key, 0), now()->addMinute());

            return back()->withErrors([
                'email' => 'Access denied. Invalid credentials.',
            ])->onlyInput('email');
        }

        app('cache')->forget($key);

        $request->session()->regenerate();
        $user = Auth::user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'college_staff' => redirect()->route('college.dashboard'),
            'org_staff' => redirect()->route('org.dashboard'),
            default => redirect()->route('home'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
 