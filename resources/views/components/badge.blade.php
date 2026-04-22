@props(['type' => 'default'])

@php
    $classes = [
        'default' => 'badge-bsh',
        'success' => 'badge-bsb',
        'warning' => 'badge-bsh',
        'danger'  => 'badge-mb',
        'info'    => 'bg-blue-50 text-blue-700',
        
        // Specific MB/BSH/BSB
        'mb'      => 'badge-mb',
        'bsh'     => 'badge-bsh',
        'bsb'     => 'badge-bsb',
    ];

    $class = $classes[$type] ?? $classes['default'];
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $class]) }}>
    {{ $slot }}
</span>
