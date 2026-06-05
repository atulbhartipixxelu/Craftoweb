<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        $frontend = rtrim((string) config('app.frontend_url'), '/');

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()->away($frontend.'/login?google_error=1');
        }

        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();
        $name = $googleUser->getName() ?: ($googleUser->getNickname() ?: 'User');

        if (! $email || ! $googleId) {
            return redirect()->away($frontend.'/login?google_error=no_email');
        }

        $user = User::query()->where('google_id', $googleId)->first();

        if (! $user) {
            $existing = User::query()->where('email', $email)->first();
            if ($existing) {
                if ($existing->google_id && $existing->google_id !== $googleId) {
                    return redirect()->away($frontend.'/login?google_error=linked');
                }
                $existing->forceFill(['google_id' => $googleId])->save();
                $user = $existing->fresh();
            } else {
                $user = User::query()->create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Str::random(32),
                    'phone' => null,
                    'role' => 'passenger',
                    'google_id' => $googleId,
                ]);
            }
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth')->plainTextToken;

        return redirect()->away($frontend.'/auth/google/callback?token='.rawurlencode($token));
    }
}
