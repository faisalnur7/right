@extends('layouts.master')

@section('title', 'Order Placed Successfully')
@section('page_title', 'Order Success')

@section('contents')
    <div class="container mx-auto px-4 py-16">
        <div class="bg-white p-10 rounded-xl shadow-xl max-w-3xl mx-auto border border-green-200 relative overflow-hidden">

            <!-- Confetti Effect -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-green-100 rounded-full opacity-30 animate-pulse"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-green-100 rounded-full opacity-30 animate-ping"></div>

            <div class="flex flex-col items-center text-center relative z-10">
                <svg class="w-24 h-24 text-green-500 mb-6" fill="none" stroke="currentColor" stroke-width="1.5"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>

                <h2 class="text-4xl font-extrabold text-green-700 mb-3">Thank You!</h2>
                <p class="text-lg text-gray-600 mb-6">Your order has been placed successfully.</p>

                <div class="bg-green-50 border border-green-200 text-green-800 rounded-md p-4 w-full max-w-md shadow-sm mb-6">
                    <p class="text-base"><span class="font-semibold">Tracking ID:</span> <span class="tracking-wide">{{ $order->order_tracking_number }}</span></p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('dashboard') }}"
                        class="px-6 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-md shadow hover:bg-indigo-700 transition">
                        Go to Dashboard
                    </a>

                    <a href="#"
                        class="px-6 py-3 bg-white border border-indigo-600 text-indigo-600 text-sm font-semibold rounded-md shadow hover:bg-indigo-50 transition">
                        View Order History
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
