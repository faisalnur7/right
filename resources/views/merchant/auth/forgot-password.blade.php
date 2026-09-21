<x-guest-layout>
    <x-auth-shell eyebrow="Merchant account recovery" title="Reset your password" description="Enter your business email and we’ll send a secure reset link." panel-title="Keep business moving." panel-description="Get back to your products, orders and customers without losing momentum.">
        <x-auth-session-status class="auth-status" :status="session('status')" />
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div><x-input-label for="email" :value="__('Business email')" /><x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@company.com" /><x-input-error :messages="$errors->get('email')" class="auth-error" /></div>
            <x-primary-button class="auth-submit">{{ __('Send reset link') }}</x-primary-button>
        </form>
        <x-slot:footer><a href="{{ route('merchant.login') }}">Return to merchant sign in</a></x-slot:footer>
    </x-auth-shell>
</x-guest-layout>
