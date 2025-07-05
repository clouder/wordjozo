<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Log in to your account')" :description="__('Please enter your credentials to log in.')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />


    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Remember me')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}</flux:button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Don\'t have an account?') }}
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
    @endif
    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('or sign in socially') }}
    </div>
    <flux:button :href="route('auth.socialite.redirect', ['provider' => 'google'])">
        {{ __('Sign in with Google') }}
    </flux:button>
    {{-- <flux:button :href="route('auth.socialite.redirect', ['provider' => 'facebook'])">
        {{ __('Sign in with Facebook') }}
    </flux:button> --}}
    <flux:button :href="route('auth.socialite.redirect', ['provider' => 'github'])">
        {{ __('Sign in with GitHub') }}
    </flux:button>
</div>
