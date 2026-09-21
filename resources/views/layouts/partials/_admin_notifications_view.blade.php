@forelse ($notifications as $notification)
    <a href="{{ route('admin.notifications.redirect', $notification->id) }}"
       class="flex items-start gap-3 px-3 py-3 hover:bg-gray-100 transition duration-200">

        {{-- Icon Circle --}}
        <div
            class="relative flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br 
                   {{ $notification->is_read_by_admin ? 'from-gray-200 to-gray-300' : 'from-blue-500 to-blue-600' }}
                   flex items-center justify-center text-white shadow-sm">
            <i class="fas {{ $notification->is_read_by_admin ? 'fa-envelope-open' : 'fa-envelope' }} text-lg"></i>
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-center">
                <h6 class="text-sm font-semibold text-gray-800 truncate">
                    {{ Str::limit($notification->title ?? 'Notification', 40) }}
                </h6>
                <span class="text-xs text-gray-400 whitespace-nowrap">
                    {{ $notification->time ?? optional($notification->created_at)->diffForHumans() }}
                </span>
            </div>

            <p class="text-sm text-gray-600 mt-1 leading-tight">
                {{ Str::limit(strip_tags($notification->message ?? ''), 60) }}
            </p>

            @if (!$notification->is_read_by_admin)
                <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium text-blue-600 bg-blue-100 rounded-full">
                    New
                </span>
            @endif
        </div>
    </a>
@empty
    <div class="text-center py-6 text-gray-500 text-sm">
        <i class="fas fa-bell-slash text-lg mb-1"></i>
        <p>No notifications yet</p>
    </div>
@endforelse
