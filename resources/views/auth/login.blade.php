<x-guest-layout>
    <x-auth-shell eyebrow="Member account" title="Welcome back" description="Sign in to continue your journey with Right." panel-title="Grow on your terms." panel-description="Access your network, orders and opportunities from a space designed around your momentum.">
        <x-auth-session-status class="auth-status" :status="session('status')" />
        <form method="POST" action="{{ route('login.otp') }}">
            @csrf
            <div><x-input-label for="login" :value="__('Email or phone')" /><x-text-input id="login" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" placeholder="Email address or phone number" /><x-input-error :messages="$errors->get('login')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password" :value="__('Password')" /><x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" /><x-input-error :messages="$errors->get('password')" class="auth-error" /></div>
            <div class="auth-form-row"><label class="auth-check"><input id="remember_me" type="checkbox" name="remember"><span>Remember me</span></label>@if (Route::has('password.request'))<a href="{{ route('password.request') }}">Forgot password?</a>@endif</div>
            <x-primary-button class="auth-submit">{{ __('Continue securely') }}</x-primary-button>
        </form>
        <x-slot:footer>New to Right? <a href="{{ route('register') }}">Create an account</a></x-slot:footer>
    </x-auth-shell>
</x-guest-layout>
