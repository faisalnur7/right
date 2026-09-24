<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @php
        $authPageTitle = match (true) {
            request()->routeIs('admin.login') => 'Admin Login',
            request()->routeIs('admin.register') => 'Create Admin Account',
            request()->routeIs('merchant.login') => 'Merchant Login',
            request()->routeIs('merchant.register') => 'Create Merchant Account',
            request()->routeIs('login') => 'Sign In',
            request()->routeIs('register') => 'Create Account',
            request()->routeIs('password.request') => 'Forgot Password',
            request()->routeIs('password.reset') => 'Reset Password',
            request()->routeIs('login_otp') => 'Verify Sign In',
            request()->routeIs('otp', 'user.otpPage') => 'Verify Account',
            default => 'Secure Access',
        };
    @endphp
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $authPageTitle }} | Right</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
        <link href="{{asset('assets/css/guest_style.css')}}" rel="stylesheet" />

    </head>
    <body class="font-sans text-gray-900 antialiased auth-body">
        @include('layouts.partials._customer_guest_nav')
        {{-- <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="flex items-center justify-center">
                <img src="" />
            </div>
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"> --}}
                {{ $slot }}
            {{-- </div>
        </div> --}}
        <script>
            document.addEventListener('click', function (event) {
                const toggle = event.target.closest('[data-password-toggle]');
                if (!toggle) return;

                const input = document.getElementById(toggle.dataset.passwordToggle);
                if (!input) return;

                const isVisible = input.type === 'text';
                input.type = isVisible ? 'password' : 'text';
                toggle.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
                toggle.innerHTML = `<i class="far ${isVisible ? 'fa-eye' : 'fa-eye-slash'}" aria-hidden="true"></i>`;
            });
        </script>
    </body>
</html>
