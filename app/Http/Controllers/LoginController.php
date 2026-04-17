<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Display login page.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle authentication attempt.
     */
    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->validated();

        try {
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                Log::info('User logged in', ['user_id' => Auth::id(), 'username' => $credentials['username']]);

                return redirect()->intended('/dashboard');
            }

            Log::warning('Failed login attempt', ['username' => $credentials['username'], 'ip' => $request->ip()]);

            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->onlyInput('username');

        } catch (\Exception $e) {
            Log::error('Authentication error', ['error' => $e->getMessage()]);

            return back()->withErrors(['error' => 'An unexpected error occurred. Please try again later.']);
        }
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();
        $username = Auth::user()?->username;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['user_id' => $userId, 'username' => $username]);

        return redirect('/login');
    }
}
