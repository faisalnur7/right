<x-guest-layout>
    <div class="w-full container flex mx-auto h-dvh">
            <div class="w-1/2 hidden lg:flex bg-[url('./assets/img/backgrounds/sign_in_bg.png')] bg-no-repeat bg-cover bg-bottom"></div>
            <div class="w-full lg:w-1/2 bg-white p-10 lg:p-12 flex flex-col gap-4 justify-start lg:justify-center">
                <div class="flex flex-col gap-2 items-start">
                    <a href="{{route('homepage')}}" class="p-3 flex lg:hidden text-white rounded-lg ml-0 border-2 border-gray-200 bg-gray-800">Go to Home </a>
                    <h1 class="text-3xl lg:text-4xl font-bold mb-3">Welcome to our store</h1>
                </div>
            <div>
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />


                {{-- <form method="POST" action="{{ route('login') }}"> --}}
                <form method="POST" action="{{ route('login.otp') }}">
                    @csrf

                <!-- Email or Phone -->
                <div>
                    <x-input-label for="login" :value="__('Email or Phone')" class="!text-md" />
                    <x-text-input
                        id="login"
                        class="block mt-1 w-full"
                        type="text"
                        name="login"
                        :value="old('login')"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <x-input-error :messages="$errors->get('login')" class="mt-2" />
                </div>


                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')"  class="!text-md" />

                        <x-text-input id="password" class="block mt-1 w-full"
                                        type="password"
                                        name="password"
                                        required autocomplete="current-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                            <span class="ms-2 text-md text-gray-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="flex items-start justify-end mt-4 flex-col gap-2">
                        @if (Route::has('password.request'))
                            <a class="underline text-md text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 p-0 m-0" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif

                        <p class="text-gray-600 text-md">Still don't have any account? <a class="underline decoration-1" href="{{route('register')}}">Register</a></p>

                        <x-primary-button class="w-full lg:w-1/2 flex mx-auto justify-center align-center py-3 mt-6 capitalize !text-lg !rounded-full">
                            {{ __('Sign in') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
