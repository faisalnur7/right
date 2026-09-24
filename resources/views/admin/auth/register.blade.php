<x-guest-layout>
    <x-auth-shell eyebrow="Admin workspace" title="Create an admin account" description="Set up your secure access to the Right operations dashboard." panel-title="Make every move count." panel-description="Bring your team, products and performance into one professional workspace.">
        <form method="POST" action="{{ route('admin.store') }}">
            @csrf
            <div><x-input-label for="name" :value="__('Full name')" /><x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Your full name" /><x-input-error :messages="$errors->get('name')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="email" :value="__('Email address')" /><x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@company.com" /><x-input-error :messages="$errors->get('email')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password" :value="__('Password')" /><x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Create a strong password" /><x-input-error :messages="$errors->get('password')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password_confirmation" :value="__('Confirm password')" /><x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" /><x-input-error :messages="$errors->get('password_confirmation')" class="auth-error" /></div>
            <x-primary-button class="auth-submit">{{ __('Create admin account') }}</x-primary-button>
        </form>
        <x-slot:footer>Already have admin access? <a href="{{ route('admin.login') }}">Sign in</a></x-slot:footer>
    </x-auth-shell>
</x-guest-layout>
