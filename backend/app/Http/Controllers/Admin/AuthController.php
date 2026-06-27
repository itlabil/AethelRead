<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends AdminController
{
    /**
     * Show the admin login page.
     */
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin login.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid credentials.']);
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    /**
     * Handle admin logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}