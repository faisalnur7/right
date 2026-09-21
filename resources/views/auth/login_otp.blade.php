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


                <form method="POST" action="{{ route('login_verify') }}">
                    @csrf

                    <!-- OTP -->
                    <input type="hidden" name="temp_id" value="{{$temp_id}}" />
                    <div>
                        <x-input-label for="otp" :value="__('OTP')" class="!text-md" />
                        <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" :value="old('otp')" required autofocus />
                        <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                    </div>

                    <div class="flex items-start justify-end mt-4 flex-col gap-2">

                        <p class="text-gray-600 text-md">Please check your phone for the otp?</p>

                        <x-primary-button class="w-full lg:w-1/2 flex mx-auto justify-center align-center py-3 mt-6 capitalize !text-lg !rounded-full">
                            {{ __('Go') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
