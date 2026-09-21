<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Right BD । Affiliate Marketing Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-red-500 font-bold text-2xl"><img src="{{ asset('assets/img/site_logo.webp') }}"
                        class="h-10" /></span>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <img src="https://flagcdn.com/w20/bd.png" alt="BD" class="w-5 h-4">
                    <span class="text-sm">EN</span>
                </div>
                <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-gray-900">Sign In</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-cover bg-center h-64"
        style="background-image: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200');">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="relative container mx-auto px-4 h-full flex flex-col items-center justify-center text-center">
            <h1 class="text-white text-4xl font-bold mb-4">Recommend Products. Earn Commissions.</h1>
            <a href="{{route('register')}}" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold px-8 py-3 rounded-md transition">Sign up</a>
        </div>
    </section>

    <!-- Program Info -->
    <section class="bg-gradient-to-r from-blue-400 to-blue-500 py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col items-center justify-start mb-6 gap-2">
                <h2 class="text-white text-2xl font-bold">Right Associate</h2>
                <h3 class="text-yellow-400 text-xl font-semibold">Right BD's Affiliate Marketing Program</h3>
            </div>
            <p class="text-white text-center text-sm ">
                Welcome to the leading affiliate marketing programs in Bangladesh. The Right Associates Program helps
                Customers, content creators, and bloggers monetize their traffic. With thousands of products and
                programs available on Right, associates use easy link-building tools to direct their audience to their
                recommendations, and earn from qualifying purchases.
            </p>
        </div>
    </section>

    <!-- Features -->
    <section class="container mx-auto px-4 py-12">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Sign Up -->
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <div class="bg-yellow-100 p-6 rounded-full">
                        <svg class="w-12 h-12 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold mb-3">Sign up</h3>
                <p class="text-gray-600 text-sm">Join thousands of affiliate marketers, fb marketers and bloggers who
                    are earning with the Right Associates Program</p>
            </div>

            <!-- Recommend -->
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <div class="bg-orange-100 p-6 rounded-full">
                        <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold mb-3">Recommend</h3>
                <p class="text-gray-600 text-sm">Share products with your audience. We have customized linking tools
                    for, individual marketers, bloggers and social media users.</p>
            </div>

            <!-- Earn -->
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <div class="bg-yellow-100 p-6 rounded-full">
                        <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold mb-3">Earn</h3>
                <p class="text-gray-600 text-sm">Earn affiliate and leads Commission from qualifying purchase.</p>
            </div>
        </div>
    </section>

    <!-- Login Form -->
    <section class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-center mb-2">Log In</h2>
            <p class="text-gray-600 text-sm text-center mb-6">Enter your email and password to log in</p>

            <form class="space-y-4" method="POST" action="{{ route('login.otp') }}">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <div class="relative">
                        <input type="text" placeholder="name@gmail.com" name="login"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                    <div class="relative">
                        <input type="password" placeholder="Enter your password" name="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button type="button" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center">
                        <input type="checkbox" class="mr-2 rounded">
                        <span class="text-gray-600">Keep me logged in</span>
                    </label>
                    <a href="#" class="text-blue-500 hover:underline">Forgot password?</a>
                </div>

                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-md transition">
                    Sign in
                </button>

                <p class="text-center text-sm text-gray-600">
                    Don't have an account? <a href="#" class="text-blue-500 hover:underline">Sign Up</a>
                </p>
            </form>
        </div>
    </section>

    <!-- Features Banner -->
    <section class="bg-orange-500 py-4">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-white">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <div>
                        <div class="font-bold">FAST SHIPPING</div>
                        <div class="text-sm opacity-90">Delivery Information</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <div>
                        <div class="font-bold">ONLINE PAYMENT</div>
                        <div class="text-sm opacity-90">Secure Payment</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <div>
                        <div class="font-bold">100% SAFE</div>
                        <div class="text-sm opacity-90">Money back benefits</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <div>
                        <div class="font-bold">24/7 SUPPORT</div>
                        <div class="text-sm opacity-90">Quick Online Support</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <!-- About -->
                <div>
                    <h3 class="font-bold text-lg mb-4">About Online Shop</h3>
                    <p class="text-gray-400 text-sm mb-4">Right BD is a trusted platform for online shopping. Here, you
                        will find high-quality products at affordable prices. You can also discuss your skin concerns
                        with us in detail.</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                            <span class="text-sm">01309-906977,</span>
                            <span class="text-sm">01806-633336</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            <span class="text-sm">admn.rightbd@gmail.com</span>
                        </div>
                    </div>
                </div>

                <!-- Information -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Information</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">My Account</a></li>
                        <li><a href="#" class="hover:text-white">Wishlist</a></li>
                        <li><a href="#" class="hover:text-white">Cart Page</a></li>
                        <li><a href="#" class="hover:text-white">Checkout</a></li>
                        <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white">Terms & Condition</a></li>
                        <li><a href="#" class="hover:text-white">Refund & Return Policy</a></li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">Home</a></li>
                        <li><a href="#" class="hover:text-white">Shop</a></li>
                        <li><a href="#" class="hover:text-white">About us</a></li>
                        <li><a href="#" class="hover:text-white">Contact us</a></li>
                        <li><a href="#" class="hover:text-white">Blog</a></li>
                    </ul>
                </div>

                <!-- Payment Links -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Payment Links</h3>
                    <div class="mb-4">
                        <p class="text-sm text-gray-400 mb-2">Payment System:</p>
                        <div class="flex flex-wrap gap-2">
                            <div class="bg-white px-2 py-1 rounded text-xs font-semibold text-gray-800">Cash On
                                Delivery</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Copyright -->
    <div class="bg-gray-950 text-gray-400 text-center py-4">
        <p class="text-sm">© 2025 Right BD. All Rights Reserved.</p>
    </div>
</body>

</html>
