<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Exception;

class DiscordController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('discord')->redirect();
    }

    public function callback()
    {
        try {
            $discordUser = Socialite::driver('discord')->user();
            
            $user = User::where('email', $discordUser->email)->first();
            
            if (!$user) {
                // Create new user if they don't exist
                $user = User::create([
                    'email' => $discordUser->email,
                    'name' => $discordUser->name ?? $discordUser->nickname,
                    'password' => bcrypt(str_random(16)),
                    'root_admin' => false,
                    'language' => config('app.locale'),
                ]);
            }

            Auth::login($user);
            
            return redirect('/');
            
        } catch (Exception $e) {
            return redirect('/auth/login')->with('error', 'Discord authentication failed');
        }
    }
} 