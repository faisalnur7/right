<x-guest-layout>
    <x-auth-shell eyebrow="Merchant account recovery" title="Choose a new password" description="Create a new secure password for your merchant workspace." panel-title="Ready when you are." panel-description="Secure access to the tools that help your business grow.">
        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div><x-input-label for="email" :value="__('Email address')" /><x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" /><x-input-error :messages="$errors->get('email')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password" :value="__('New password')" /><x-text-input id="password" type="password" name="password" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password_confirmation" :value="__('Confirm new password')" /><x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password_confirmation')" class="auth-error" /></div>
            <x-primary-button class="auth-submit">{{ __('Reset password') }}</x-primary-button>
        </form>
        <x-slot:footer><a href="{{ route('merchant.login') }}">Return to merchant sign in</a></x-slot:footer>
    </x-auth-shell>
</x-guest-layout>
