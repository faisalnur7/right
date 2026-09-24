<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light right-top-nav" style="position: sticky; top: 0;">
    <!-- Left navbar links -->
    <ul class="navbar-nav right-top-nav__left">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        @php
            use Carbon\Carbon;
            use Illuminate\Support\Str;
            $saleLogs = App\Models\SaleLog::all();
            $settings = App\Models\GeneralSetting::first();
            $saleLogCount = count($saleLogs);
            $primeBalance = optional(auth()->user()->primeWallet)->balance ?? 0;
            $affiliateBalance = optional(auth()->user()->affiliateWallet)->balance ?? 0;

            $startDate = Carbon::parse($settings->business_start_date)->startOfDay();
            $now = Carbon::now()->startOfDay();
            $daysPassed = $startDate->diffInDays($now) + 1;

            $packageUser = auth()->user()->activePackage ? auth()->user()->activePackage->first() : null;

            $daysRemaining = null;

            if ($packageUser) {
                $expiresAt = Carbon::parse($packageUser->pivot->expires_at)->startOfDay();
                $today = Carbon::now()->startOfDay();
                $daysRemaining = $today->diffInDays($expiresAt, false);
            }

            $badgeDesign = 'right-top-nav__metric';
            $badgeTextDesign = 'right-top-nav__metric-value';
            $notifications = App\Models\Notification::with('user')
                ->where(function ($q) {
                    $q->where('user_id', auth()->id())->orWhereNull('user_id');
                })
                ->where('is_for_admin', 0)
                ->latest()
                ->get();

            $unreadNotifications = App\Models\Notification::with('user')
                ->where(function ($q) {
                    $q->where('user_id', auth()->id())->orWhereNull('user_id');
                })
                ->where('is_for_admin', 0)
                ->where('is_read', 0)
                ->count();

        @endphp
        <div class="flex gap-0 md:ml-4 right-top-nav__sales">
            @if (!empty(auth()->user()) && auth()->user()->is_active && !empty(auth()->user()->saleLogUnits))
                @php $authUser = auth()->user(); @endphp
                @foreach ($saleLogs as $key => $saleLog)
                    @php
                        $available_units = $authUser->getRemainingUnits($saleLog->id);
                        $isActive = !empty($authUser->isActiveInSaleLog($saleLog->id)) ? 1 : 0;
                        $isActiveInTree = !empty($authUser->isActiveInSaleLogTree($saleLog->id)) ? 1 : 0;
                        $activeStyle = 'bg-red-600';
                        $roundedRight = $key == $saleLogCount - 1 ? 'md:rounded-r-3xl' : '';
                        $roundedLeft = $key == 0 ? 'md:rounded-l-3xl' : '';

                        $isActive = $authUser->isActiveInSaleLogTreeAdmin($saleLog->id);
                        $activeStyle = 'bg-red-600';

                        if ($isActive == App\Models\BinaryTreeNode::ACTIVE_IN_TREE) {
                            $activeStyle = 'bg-green-600';
                            $activeStyleText = 'text-green-600';
                        } elseif ($isActive == App\Models\BinaryTreeNode::INACTIVE_PENDING_IN_TREE) {
                            $activeStyle = 'bg-yellow-600';
                            $activeStyleText = 'text-yellow-600';
                        } elseif ($isActive == App\Models\BinaryTreeNode::INACTIVE_IN_TREE) {
                            $activeStyle = 'bg-red-600';
                            $activeStyleText = 'text-red-600';
                        }

                    @endphp
                    <a href="{{ route('prime_stock') }}?sale_log_id={{ $saleLog->id }}"
                        class="btn px-1 py-0 md:!px-2 md:!py-0 rounded-md btn-shine border-shine {{ $activeStyleText }} font-semibold text-xs md:text-sm lg:text-base flex gap-1 md:gap-2 justify-center items-center hover:opacity-90 focus:ring-2 !bg-transparent !border-transparent">

                        <!-- Sale log name with activeStyle text color -->
                        <span class="{{ $activeStyleText }} text-md">
                            {{ $saleLog->name }}
                        </span>

                        <!-- Badge with activeStyle background -->
                        <span
                            class="badge {{ $activeStyle }} text-xs md:text-sm lg:text-base flex justify-center items-center rounded-full h-5 w-5 text-white">
                            {{ $available_units }}
                        </span>
                    </a>
                @endforeach
            @endif
        </div>

        @if (!empty(auth()->user()->user_affiliate_type))
            <div class="hidden md:flex gap-0 right-top-nav__wallets">
                {{-- Wallet buttons --}}

                @if (auth()->user()->user_affiliate_type == App\Models\User::PRIME)
                    <div class="{{ $badgeDesign }}">
                        <span class="text-md">Prime Wallet</span>
                        <span class="{{ $badgeTextDesign }}">{{ number_format($primeBalance, 2) }}</span>
                    </div>
                @endif

                <div class="{{ $badgeDesign }}">
                    <span class="text-md">Affiliate Wallet</span>
                    <span class="{{ $badgeTextDesign }}">{{ number_format($affiliateBalance, 2) }}</span>
                </div>
            </div>

            <div class="md:flex gap-0 right-top-nav__business-days">
                {{-- Wallet buttons --}}
                <div class="{{ $badgeDesign }}">
                    <span class="text-md">BD</span>
                    <span class="{{ $badgeTextDesign }}">{{ $daysPassed }}</span>
                </div>
            </div>

            @if (auth()->user()->user_affiliate_type == App\Models\User::PRIME)
                <div class="hidden md:flex gap-0 right-top-nav__membership">
                    <div class="{{ $badgeDesign }}">
                        <span class="text-md">Days</span>
                        <span class="{{ $badgeTextDesign }}">{{ $daysRemaining }}</span>
                    </div>
                </div>
            @else
                <div class="hidden md:flex gap-0 right-top-nav__membership">
                    <a href="{{ route('kyc.prime') }}"
                        class="btn bg-yellow-500 px-4 rounded-3xl ml-4 btn-shine border-shine text-white font-semibold text-md flex gap-2 justify-center items-center hover:opacity-90 focus:ring-2 right-top-nav__go-prime">
                        <span>Go Prime</span>
                        <span class="badge bg-white text-md text-green-700">{{ $daysRemaining }}</span>
                    </a>
                </div>
            @endif
        @endif

    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto right-top-nav__tools">
        <!-- Navbar Search -->
        @if (auth()->user()->prime_verified == App\Models\User::PRIME_VERIFIED_STATUS_COMPLETED &&
                !(request()->routeIs('cart') || request()->routeIs('prime_checkout')))
            <li class="nav-item dropdown shopping_cart" id="shopping_cart">
                @include('layouts.partials._top_cart')
            </li>
        @endif

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link relative" id="notificationBell" data-toggle="dropdown" href="#">
                <i class="far fa-bell text-lg"></i>
                <span id="notificationCount"
                    class="badge badge-danger rounded-full navbar-badge absolute w-auto min-w-5 h-5 flex justify-center items-center top-0 right-0 font-bold text-xs"
                    title="15">{{ $unreadNotifications }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">
                    <span id="notificationHeader">{{ count($notifications) ?? 0 }}</span> Notifications
                </span>
                <div class="dropdown-divider"></div>

                <div id="notificationList" class="max-h-80 overflow-y-auto divide-y divide-gray-200">
                    {{-- Notifications will be loaded here via AJAX --}}
                </div>

                <div class="dropdown-divider"></div>
                <a href="{{ route('notifications.index') }}" class="dropdown-item dropdown-footer">See All
                    Notifications</a>
            </div>

        </li>

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="{{ asset('assets/dist/img/user1-128x128.jpg') }}" alt="User Avatar"
                            class="img-size-50 mr-3 img-circle">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                {{ auth()->user()->name }}
                            </h3>
                            <p class="text-sm">Role</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>

                <a href="{{ route('editUserProfile') }}" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> Profile Setting
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item cursor-pointer" id="customer-logout-btn">
                    <i class="fas fa-power-off mr-2"></i> Logout
                </a>

                <form id="customer_logout_form" style="display:none" action="{{ route('logout') }}" method="POST">
                    @csrf
                </form>
                <script>
                    $(document).on('click', '#customer-logout-btn', function() {
                        $("#customer_logout_form").submit();
                    })
                </script>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
<script>
    $(document).ready(function() {
        let lastUnreadCount = 0;
        let notificationInterval = null;

        // ✅ Toastr setup (optional styling)
        toastr.options = {
            positionClass: 'toast-bottom-right',
            timeOut: 4000,
            progressBar: true,
            closeButton: true,
        };

        // ✅ Function to load notifications
        function loadNotifications(showToast = false) {
            $.ajax({
                url: "{{ route('notifications.unread.user') }}",
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Update notification dropdown and badge
                    $('#notificationList').html(data.html);
                    $('#notificationCount').text(data.unreadCount);
                    $('#notificationHeader').text(`${data.unreadCount} Notifications`);

                    // ✅ If new notifications are found, show toast
                    if (showToast && data.unreadCount > lastUnreadCount) {
                        const newCount = data.unreadCount - lastUnreadCount;
                        toastr.success(
                            `You have ${newCount} new notification${newCount > 1 ? 's' : ''}!`
                        );
                    }

                    lastUnreadCount = data.unreadCount;
                },
                error: function(err) {
                    console.error('Error loading notifications:', err);
                }
            });
        }

        // ✅ Start polling
        function startPolling() {
            if (!notificationInterval) {
                notificationInterval = setInterval(() => {
                    if (document.visibilityState === 'visible') {
                        loadNotifications(true);
                    }
                }, 2000); // every 20 seconds
            }
        }

        // ✅ Stop polling
        function stopPolling() {
            if (notificationInterval) {
                clearInterval(notificationInterval);
                notificationInterval = null;
            }
        }

        // ✅ Pause/resume when tab visibility changes
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                startPolling();
                loadNotifications();
            } else {
                stopPolling();
            }
        });

        // ✅ Load immediately when page loads
        loadNotifications();

        // ✅ Begin polling
        startPolling();

        // ✅ Manual reload on bell icon click
        $('#notificationBell').on('click', function() {
            loadNotifications();
        });
    });
</script>
