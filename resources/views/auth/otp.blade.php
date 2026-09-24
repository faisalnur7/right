<x-guest-layout>
    <x-auth-shell eyebrow="Secure verification" title="Verify your phone" description="Enter the one-time code to finish creating your account." panel-title="Welcome to the network." panel-description="A quick verification helps us keep your new account safe from the start.">
        <x-auth-session-status class="auth-status" :status="session('status')" />
        <form method="POST" action="{{ route('verify') }}">
            @csrf
            <input type="hidden" name="temp_id" value="{{ $temp_id }}">
            <div><x-input-label for="otp" :value="__('Verification code')" /><x-text-input id="otp" type="text" name="otp" :value="old('otp')" required autofocus inputmode="numeric" autocomplete="one-time-code" placeholder="Enter your OTP" /><x-input-error :messages="$errors->get('otp')" class="auth-error" /></div>
            <p class="auth-legal">Didn’t receive the code? <button type="submit" form="resend-otp" class="auth-inline-button">Resend OTP</button></p>
            <x-primary-button class="auth-submit">{{ __('Verify account') }}</x-primary-button>
        </form>
        <form id="resend-otp" method="POST" action="{{ route('resend.otp') }}">@csrf<input type="hidden" name="phone" value="{{ old('phone', session('phone')) }}"><input type="hidden" name="email" value="{{ old('email', session('email')) }}"></form>
    </x-auth-shell>
</x-guest-layout>
