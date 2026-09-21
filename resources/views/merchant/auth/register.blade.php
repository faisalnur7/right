<x-guest-layout>
    <x-auth-shell eyebrow="Merchant workspace" title="Start selling with Right" description="Create your merchant account and bring your products to more customers." panel-title="Turn your ambition into reach." panel-description="A polished storefront and a connected marketplace to help your business go further.">
        <form method="POST" action="{{ route('merchant.store') }}">
            @csrf
            <div><x-input-label for="name" :value="__('Business or full name')" /><x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Your business name" /><x-input-error :messages="$errors->get('name')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="email" :value="__('Business email')" /><x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@company.com" /><x-input-error :messages="$errors->get('email')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password" :value="__('Password')" /><x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Create a strong password" /><x-input-error :messages="$errors->get('password')" class="auth-error" /></div>
            <div class="mt-4"><x-input-label for="password_confirmation" :value="__('Confirm password')" /><x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" /><x-input-error :messages="$errors->get('password_confirmation')" class="auth-error" /></div>
            <x-primary-button class="auth-submit">{{ __('Create merchant account') }}</x-primary-button>
        </form>
        <x-slot:footer>Already have a merchant account? <a href="{{ route('merchant.login') }}">Sign in</a></x-slot:footer>
    </x-auth-shell>
</x-guest-layout>
