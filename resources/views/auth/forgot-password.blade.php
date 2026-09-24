<x-guest-layout>
    <x-auth-shell eyebrow="Account recovery" title="Reset your password" description="Enter the email connected to your account and we’ll send you a secure reset link." panel-title="Back to your best." panel-description="A secure, simple way to get back into your Right account and keep moving.">
        <x-auth-session-status class="auth-status" :status="session('status')" />
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div><x-input-label for="email" :value="__('Email address')" /><x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" /><x-input-error :messages="$errors->get('email')" class="auth-error" /></div>
            <x-primary-button class="auth-submit">{{ __('Send reset link') }}</x-primary-button>
        </form>
        <x-slot:footer>Remember your password? <a href="{{ route('login') }}">Back to sign in</a></x-slot:footer>
    </x-auth-shell>
</x-guest-layout>
