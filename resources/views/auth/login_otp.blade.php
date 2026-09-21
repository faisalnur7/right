<x-guest-layout>
    <x-auth-shell eyebrow="Secure verification" title="Check your phone" description="Enter the one-time code we sent to verify your sign-in." panel-title="Almost there." panel-description="One quick verification keeps your account protected and your access personal.">
        <x-auth-session-status class="auth-status" :status="session('status')" />
        <form method="POST" action="{{ route('login_verify') }}">
            @csrf
            <input type="hidden" name="temp_id" value="{{ $temp_id }}">
            <div><x-input-label for="otp" :value="__('Verification code')" /><x-text-input id="otp" type="text" name="otp" :value="old('otp')" required autofocus inputmode="numeric" autocomplete="one-time-code" placeholder="Enter your OTP" /><x-input-error :messages="$errors->get('otp')" class="auth-error" /></div>
            <p class="auth-legal">The code is valid for a limited time. Please check your phone.</p>
            <x-primary-button class="auth-submit">{{ __('Verify and continue') }}</x-primary-button>
        </form>
        <x-slot:footer><a href="{{ route('login') }}">Back to sign in</a></x-slot:footer>
    </x-auth-shell>
</x-guest-layout>
