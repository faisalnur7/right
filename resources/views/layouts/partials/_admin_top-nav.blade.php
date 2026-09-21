<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light" style="position: sticky; top: 0;">
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Str;
        $settings = App\Models\GeneralSetting::first();
        $startDate = Carbon::parse($settings->business_start_date)->startOfDay();
        $now = Carbon::now()->startOfDay();
        $daysPassed = $startDate->diffInDays($now) + 1;
        $badgeDesign =
            'btn bg-[#252f51] bg-transparent text-gray-800 px-2 md:px-4 rounded-md ml-4 btn-shine border-shine  font-semibold text-xs md:text-sm lg:text-base flex gap-1 md:gap-2 justify-center items-center hover:opacity-90 focus:ring-2';
        $badgeTextDesign = 'badge text-[#252f51] bg-white border text-md md:text-sm lg:text-base rounded-lg';

        $notifications = App\Models\Notification::with('user')->where('is_for_admin', 1)->latest()->get();

        $unreadNotifications = App\Models\Notification::with('user')
            ->where('is_for_admin', 1)
            ->where('is_read_by_admin', 0)
            ->count();

    @endphp
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Contact</a>
        </li>


        <div class="md:flex gap-0">
            {{-- Wallet buttons --}}
            <div class="{{ $badgeDesign }}">
                <span class="text-md">BD</span>
                <span class="{{ $badgeTextDesign }}">{{ $daysPassed }}</span>
            </div>
        </div>

    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->

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

                <a href="#" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> Profile Setting
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item cursor-pointer" id="logout-btn">
                    <i class="fas fa-power-off mr-2"></i> Logout
                </a>

                <form id="logout_form" style="display:none" action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                </form>
                <script>
                    $(document).on('click', '#logout-btn', function() {
                        $("#logout_form").submit();
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

        // ✅ Load notifications via AJAX
        function loadNotifications(showToast = false) {
            $.ajax({
                url: "{{ route('notifications.unread') }}",
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Update UI
                    $('#notificationList').html(data.html);
                    $('#notificationCount').text(data.unreadCount);
                    $('#notificationHeader').text(`${data.unreadCount} Notifications`);

                    // ✅ Show toast only when new notifications appear
                    if (showToast && data.unreadCount > lastUnreadCount) {
                        const newCount = data.unreadCount - lastUnreadCount;
                        toastr.success(
                            `You have ${newCount} new notification${newCount > 1 ? 's' : ''}!`
                        );
                    }

                    lastUnreadCount = data.unreadCount;
                },
                error: function(err) {
                    console.error('Notification fetch error:', err);
                }
            });
        }

        // ✅ Start polling
        function startPolling() {
            if (!notificationInterval) {
                notificationInterval = setInterval(() => {
                    // Only run if the tab is visible
                    if (document.visibilityState === 'visible') {
                        loadNotifications(true);
                    }
                }, 2000); // 20 seconds
            }
        }

        // ✅ Stop polling
        function stopPolling() {
            if (notificationInterval) {
                clearInterval(notificationInterval);
                notificationInterval = null;
            }
        }

        // ✅ Handle tab visibility change
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                // User is active again
                startPolling();
                loadNotifications();
            } else {
                // User switched tabs
                stopPolling();
            }
        });

        // ✅ Load immediately when page loads
        loadNotifications();

        // ✅ Start polling initially
        startPolling();

        // ✅ Manual reload on bell click
        $('#notificationBell').on('click', function() {
            loadNotifications();
        });
    });
</script>


