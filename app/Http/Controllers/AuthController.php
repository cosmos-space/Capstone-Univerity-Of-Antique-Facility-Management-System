<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            $requiredRole = session('required_role');
            
            // Validate role if one is required
            if ($requiredRole && $user->role !== $requiredRole) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Clear the required role from session
                session()->forget('required_role');
                
                return back()->withErrors([
                    'email' => 'Access denied. Invalid credentials.',
                ]);
            }
            
            // Clear the required role from session after successful validation
            session()->forget('required_role');
            
            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'college_staff') {
                return redirect()->route('college.dashboard');
            } elseif ($user->role === 'org_staff') {
                return redirect()->route('org.dashboard');
            }
            
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Access denied. Invalid credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        // Capture role before logout so we know where to send them back
        $role = optional(Auth::user())->role;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Build the portal-entry URL with the same access_token and role
        $accessToken = env('FMS_ACCESS_TOKEN', 'UA-FMS-ACCESS-2025');

        // Fall back to generic login if for some reason role is missing/unknown
        if (!in_array($role, ['admin', 'college_staff', 'org_staff'], true)) {
            return redirect()->route('login');
        }

        // This will pass through login.access middleware again
        $url = route('login', [
            'access_token' => $accessToken,
            'role' => $role,
        ]);

        return redirect($url);
    }
}
 