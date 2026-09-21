@php
use App\Models\BinaryTreeNode;
use Illuminate\Support\Str;

$kyc = $node['user']->kyc ?? null;
$username = $node['user']->name ?? 'Unknown';
$photo = $kyc->photo ?? null;
$affiliate_id = $kyc->affiliate_id ?? 'N/A';
$hasPhoto = $node['has_photo'] ?? false;
$position = $node['position'] ?? null;
$level = $node['level'] ?? 0;
$isActive = $node['is_active'];
$isActiveInSL = $node['is_active_in_sale_log'];
$displayName = Str::limit($username, 12, '...');

$positionText = $position == 1 ? 'L' : ($position == 2 ? 'R' : '');
$positionClass = $position == 1 ? '-left-[3px] bg-blue-600 text-white' : ($position == 2 ? '-right-[3px] bg-orange-600 text-white' : '');

$activeStyle = match (true) {
    $isActive == BinaryTreeNode::ACTIVE_IN_TREE && $isActiveInSL == BinaryTreeNode::ACTIVE_IN_TREE => 'border-green-500 bg-gradient-to-br from-green-50 to-green-100',
    $isActive == BinaryTreeNode::INACTIVE_IN_TREE && $isActiveInSL == BinaryTreeNode::ACTIVE_IN_TREE => 'border-amber-500 bg-gradient-to-br from-amber-50 to-amber-100',
    default => 'border-red-500 bg-gradient-to-br from-red-50 to-red-100',
};

$nodeId = "node-{$node['user_id']}-{$level}-" . ($position ?? 0);
$childrenId = "children-{$node['user_id']}-{$level}";
@endphp

<li data-level="{{ $level }}" data-position="{{ $position }}" class="tree-node flex flex-col items-center">
    <div id="{{ $nodeId }}"
         class="node-wrap relative rounded-xl border-2 shadow-md min-w-[120px] max-w-[200px] overflow-hidden transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]
                hover:-translate-y-0.5 hover:shadow-xl {{ $activeStyle }}"
         data-user-id="{{ $node['user_id'] }}"
         data-affiliate-id="{{ $affiliate_id }}"
         data-level="{{ $level }}"
         data-position="{{ $position }}">

        {{-- Node Card --}}
        <div class="p-3 cursor-pointer" onclick="toggleNodeDetails('{{ $nodeId }}')">
            {{-- Position Badge --}}
            @if($positionText)
                <div class="absolute {{ $positionClass }} -top-2 p-1 rounded-full w-7 h-7 flex justify-center items-center text-xs font-bold">{{ $positionText }}</div>
            @endif

            {{-- Avatar + Info --}}
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                    @if($hasPhoto && $photo)
                        <img src="{{ asset('kyc/photo/' . $photo) }}" alt="Avatar" class="w-full h-full object-cover" 
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                    @else
                        <i class="fas fa-user text-gray-500"></i>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="font-semibold text-sm truncate" title="{{ $username }}">{{ $displayName }}</div>
                    <div class="text-xs text-gray-800 font-bold">ID: {{ $node['user_id'] }}</div>
                    <div class="text-xs text-gray-800 font-bold">{{ $affiliate_id }}</div>
                </div>
                <div class="text-xs text-gray-600 flex items-center gap-1">
                    @if($node['immediate_children_count'] == 2)
                        <i class="fas fa-check-circle text-green-500" title="Complete"></i>
                    @elseif($node['immediate_children_count'] == 1)
                        <i class="fas fa-clock text-yellow-500" title="Partial"></i>
                    @else
                        <i class="far fa-circle text-gray-400" title="Empty"></i>
                    @endif
                </div>
            </div>
        </div>

        {{-- Details Panel --}}
        <div class="details-panel hidden p-3 border-t border-gray-200">
            <div class="grid grid-cols-1 gap-2 text-sm">
                <div><span class="font-semibold">{{ $username }}</span></div>
                <div><span class="font-semibold">UID: {{ $node['user_id'] }}</span> </div>
                <div><span class="font-semibold">{{ $affiliate_id }}</span></div>
                <div><span class="font-semibold">Level: {{ $level }}</span></div>
                <div><span class="font-semibold">A # {{ $node['activation_number'] ?? 'N/A' }}</span></div>
            </div>

            <div class="flex gap-2 mt-2">
                <button class="px-2 py-1 bg-blue-500 text-white rounded text-xs flex-1"
                        onclick="loadTree({{ $node['user_id'] }}, {{ $node['node_id'] }}); event.stopPropagation();">
                    <i class="fas fa-sitemap mr-1"></i>
                </button>
                <button class="px-2 py-1 bg-gray-300 text-gray-700 rounded text-xs flex-1"
                        onclick="toggleNodeDetails('{{ $nodeId }}'); event.stopPropagation();">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- Children --}}
    @if(!empty($node['children']))
        <ul id="{{ $childrenId }}" class="">
            @foreach($node['children'] as $child)
                @include('admin.tree.partials.graph_node', ['node' => $child, 'saleLogId' => $saleLogId])
            @endforeach
        </ul>
    @endif
</li>
