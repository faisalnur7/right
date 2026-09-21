@php
    $isAdminAuth = request()->is('admin/*');
    $isMerchantAuth = request()->is('merchant/*');
    $loginRoute = $isAdminAuth ? 'admin.login' : ($isMerchantAuth ? 'merchant.login' : 'login');
    $registerRoute = $isAdminAuth ? 'admin.register' : ($isMerchantAuth ? 'merchant.register' : 'register');
    $loginLabel = $isAdminAuth ? 'Admin login' : ($isMerchantAuth ? 'Merchant login' : 'Sign in');
    $registerLabel = $isAdminAuth ? 'Create admin account' : ($isMerchantAuth ? 'Become a merchant' : 'Create an account');
    $homeActive = request()->routeIs('homepage');
    $registerActive = request()->routeIs($registerRoute);
    $loginActive = request()->routeIs($loginRoute);
@endphp
<nav class="guest-nav">
    <div class="guest-nav__inner">
        <div class="guest-nav__bar">
            <!-- Logo -->
            <div class="guest-nav__brand-wrap">
                <a href="{{route('homepage')}}" class="guest-nav__brand">
                    <img src="{{asset('./assets/img/site_logo.webp')}}" class="guest-nav__logo" alt="Right">
                </a>
            </div>

            <!-- Search Bar -->
            {{-- <div class="hidden md:flex items-center flex-1 mx-8">
                <input 
                    type="text" 
                    placeholder="Search for products..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div> --}}

            <!-- Navigation Links -->
            <div class="guest-nav__links">
                <a href="{{route('homepage')}}" class="guest-nav__link {{ $homeActive ? 'guest-nav__link--active' : '' }}">Home</a>
                <a href="{{route($registerRoute)}}" class="guest-nav__link {{ $registerActive ? 'guest-nav__link--active' : '' }}">{{ $registerLabel }}</a>
                <a href="{{route($loginRoute)}}" class="guest-nav__link guest-nav__link--cta {{ $loginActive ? 'guest-nav__link--active' : '' }}">{{ $loginLabel }}</a>
            </div>
        </div>

        <!-- Mobile Menu Button -->
        <div class="guest-nav__mobile-trigger">
            <button id="menuButton" class="guest-nav__menu-button" aria-label="Open navigation">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden">
        <a href="{{ route('homepage') }}" class="guest-nav__mobile-link {{ $homeActive ? 'guest-nav__link--active' : '' }}">Home</a>
        <a href="{{ route($registerRoute) }}" class="guest-nav__mobile-link {{ $registerActive ? 'guest-nav__link--active' : '' }}">{{ $registerLabel }}</a>
        <a href="{{ route($loginRoute) }}" class="guest-nav__mobile-link {{ $loginActive ? 'guest-nav__link--active' : '' }}">{{ $loginLabel }}</a>
    </div>
</nav>

<script>
    const menuButton = document.getElementById('menuButton');
    const mobileMenu = document.getElementById('mobileMenu');

    menuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
</script>
