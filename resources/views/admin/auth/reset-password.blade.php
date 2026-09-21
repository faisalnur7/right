<x-guest-layout>
    <x-auth-shell eyebrow="Admin account recovery" title="Choose a new password" description="Create a new secure password for your admin workspace." panel-title="Secure by design." panel-description="Your operations deserve a clear, protected home base.">
        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div><x-input-label for="email" :value="__('Email address')" /><x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" /><x-input-error :messages="$errors->get('email')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password" :value="__('New password')" /><x-text-input id="password" type="password" name="password" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password_confirmation" :value="__('Confirm new password')" /><x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password_confirmation')" class="auth-error" /></div>
            <x-primary-button class="auth-submit">{{ __('Reset admin password') }}</x-primary-button>
        </form>
        <x-slot:footer><a href="{{ route('admin.login') }}">Return to admin sign in</a></x-slot:footer>
    </x-auth-shell>
</x-guest-layout>
