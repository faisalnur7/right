<x-guest-layout>
    <x-auth-shell eyebrow="Admin account recovery" title="Reset your admin password" description="Enter your email and we’ll send a secure password reset link." panel-title="Stay in control." panel-description="Secure access keeps your operations moving with confidence.">
        <x-auth-session-status class="auth-status" :status="session('status')" />
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div><x-input-label for="email" :value="__('Email address')" /><x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="admin@company.com" /><x-input-error :messages="$errors->get('email')" class="auth-error" /></div>
            <x-primary-button class="auth-submit">{{ __('Send reset link') }}</x-primary-button>
        </form>
        <x-slot:footer><a href="{{ route('admin.login') }}">Return to admin sign in</a></x-slot:footer>
    </x-auth-shell>
</x-guest-layout>
