@extends('layouts.master')

@section('hide_page_header', true)

@section('contents')
    @php
        $dashboardUser = auth()->user();
        $isAffiliate = filled($dashboardUser->user_affiliate_type);
        $isPrimeMember = (int) $dashboardUser->user_affiliate_type === App\Models\User::PRIME;
        $activePackage = $dashboardUser->activePackage->first();
        $hasPackage = filled($activePackage);
        $remainingDays = $daysRemaining !== null ? max(0, (int) $daysRemaining) : null;
        $daysTone = $remainingDays === null ? 'neutral' : ($remainingDays <= 7 ? 'warning' : 'success');
        $daysText = $remainingDays === null ? 'No active package' : ($daysRemaining < 0 ? 'Expired' : 'Days remaining');
        $incomeItems = [
            ['label' => 'Subscription income', 'value' => $todaySubscriptionIncome, 'icon' => 'fa-layer-group', 'tone' => 'violet'],
            ['label' => 'Affiliate income', 'value' => $todayAffiliateIncome, 'icon' => 'fa-network-wired', 'tone' => 'blue'],
            ['label' => 'Leads income', 'value' => $todayLeadsIncome, 'icon' => 'fa-bullseye', 'tone' => 'amber'],
        ];
        if ($dashboardUser->is_super_prime > 0) {
            $incomeItems[] = ['label' => 'Associate income', 'value' => $todayAssociateCommission, 'icon' => 'fa-handshake', 'tone' => 'green'];
        }
    @endphp

    <section class="dashboard-page" aria-labelledby="dashboard-title">
        <div class="dashboard-container">
            <header class="dashboard-hero">
                <div class="dashboard-hero__glow"></div>
                <div class="dashboard-hero__content">
                    <span class="dashboard-eyebrow">Member overview</span>
                    <h1 id="dashboard-title">{{ $isAffiliate ? 'Welcome back, ' . $dashboardUser->name . '.' : 'Start your affiliate journey.' }}</h1>
                    <p>{{ $isAffiliate ? 'Keep an eye on your earnings, network and progress from one place.' : 'Complete your affiliate setup to unlock your wallets, earnings and member dashboard.' }}</p>
                    <div class="dashboard-hero__meta">
                        <span><i class="far fa-calendar-alt"></i> {{ now()->format('l, d F Y') }}</span>
                        <span class="dashboard-status dashboard-status--{{ $hasPackage ? 'success' : 'warning' }}">
                            <i class="fas {{ $hasPackage ? 'fa-check-circle' : 'fa-info-circle' }}"></i>
                            {{ $hasPackage ? 'Account active' : 'Complete your activation' }}
                        </span>
                    </div>
                </div>
                <div class="dashboard-hero__actions">
                    @if ($isAffiliate)
                        <a href="{{ route('products') }}" class="dashboard-button dashboard-button--light"><i class="fas fa-arrow-up-right-from-square"></i> Explore products</a>
                        <a href="{{ route('editUserProfile') }}" class="dashboard-button dashboard-button--ghost">Profile settings <i class="fas fa-arrow-right"></i></a>
                    @else
                        <a href="{{ route('kyc.list') }}" class="dashboard-button dashboard-button--light"><i class="fas fa-handshake"></i> Become an Affiliate</a>
                    @endif
                </div>
            </header>

            @if ($isAffiliate)
            <div class="dashboard-section-heading">
                <div>
                    <span class="dashboard-eyebrow">Your finances</span>
                    <h2>Wallet overview</h2>
                </div>
                @if ($isPrimeMember)
                <a href="{{ route('prime_transactions') }}" class="dashboard-text-link">View transactions <i class="fas fa-arrow-right"></i></a>
                @endif
            </div>

            <div class="dashboard-wallet-grid {{ $isPrimeMember ? '' : 'dashboard-wallet-grid--affiliate-only' }}">
                <article class="dashboard-wallet dashboard-wallet--affiliate">
                    <div class="dashboard-wallet__top"><span class="dashboard-icon"><i class="fas fa-wallet"></i></span><span class="dashboard-wallet__tag">Affiliate</span></div>
                    <div class="dashboard-wallet__label">Affiliate wallet</div>
                    <div class="dashboard-wallet__amount">৳{{ number_format($affiliateBalance, 2) }}</div>
                    <div class="dashboard-wallet__footer"><span>Available balance</span><i class="fas fa-chart-line"></i></div>
                </article>
                @if ($isPrimeMember)
                <a href="{{ route('prime_transactions') }}" class="dashboard-wallet dashboard-wallet--prime">
                    <div class="dashboard-wallet__top"><span class="dashboard-icon"><i class="fas fa-gem"></i></span><span class="dashboard-wallet__tag">Prime</span></div>
                    <div class="dashboard-wallet__label">Prime wallet</div>
                    <div class="dashboard-wallet__amount">৳{{ number_format($primeBalance, 2) }}</div>
                    <div class="dashboard-wallet__footer"><span>View transaction history</span><i class="fas fa-arrow-right"></i></div>
                </a>
                <article class="dashboard-package dashboard-package--{{ $daysTone }}">
                    <div class="dashboard-wallet__top"><span class="dashboard-icon"><i class="fas fa-hourglass-half"></i></span><span class="dashboard-wallet__tag">Membership</span></div>
                    <div class="dashboard-wallet__label">{{ $daysText }}</div>
                    <div class="dashboard-package__value">{{ $remainingDays ?? '—' }} <small>{{ $remainingDays !== null ? 'days' : '' }}</small></div>
                    <div class="dashboard-wallet__footer"><span>{{ $hasPackage ? $activePackage->name : 'Choose a package to get started' }}</span><i class="fas fa-arrow-right"></i></div>
                </article>
                @endif
            </div>

            <div class="dashboard-section-heading dashboard-section-heading--metrics">
                <div><span class="dashboard-eyebrow">At a glance</span><h2>Performance snapshot</h2></div>
            </div>
            <div class="dashboard-metrics-grid">
                @if ($isPrimeMember)
                @forelse ($saleLogs as $saleLog)
                    <article class="dashboard-metric-card">
                        <span class="dashboard-metric-card__icon dashboard-metric-card__icon--blue"><i class="fas fa-boxes-stacked"></i></span>
                        <div><span class="dashboard-metric-card__label">{{ $saleLog->name }} activations</span><strong>{{ number_format($saleLog->count) }}</strong></div>
                    </article>
                @empty
                    <article class="dashboard-empty-card"><i class="fas fa-chart-simple"></i><span>No activation activity yet.</span></article>
                @endforelse
                @endif
                <article class="dashboard-metric-card">
                    <span class="dashboard-metric-card__icon dashboard-metric-card__icon--rose"><i class="fas fa-coins"></i></span>
                    <div><span class="dashboard-metric-card__label">Total earned</span><strong>৳{{ number_format($totalIncome, 2) }}</strong></div>
                </article>
                <article class="dashboard-metric-card">
                    <span class="dashboard-metric-card__icon dashboard-metric-card__icon--amber"><i class="fas fa-clock"></i></span>
                    <div><span class="dashboard-metric-card__label">Today’s income</span><strong>৳{{ number_format($todayIncome, 2) }}</strong></div>
                </article>
            </div>

            @if ($isPrimeMember)
            <div class="dashboard-content-grid">
                <section class="dashboard-panel dashboard-income-panel" aria-labelledby="income-title">
                    <div class="dashboard-panel__heading">
                        <div><span class="dashboard-eyebrow">Today</span><h2 id="income-title">Income breakdown</h2></div>
                        <span class="dashboard-total">৳{{ number_format($todayIncome, 2) }} <small>total</small></span>
                    </div>
                    <div class="dashboard-income-list">
                        @foreach ($incomeItems as $income)
                            <div class="dashboard-income-row">
                                <span class="dashboard-income-row__icon dashboard-income-row__icon--{{ $income['tone'] }}"><i class="fas {{ $income['icon'] }}"></i></span>
                                <span class="dashboard-income-row__label">{{ $income['label'] }}</span>
                                <strong>৳{{ number_format($income['value'], 2) }}</strong>
                            </div>
                        @endforeach
                    </div>
                </section>

                <aside class="dashboard-panel dashboard-actions-panel" aria-labelledby="actions-title">
                    <div class="dashboard-panel__heading"><div><span class="dashboard-eyebrow">Shortcuts</span><h2 id="actions-title">Quick actions</h2></div></div>
                    <div class="dashboard-action-list">
                        <a href="{{ route('products') }}"><span class="dashboard-action-icon dashboard-action-icon--blue"><i class="fas fa-bag-shopping"></i></span><span><strong>Browse products</strong><small>Explore the marketplace</small></span><i class="fas fa-arrow-right"></i></a>
                        <a href="{{ route('notifications.index') }}"><span class="dashboard-action-icon dashboard-action-icon--amber"><i class="fas fa-bell"></i></span><span><strong>Notifications</strong><small>See your latest updates</small></span><i class="fas fa-arrow-right"></i></a>
                    </div>
                </aside>
            </div>

            <section class="dashboard-summary-strip">
                <div><span class="dashboard-summary-strip__icon"><i class="fas fa-hand-holding-dollar"></i></span><span><small>Total disbursement</small><strong>৳{{ number_format($totalDisbursement, 2) }}</strong></span></div>
                <span class="dashboard-summary-strip__line"></span>
                <div><span class="dashboard-summary-strip__icon"><i class="fas fa-shield-heart"></i></span><span><small>Account security</small><strong>Protected and active</strong></span></div>
                <a href="{{ route('editUserProfile') }}">Manage account <i class="fas fa-arrow-right"></i></a>
            </section>
            @endif
            @endif
        </div>
    </section>
@endsection
