@props(['type' => 'primary', 'size' => 'md'])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-black uppercase tracking-widest transition-all cursor-pointer outline-none border transition-all';
    
    $types = [
        'primary'   => 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/20',
        'secondary' => 'bg-blue-600 text-white border-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-600/20',
        'outline'   => 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50',
        'ghost'     => 'bg-transparent text-slate-400 border-transparent hover:bg-slate-100 hover:text-slate-600',
        'danger'    => 'bg-red-50 text-red-600 border-red-100 hover:bg-red-600 hover:text-white',
    ];

    $sizes = [
        'xs' => 'px-2.5 py-1 text-[9px] rounded-lg',
        'sm' => 'px-3.5 py-1.5 text-[10px] rounded-lg',
        'md' => 'px-5 py-2.5 text-[11px] rounded-xl',
        'lg' => 'px-7 py-3.5 text-xs rounded-xl',
    ];

    $selectedType = $types[$type] ?? $types['primary'];
    $selectedSize = $sizes[$size] ?? $sizes['md'];
@endphp

<button {{ $attributes->merge(['class' => "$base $selectedType $selectedSize"]) }}>
    {{ $slot }}
</button>
