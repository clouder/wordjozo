<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

Route::get('auth/{provider}', function (string $provider) {
    if (!in_array($provider, ['github', 'google', 'facebook'])) {
        return redirect()->route('home')->withErrors(['error' => 'Unsupported authentication provider.']);
    }

    return Socialite::driver($provider)->redirect();
})->name('auth.socialite.redirect');

Route::get('auth/{provider}/callback', function (string $provider) {
    $user = Socialite::driver($provider)->user();

    if (!$user) {
        return redirect()->route('home')->withErrors(['error' => 'Authentication failed.']);
    }

    // Check if the user already exists in the database
    $existingUser = User::where('email', $user->getEmail())->first();

    if (!$existingUser) {
        // If the user does not exist, create a new user
        $existingUser = User::create([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => bcrypt(Str::random(16)), // Generate a random password
        ]);
    }

    SocialAccount::updateOrCreate(
        ['provider' => $provider, 'email' => $user->getEmail()],
        [
            'user_id' => $existingUser->id,
            'provider_user_id' => $user->getId(),
            'access_token' => $user->token,
            'refresh_token' => $user->refreshToken,
            'token_expires_at' => now()->addSeconds($user->expiresIn),
            'name' => $user->getName(),
            'avatar' => $user->getAvatar(),
        ]
    );

    auth()->login($existingUser);

    return redirect()->route('dashboard')->with('success', 'Logged in with GitHub successfully!');
})->name('auth.github.callback');

require __DIR__.'/auth.php';
