<x-guest-layout>
    <x-auth-shell eyebrow="Account recovery" title="Choose a new password" description="Create a strong password to keep your Right account protected." panel-title="A fresh start, securely." panel-description="Update your access details and return to the work that matters.">
        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div><x-input-label for="email" :value="__('Email address')" /><x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="you@example.com" /><x-input-error :messages="$errors->get('email')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password" :value="__('New password')" /><x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Create a strong password" /><x-input-error :messages="$errors->get('password')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password_confirmation" :value="__('Confirm new password')" /><x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" /><x-input-error :messages="$errors->get('password_confirmation')" class="auth-error" /></div>
            <x-primary-button class="auth-submit">{{ __('Reset password') }}</x-primary-button>
        </form>
        <x-slot:footer><a href="{{ route('login') }}">Return to sign in</a></x-slot:footer>
    </x-auth-shell>
</x-guest-layout>
