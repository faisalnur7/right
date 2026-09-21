<x-guest-layout>
    <x-auth-shell eyebrow="Admin workspace" title="Welcome back, admin" description="Sign in to manage your marketplace, members and operations." panel-title="Lead with clarity." panel-description="Your command center for the Right platform is just one secure sign-in away.">
        <x-auth-session-status class="auth-status" :status="session('status')" />
        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div><x-input-label for="email" :value="__('Email address')" /><x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@company.com" /><x-input-error :messages="$errors->get('email')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password" :value="__('Password')" /><x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" /><x-input-error :messages="$errors->get('password')" class="auth-error" /></div>
            <div class="auth-form-row"><label class="auth-check"><input id="remember_me" type="checkbox" name="remember"><span>Remember me</span></label>@if (Route::has('password.request'))<a href="{{ route('password.request') }}">Forgot password?</a>@endif</div>
            <x-primary-button class="auth-submit">{{ __('Sign in to admin') }}</x-primary-button>
        </form>
        <x-slot:footer>Need a user account? <a href="{{ route('register') }}">Sign in as a member</a></x-slot:footer>
    </x-auth-shell>
</x-guest-layout>
