<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DocsController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->canAccessDocs()) {
            return redirect()->route('docs.index');
        }

        return view('docs.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        // Auth::guard('web') — default, users table use karega
        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Invalid email or password.',
                ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user->canAccessDocs()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'You do not have permission to access API documentation.',
                ]);
        }

        return redirect()->intended(route('docs.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('docs.login');
    }

    public function index(): View
    {
        return view('docs.index', [
            'user' => Auth::user(),
            'apiBaseUrl' => rtrim(config('app.url'), '/').'/api',
        ]);
    }
}