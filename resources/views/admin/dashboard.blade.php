@extends('layouts.admin_master')

@section('contents')
    @include('layouts.partials._clock')

    <div class="w-full space-y-6 px-6 pb-6">

        {{-- === Users Section === --}}
        <div class="mb-6">
            <h4 class="text-lg font-semibold text-gray-700 mb-3"><i class="fas fa-chart-pie mr-2"></i>
                User Statistics

                @if ($todaysUsers > 0)
                    <span class="text-blue-600 text-lg ml-6 gap-1">
                        <i class="fas fa-star animate-bounce-rotate-shine text-lg"></i>
                        Good News!!
                        <span class="text-red-500 font-extrabold">{{ $todaysUsers }}</span> New Users Joined Today!!!
                        <i class="fas fa-star animate-bounce-rotate-shine text-lg"></i>
                    </span>
                @endif
            </h4>

            @php
                $userCards = [
                    'total' => ['label' => 'Total Users', 'color' => 'indigo-600', 'icon' => 'fa-users'],
                    'active' => ['label' => 'Active Users', 'color' => 'blue-400', 'icon' => 'fa-user-check'],
                ];
                $colors = ['indigo-500', 'amber-500', 'fuchsia-500'];
                $icons = ['fa-user-tag', 'fa-user-friends', 'fa-user-clock'];
                $i = 0;
                foreach ($users as $key => $count) {
                    if (!in_array($key, ['total', 'active'])) {
                        $userCards[$key] = [
                            'label' => strtoupper($key) . ' Users',
                            'color' => $colors[$i % count($colors)],
                            'icon' => $icons[$i % count($icons)],
                        ];
                        $i++;
                    }
                }
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach ($userCards as $key => $card)
                    <div
                        class="bg-gradient-to-r from-sky-500 via-sky-600 to-sky-700 text-white rounded-lg shadow p-4 hover:translate-y-1 transition-transform relative">
                        <div class="text-lg font-bold">{{ $users[$key] ?? 0 }}</div>
                        <div class="text-md font-bold mt-1">{{ $card['label'] }}</div>
                        <i class="fas {{ $card['icon'] }} text-2xl absolute right-3 top-3 opacity-20"></i>
                    </div>
                @endforeach
            </div>
        </div>
        {{-- === Today's Sales & Monthly Chart === --}}
        <div class="flex flex-col md:flex-row gap-6">

            {{-- Today's Sales --}}
            <div class="md:w-1/2 flex flex-col gap-6">
                <div class="space-y-3">

                    <!-- Accordion Header -->
                    <div
                        class="accordion-header bg-gradient-to-r from-sky-500 via-sky-600 to-sky-700 text-white rounded-xl shadow-md p-4 hover:shadow-xl transition-all duration-300 flex justify-between items-center cursor-pointer">

                        <span class="font-semibold text-base md:text-lg">Commissions</span>

                        <div class="flex items-center gap-3">
                            <span class="font-bold text-lg md:text-2xl border p-1 rounded-md">
                                ৳{{ number_format(array_sum($commissions), 0) }}
                            </span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <!-- Accordion Body -->
                    <div class="accordion-body flex flex-col gap-4 mt-2">
                        @php
                            $commissionCards = [
                                'associate' => ['label' => 'Associate Commission', 'icon' => 'fa-handshake'],
                                'subscription' => ['label' => 'Subscription Commission', 'icon' => 'fa-credit-card'],
                                'affiliate' => ['label' => 'Affiliate Commission', 'icon' => 'fa-network-wired'],
                                'leads' => ['label' => 'Leads Commission', 'icon' => 'fa-coins'],
                            ];
                        @endphp

                        @foreach ($commissionCards as $key => $card)
                            <div
                                class="bg-white rounded-xl shadow-md p-[16px] hover:shadow-lg transition-all duration-300 flex justify-between items-center border border-sky-200">
                                <div class="flex items-center gap-3">
                                    <i class="fas {{ $card['icon'] }} text-blue-900 text-3xl"></i>
                                    <span class="text-gray-700 font-bold">{{ $card['label'] }}</span>
                                </div>
                                <span class="font-semibold text-lg text-gray-900 border p-1 rounded-md">
                                    ৳{{ number_format($commissions[$key] ?? 0, 0) }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                </div>

            </div>

            <div class="md:w-1/2 flex flex-col gap-6">
                <div class="space-y-3">

                    <!-- Accordion Header -->
                    <div
                        class="accordion-header bg-gradient-to-r from-sky-500 via-sky-600 to-sky-700 text-white rounded-xl shadow-md p-4 hover:shadow-xl transition-all duration-300 flex justify-between items-center cursor-pointer">

                        <span class="font-semibold text-base md:text-lg">Today's Sale</span>

                        <div class="flex items-center gap-3">
                            <span class="font-bold text-lg md:text-2xl border p-1 rounded-md">
                                ৳{{ number_format(($todayBySaleLog->sum('total_subtotal') + $adminSubscriptionIncome), 0) }}
                            </span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <!-- Accordion Body -->
                    <div class="accordion-body flex flex-col gap-4 mt-2">
                        @foreach ($todayBySaleLog as $log)
                            @php
                                $name = strtoupper($log->name);
                                $icons = [
                                    'PB' => ['icon' => 'fas fa-box', 'label' => 'PB'],
                                    'PS' => ['icon' => 'fas fa-box', 'label' => 'PS'],
                                    'PE' => ['icon' => 'fas fa-box', 'label' => 'PE'],
                                ];
                                $icon = $icons[$name]['icon'] ?? 'fas fa-cash-register';
                                $label = $icons[$name]['label'] ?? $name . ' Income';
                            @endphp

                            <div
                                class="bg-white rounded-xl shadow-md p-[16px] hover:shadow-lg transition-all duration-300 flex justify-between items-center border border-sky-200">
                                <div class="flex items-center gap-3">
                                    <i class="{{ $icon }} text-blue-900 text-3xl"></i>
                                    <span class="text-gray-700 font-bold">{{ $label }}</span>
                                </div>
                                <span class="font-semibold text-lg text-gray-900 border p-1 rounded-md">
                                    ৳{{ number_format($log->total_subtotal, 0) }}
                                </span>
                            </div>
                        @endforeach

                        <div
                            class="bg-white rounded-xl shadow-md p-[16px] hover:shadow-lg transition-all duration-300 flex justify-between items-center border border-sky-200">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-star text-blue-900 text-3xl"></i>
                                <span class="text-gray-700 font-bold">Subscription</span>
                            </div>
                            <span class="font-semibold text-lg text-gray-900 border p-1 rounded-md">
                                ৳{{ number_format($adminSubscriptionIncome, 0) }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Monthly Sales Chart --}}
            <div class="md:w-full flex flex-col">
                <div class="bg-white rounded-lg shadow-lg p-4 flex-1">
                    <canvas id="monthlySalesChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <div class="mb-6 flex gap-6">
            {{-- === Leads Section === --}}
            <div class="w-full">
                <h4 class="text-lg font-semibold text-gray-700 mb-3">
                    <i class="fas fa-chart-pie mr-2"></i>Today's Leads
                </h4>

                @php
                    $leadColors = ['indigo-500', 'emerald-500', 'amber-500'];
                    $leadIcons = ['fa-bullseye', 'fa-crosshairs', 'fa-target'];
                    $i = 0;
                    $totalLeads = array_sum($leads);
                @endphp

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    {{-- Total Leads --}}
                    <div
                        class="bg-gradient-to-r from-sky-500 via-sky-600 to-sky-700 text-white rounded-lg shadow p-4 hover:translate-y-1 transition-transform relative">
                        <div class="text-base text-lg font-bold">{{ $totalLeads }}</div>
                        <div class=" text-md font-bold mt-1">Total Leads</div>
                        <i class="fas fa-chart-line text-2xl absolute right-3 top-3 opacity-20"></i>
                    </div>
                    @foreach ($leads as $key => $count)
                        @php
                            $color = $leadColors[$i % count($leadColors)];
                            $icon = $leadIcons[$i % count($leadIcons)];
                            $i++;
                        @endphp
                        <div
                            class="bg-gradient-to-r from-sky-500 via-sky-600 to-sky-700 text-white rounded-lg shadow p-4 hover:translate-y-1 transition-transform relative">
                            <div class="text-base text-lg font-bold">{{ $count }}</div>
                            <div class=" text-md font-bold mt-1">{{ strtoupper($key) }}</div>
                            <i class="fas {{ $icon }} text-2xl absolute right-3 top-3 opacity-20"></i>
                        </div>
                    @endforeach


                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('monthlySalesChart').getContext('2d');

        const monthlySalesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlySales['months']) !!},
                datasets: [
                    @foreach ($monthlySales['logs'] as $logName => $values)
                        {
                            label: '{{ $logName }} Sales',
                            data: {!! json_encode($values) !!},
                            borderColor: '{{ $monthlySales['colors'][$logName]['border'] ?? '#6b7280' }}',
                            backgroundColor: '{{ $monthlySales['colors'][$logName]['background'] ?? 'rgba(107, 114, 128, 0.6)' }}',
                            tension: 0.3,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                    @endforeach
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                interaction: {
                    mode: 'nearest',
                    intersect: false
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1000
                        }
                    }
                }
            }
        });

        $(document).ready(function() {
            $('.accordion-body').show(); // Hide initially
            $('.accordion-header').click(function() {
                $(this).next('.accordion-body').slideToggle(300);
                $(this).find('i.fas').toggleClass('fa-chevron-down fa-chevron-up');
            });
        });
    </script>
@endsection
