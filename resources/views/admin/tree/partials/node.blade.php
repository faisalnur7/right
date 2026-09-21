<div class="tree-node">
    <strong>User ID:</strong> {{ $node['user_id'] }} <br>
    <strong>Level:</strong> {{ $node['level'] }} |
    <strong>Activation:</strong> {{ $node['activation_number'] }}

    @if (!empty($node['children']))
        <div class="tree-children">
            @foreach($node['children'] as $child)
                @include('admin.tree.partials.node', ['node' => $child])
            @endforeach
        </div>
    @endif
</div>
