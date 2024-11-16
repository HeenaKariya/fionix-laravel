<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(RouteServiceProvider::HOME);
    // }
    public function store(Request $request)
    {
        $request->validate([
            'mobile_no' => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('mobile_no', 'password'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->hasRole('admin')) {
                return Redirect::route('admin.dashboard');
            } elseif ($user->hasRole('manager')) {
                return Redirect::route('manager.dashboard');
            } elseif ($user->hasRole('owner')) {
                return Redirect::route('owner.dashboard');
            } elseif ($user->hasRole('supervisor')) {
                return Redirect::route('supervisor.dashboard');
            } elseif ($user->hasRole('account manager')) {
                return Redirect::route('account-manager.dashboard');
            }
        }

        return back()->withErrors([
            'mobile_no' => 'The provided credentials do not match our records.',
        ]);
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
