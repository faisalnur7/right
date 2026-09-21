@extends('layouts.admin_master')

@section('title', 'Binary Tree View')
@section('page_title', 'Binary Tree (Graph View)')

@section('contents')
    <div class="flex flex-col w-full">
        <div class="p-4">
            {{-- Filter Controls --}}
            <form method="GET" action="{{ route('admin.tree.filtered') }}" class="mb-4 flex gap-4 items-end flex-wrap">
                <div class="flex-1 min-w-48">
                    <label class="block font-medium mb-1">Sale Log</label>
                    <select name="sale_log_id" class="form-select rounded border-gray-300 w-full">
                        @foreach ($saleLogs as $log)
                            <option value="{{ $log->id }}" {{ request('sale_log_id') == $log->id ? 'selected' : '' }}>
                                {{ $log->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 min-w-48">
                    <label class="block font-medium mb-1">Affiliate ID</label>
                    <input type="text" name="affiliate_id" class="form-input rounded border-gray-300 w-full"
                        value="{{ request('affiliate_id') }}" placeholder="Enter Affiliate ID">
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-1"></i>
                    </button>
                    <a href="{{ route('adminTreeView', ['sale_log_id' => $saleLogId]) }}"
                        class="px-4 py-2 bg-gray-600 text-white rounded shadow hover:bg-gray-700 transition-colors">
                        <i class="fas fa-undo mr-1"></i>
                    </a>
                </div>
            </form>
            {{-- Tree Statistics --}}
            @if (isset($treeStats))
                <div class="mb-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-4 border">
                    <h3 class="font-bold mb-3 text-gray-800 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                        Tree Statistics
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div class="bg-blue-600 p-3 rounded-lg shadow-md text-white text-center">
                            <div class="text-2xl font-bold">{{ $treeStats['total_nodes'] ?? 0 }}</div>
                            <div class="text-xs opacity-90">Total Users</div>
                        </div>
                        <div class="bg-green-600 p-3 rounded-lg shadow-md text-white text-center">
                            <div class="text-2xl font-bold">{{ $treeStats['active_nodes'] ?? 0 }}</div>
                            <div class="text-xs opacity-90">Active Users</div>
                        </div>
                        <div class="bg-purple-600 p-3 rounded-lg shadow-md text-white text-center">
                            <div class="text-2xl font-bold">{{ $treeStats['max_level'] ?? 0 }}</div>
                            <div class="text-xs opacity-90">Max Depth</div>
                        </div>
                        <div class="bg-orange-600 p-3 rounded-lg shadow-md text-white text-center">
                            <div class="text-2xl font-bold">{{ $count ?? 0 }}/{{ $treeStats['total_nodes'] ?? 0 }}</div>
                            <div class="text-xs opacity-90">Showing</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tree Legend --}}
            <div class="mb-4 p-3 bg-gray-50 rounded-lg border">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                        <span>Active User</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-red-500 rounded"></div>
                        <span>Inactive User</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                        <span>Pending to be inactive</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="w-4 h-4 bg-blue-500 text-white rounded text-xs flex items-center justify-center">L</span>
                        <span>Left Position</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="w-4 h-4 bg-purple-500 text-white rounded text-xs flex items-center justify-center">R</span>
                        <span>Right Position</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-mouse-pointer text-blue-600"></i>
                        <span>Click to expand</span>
                    </div>
                </div>
            </div>

            {{-- Tree Controls --}}
            <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
                <div class="flex gap-2">
                    <button onclick="expandAllNodes()"
                        class="px-3 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700 transition-colors">
                        <i class="fas fa-expand-alt mr-1"></i> Expand
                    </button>
                    <button onclick="collapseAllNodes()"
                        class="px-3 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700 transition-colors">
                        <i class="fas fa-compress-alt mr-1"></i> Collapse
                    </button>
                </div>
            </div>

            {{-- Binary Tree Display --}}
            <div class="tree text-center overflow-x-auto flex flex-col justify-center">
                @if ($tree[0]['parent_node_id'] != 0)
                    <button
                        onclick="loadTree({{ $tree[0]['user_id'] }}, {{ $tree[0]['parent_node_id'] }}); event.stopPropagation();"
                        class="px-3 py-2 bg-transparent text-black max-w-48 mx-auto rounded text-sm hover:bg-blue-700 transition-colors">
                        <i class="fas fa-arrow-up mb-1 text-lg animate-upBreath"></i>
                    </button>
                @endif
                <ul class="tree-root w-full">
                    @foreach ($tree as $node)
                        @include('admin.tree.partials.graph_node', ['node' => $node])
                    @endforeach
                </ul>
            </div>

            @if (($count ?? 0) >= ($limit ?? 0))
                <div class="mt-4 p-3 bg-yellow-100 border border-yellow-300 rounded-lg">
                    <p class="text-yellow-800">
                        <i class="fas fa-info-circle"></i>
                        Tree display limited to {{ $limit }} nodes for performance. Click on any node to view its
                        subtree.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Enhanced Tree Styles for Better Left-Right Visualization */
    </style>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {
            const saleLogId = {{ $saleLogId }};

            // Load subtree
            window.loadTree = function(userId, nodeId) {
                const url =
                    `{{ route('admin.tree.from_user', ['user' => '__user__', 'nodeId' => '__nodeId__']) }}?sale_log_id=${saleLogId}`
                    .replace('__user__', userId)
                    .replace('__nodeId__', nodeId);
                window.location.href = url;
            };

            // Add loading indicator on click
            $('.node').on('click', function() {
                const $spinner = $(
                    '<div class="spinner-border spinner-border-sm"><i class="fas fa-spinner fa-spin"></i></div>'
                );
                $(this).append($spinner);
            });

            // Toggle node details
            window.toggleNodeDetails = function(nodeId) {
                const $nodeWrapper = $('#' + nodeId);
                if (!$nodeWrapper.length) return;

                const $detailsPanel = $nodeWrapper.find('.details-panel').first();
                if (!$detailsPanel.length) return;

                if ($detailsPanel.is(':hidden')) {
                    $detailsPanel.show();
                    $nodeWrapper.addClass('expanded');
                } else {
                    $detailsPanel.hide();
                    $nodeWrapper.removeClass('expanded');
                }
            };

            // Expand all nodes
            window.expandAllNodes = function() {
                $('.node-wrap').each(function() {
                    const $detailsPanel = $(this).find('.details-panel').first();
                    if ($detailsPanel.length) {
                        $detailsPanel.show();
                        $(this).addClass('expanded');
                    }
                });
            };

            // Collapse all nodes
            window.collapseAllNodes = function() {
                $('.node-wrap').each(function() {
                    const $detailsPanel = $(this).find('.details-panel').first();
                    if ($detailsPanel.length) {
                        $detailsPanel.hide();
                        $(this).removeClass('expanded');
                    }
                });
            };

            // Prevent event bubbling for action buttons
            $(document).on('click', '.action-btn', function(e) {
                e.stopPropagation();
            });
        });
    </script>

@endsection
