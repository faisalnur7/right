<x-guest-layout>
    <x-auth-shell eyebrow="Member account" title="Create your account" description="Join the Right community and start building your next chapter." panel-title="Start something meaningful." panel-description="A connected marketplace for people, products and possibilities.">
        <x-auth-session-status class="auth-status" :status="session('status')" />
        <form method="POST" action="{{ route('user.otp') }}">
            @csrf
            <div><x-input-label for="name" :value="__('Full name')"><span class="auth-required" aria-hidden="true">*</span></x-input-label><x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Your full name" /><x-input-error :messages="$errors->get('name')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="phone" :value="__('Phone number')"><span class="auth-required" aria-hidden="true">*</span></x-input-label><x-text-input id="phone" type="text" name="phone" :value="old('phone')" required autocomplete="tel" placeholder="01XXXXXXXXX" /><x-input-error :messages="$errors->get('phone')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="email" :value="__('Email address (Optional)')" /><x-text-input id="email" type="email" name="email" :value="old('email')" autocomplete="username" placeholder="you@example.com" /><x-input-error :messages="$errors->get('email')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password" :value="__('Password')"><span class="auth-required" aria-hidden="true">*</span></x-input-label><x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Create a strong password" /><x-input-error :messages="$errors->get('password')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password_confirmation" :value="__('Confirm password')"><span class="auth-required" aria-hidden="true">*</span></x-input-label><x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" /><x-input-error :messages="$errors->get('password_confirmation')" class="auth-error" /></div>
            <p class="auth-legal">By creating an account, you agree to our <a href="#">Terms of use</a> and <a href="#">Privacy Policy</a>.</p>
            <x-primary-button class="auth-submit">{{ __('Create my account') }}</x-primary-button>
        </form>
        <x-slot:footer>Already have an account? <a href="{{ route('login') }}">Sign in</a></x-slot:footer>
    </x-auth-shell>
</x-guest-layout>
