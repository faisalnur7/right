@extends('layouts.master')

@section('contents')
    <section class="w-full">
        <div class="px-6">
            <div class="row">
                <div class="col-lg-6 col-6">
                    <!-- Affiliate Wallet -->
                    <div
                        class="small-box bg-gradient-to-r from-sky-500 via-sky-600 to-sky-700 text-white p-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 m-0">
                        <div class="flex items-center justify-between">
                            <!-- Left side (icon + label) -->
                            <div class="flex flex-col gap-2 items-start">
                                <i class="fas fa-wallet text-xl md:text-2xl opacity-90"></i>
                                <p class="text-sm md:text-base opacity-100 flex items-center gap-2 ">
                                    <span
                                        class="w-6 h-6 md:w-7 md:h-7 flex items-center justify-center rounded-full bg-white text-gray-700 font-bold">
                                        A
                                    </span>
                                    Wallet
                                </p>
                            </div>
                            <!-- Right side (amount) -->
                            <h3 class="text-lg md:text-2xl font-bold leading-tight">
                                ৳{{ number_format($affiliateBalance, 0) }}
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-6">
                    <!-- Prime Wallet -->
                    <a href="{{ route('prime_transactions') }}">
                        <div
                            class="small-box bg-gradient-to-r from-sky-500 via-sky-600 to-sky-700 text-white p-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 m-0">
                            <div class="flex items-center justify-between">
                                <!-- Left side (icon + label) -->
                                <div class="flex flex-col gap-2 items-start">
                                    <i class="fas fa-wallet text-xl md:text-2xl opacity-90"></i>

                                    <p class="text-sm md:text-base opacity-100 flex items-center gap-2 ">
                                        <span
                                            class="w-6 h-6 md:w-7 md:h-7 flex items-center justify-center rounded-full bg-white text-gray-700 font-bold">
                                            P
                                        </span>
                                        Wallet
                                    </p>
                                </div>
                                <!-- Right side (amount) -->
                                <h3 class="text-lg md:text-2xl font-bold leading-tight">
                                    ৳{{ number_format($primeBalance, 0) }}
                                </h3>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>

        <div class="px-6 mt-6">
            <div class="row">
                @foreach ($saleLogs as $saleLog)
                    <div class="col-6 col-md-3">
                        <div
                            class="info-box gap-4 bg-gradient-to-r from-sky-500 via-sky-600 to-sky-700 text-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300">

                            <!-- Solid icon with no background -->
                            <span class="info-box-icon p-0 m-0">
                                <i class="fas fa-dolly text-3xl"></i>
                            </span>

                            <div class="flex justify-between w-full items-center">
                                <span class="info-box-text text-base md:text-lg font-semibold">{{ $saleLog->name }}</span>
                                <span
                                    class="info-box-number text-lg md:text-2xl font-bold pr-3">{{ $saleLog->count }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Days Remaining -->
                <div class="col-6 col-md-3">
                    <div
                        class="info-box gap-4 bg-gradient-to-r from-rose-500 via-rose-600 to-rose-700 text-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300">

                        <!-- Solid icon with no background -->
                        <span class="info-box-icon p-0 m-0">
                            <i class="fas fa-hourglass-half text-3xl"></i>
                        </span>

                        <div class="flex justify-between w-full items-center">
                            <span class="info-box-text text-base md:text-lg font-semibold">Days</span>
                            <span class="info-box-number text-lg md:text-2xl font-bold pr-3">{{ $daysRemaining }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Left Column -->
                <div class="space-y-3">

                    <!-- Accordion Header -->
                    <div
                        class="accordion-header bg-gradient-to-r from-sky-500 via-sky-600 to-sky-700 text-white rounded-xl shadow-md p-4 hover:shadow-xl transition-all duration-300 flex justify-between items-center cursor-pointer">

                        <span class="font-semibold text-base md:text-lg">Today's Income</span>

                        <div class="flex items-center gap-3">
                            <span class="font-bold text-lg md:text-2xl border p-1 rounded-md">
                                ৳{{ number_format($todayIncome, 2) }}
                            </span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <!-- Accordion Body -->
                    <div class="accordion-body flex flex-col gap-4 mt-2">
                        <div
                            class="bg-white rounded-xl shadow-md p-[16px] hover:shadow-lg transition-all duration-300 flex justify-between items-center border border-sky-200">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-paperclip text-blue-900 text-3xl"></i>
                                <span class="text-gray-700 font-bold">Subscription Income</span>
                            </div>
                            <span class="font-semibold text-gray-900 border p-1 rounded-md">
                                ৳{{ number_format($todaySubscriptionIncome, 2) }}
                            </span>
                        </div>

                        <div
                            class="bg-white rounded-xl shadow-md p-[16px] hover:shadow-lg transition-all duration-300 flex justify-between items-center border border-sky-200">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-hands-helping text-yellow-600 text-3xl"></i>
                                <span class="text-gray-700 font-bold">Affiliate Income</span>
                            </div>
                            <span class="font-semibold text-gray-900 border p-1 rounded-md">
                                ৳{{ number_format($todayAffiliateIncome, 2) }}
                            </span>
                        </div>

                        <div
                            class="bg-white rounded-xl shadow-md p-[16px] hover:shadow-lg transition-all duration-300 flex justify-between items-center border border-sky-200">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-star text-blue-600 text-3xl"></i>
                                <span class="text-gray-700 font-bold">Leads Income</span>
                            </div>
                            <span class="font-semibold text-gray-900 border p-1 rounded-md">
                                ৳{{ number_format($todayLeadsIncome, 2) }}
                            </span>
                        </div>
                        @if (auth()->user()->is_super_prime > 0)
                            <div
                                class="bg-white rounded-xl shadow-md p-[16px] hover:shadow-lg transition-all duration-300 flex justify-between items-center border border-sky-200">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-credit-card text-blue-600 text-3xl"></i>
                                    <span class="text-gray-700 font-bold">Associate Income</span>
                                </div>
                                <span class="font-semibold text-gray-900 border p-1 rounded-md">
                                    ৳{{ number_format($todayAssociateCommission, 2) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>


                <!-- Right Column -->
                <div class="space-y-3">

                    <div
                        class="bg-gradient-to-r from-sky-500 via-sky-600 to-sky-700 text-white rounded-xl shadow-md p-4 hover:shadow-xl transition-all duration-300 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-coins text-3xl"></i>
                            <span class="font-semibold text-base md:text-lg">Total Income</span>
                        </div>
                        <span class="font-bold text-lg md:text-2xl border p-1 rounded-md">
                            ৳{{ number_format($totalIncome, 2) }}
                        </span>
                    </div>

                    <div
                        class="bg-gradient-to-r from-red-500 via-red-600 to-red-700 text-white rounded-xl shadow-md p-4 hover:shadow-xl transition-all duration-300 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-hand-holding-usd text-3xl"></i>
                            <span class="font-semibold text-base md:text-lg">Disbursement</span>
                        </div>
                        <span class="font-bold text-lg md:text-2xl border p-1 rounded-md">
                            ৳{{ number_format($totalDisbursement, 2) }}
                        </span>
                    </div>

                </div>



            </div>
        </div>

    </section>
@endsection

@section('scripts')
    <!-- jQuery Script -->
    <script>
        $(document).ready(function() {
            $('.accordion-body').show(); // Hide initially
            $('.accordion-header').click(function() {
                $(this).next('.accordion-body').slideToggle(300);
                $(this).find('i.fas').toggleClass('fa-chevron-down fa-chevron-up');
            });
        });
    </script>
@endsection
