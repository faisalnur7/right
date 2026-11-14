@extends('layouts.master')

@section('title', 'Team Organogram')
@section('page_title', 'Team Organogram')

@section('contents')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if ($tabs->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-sitemap fa-2x mb-2"></i>
                        <p class="mb-0">No sale log is available yet.</p>
                    </div>
                @else
                    {{-- Legend --}}
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
                                <div class="w-4 h-4 bg-purple-500 rounded"></div>
                                <span>Clone ID</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 bg-blue-500 text-white rounded text-xs flex items-center justify-center">L</span>
                                <span>Left Position</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 bg-orange-600 text-white rounded text-xs flex items-center justify-center">R</span>
                                <span>Right Position</span>
                            </div>
                        </div>
                    </div>

                    {{-- Sale log tabs: the number is the user's current activation in that sale log --}}
                    <ul class="nav nav-tabs" id="organogramTabs" role="tablist">
                        @foreach ($tabs as $tab)
                            @php $isCurrent = $tab['sale_log']->id === $activeSaleLogId; @endphp
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $isCurrent ? 'active' : '' }}"
                                    id="sale-log-tab-{{ $tab['sale_log']->id }}" data-toggle="tab"
                                    href="#sale-log-pane-{{ $tab['sale_log']->id }}" role="tab"
                                    aria-controls="sale-log-pane-{{ $tab['sale_log']->id }}"
                                    aria-selected="{{ $isCurrent ? 'true' : 'false' }}">
                                    {{ $tab['sale_log']->name }}
                                    <span class="badge badge-pill {{ $tab['activation'] > 0 ? 'badge-success' : 'badge-secondary' }} ml-1"
                                        title="Activation number in this sale log">{{ $tab['activation'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content pt-3" id="organogramTabContent">
                        @foreach ($tabs as $tab)
                            @php $isCurrent = $tab['sale_log']->id === $activeSaleLogId; @endphp
                            <div class="tab-pane fade {{ $isCurrent ? 'show active' : '' }}"
                                id="sale-log-pane-{{ $tab['sale_log']->id }}" role="tabpanel"
                                aria-labelledby="sale-log-tab-{{ $tab['sale_log']->id }}">

                                @if ($tab['tree'])
                                    <div class="mb-3 flex gap-2">
                                        <button type="button" onclick="expandAllNodes()"
                                            class="px-3 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700 transition-colors">
                                            <i class="fas fa-expand-alt mr-1"></i> Expand
                                        </button>
                                        <button type="button" onclick="collapseAllNodes()"
                                            class="px-3 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700 transition-colors">
                                            <i class="fas fa-compress-alt mr-1"></i> Collapse
                                        </button>
                                    </div>

                                    <div class="tree text-center overflow-x-auto flex flex-col justify-center pb-3">
                                        <ul class="tree-root w-full">
                                            @include('admin.tree.partials.graph_node', [
                                                'node' => $tab['tree'],
                                                'saleLogId' => $tab['sale_log']->id,
                                                'canDrill' => false,
                                            ])
                                        </ul>
                                    </div>
                                @else
                                    <div class="text-center text-muted py-5">
                                        <i class="fas fa-sitemap fa-2x mb-2"></i>
                                        <p class="mb-0">You are not placed in this sale log yet.</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Open / close one node's details panel
            window.toggleNodeDetails = function(nodeId) {
                const $node = $('#' + nodeId);
                const $panel = $node.find('.details-panel').first();
                if (!$panel.length) return;

                const willOpen = $panel.is(':hidden');
                $panel.toggle(willOpen);
                $node.toggleClass('expanded', willOpen);
            };

            // Expand / Collapse only act on the sale log tab that is open
            const setAllDetails = function(open) {
                $('#organogramTabContent .tab-pane.active .node-wrap').each(function() {
                    $(this).find('.details-panel').first().toggle(open);
                    $(this).toggleClass('expanded', open);
                });
            };

            window.expandAllNodes = function() { setAllDetails(true); };
            window.collapseAllNodes = function() { setAllDetails(false); };
        });
    </script>
@endsection
