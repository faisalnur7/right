@props(['disabled' => false])

@if ($attributes->get('type') === 'password')
    <div class="password-field">
        <input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
        <button type="button" class="password-toggle" data-password-toggle="{{ $attributes->get('id') }}" aria-label="Show password">
            <i class="far fa-eye" aria-hidden="true"></i>
        </button>
    </div>
@else
    <input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
@endif
