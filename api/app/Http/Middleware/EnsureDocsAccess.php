<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDocsAccess
{
  public function handle(Request $request, Closure $next): Response
  {
    if (! Auth::check()) {
      return redirect()->route('docs.login');
    }

    if (! Auth::user()->canAccessDocs()) {
      Auth::logout();

      $request->session()->invalidate();
      $request->session()->regenerateToken();

      return redirect()
        ->route('docs.login')
        ->withErrors([
          'email' => 'You do not have permission to access API documentation.',
        ]);
    }

    return $next($request);
  }
}