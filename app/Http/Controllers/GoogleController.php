<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User; // if you store user
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        // request extra scopes and offline access to get refresh token
        return Socialite::driver('google')
            ->scopes([
                'openid',
                'profile',
                'email',
                'https://www.googleapis.com/auth/calendar.readonly',
                'https://www.googleapis.com/auth/gmail.readonly',
                'https://www.googleapis.com/auth/tasks.readonly',
            ])
            ->with([
                'access_type' => 'offline',    // ask for refresh token
                'prompt' => 'consent'         // force consent to ensure refresh token is returned
            ])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        // If you have session issues, you can use stateless(); try without first
        $googleUser = Socialite::driver('google')->stateless()->user();

        // $googleUser has: ->getId(), ->getName(), ->getEmail(), ->token, ->refreshToken, ->expiresIn
        // Save user + tokens in DB (example)
        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken ?? null,
                'google_token_expires_at' => now()->addSeconds($googleUser->expiresIn),
            ]
        );

        Auth::login($user);

        return redirect('/dashboard'); // or /calendar page
    }
}
