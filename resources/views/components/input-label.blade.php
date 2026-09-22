@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    @if ($value !== null)
        {{ $value }}{{ $slot }}
    @else
        {{ $slot }}
    @endif
</label>
