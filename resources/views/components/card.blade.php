@props(['title' => null, 'subtitle' => null, 'padding' => 'p-4'])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden']) }}>
    @if($title || $subtitle)
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
            <div>
                @if($title)
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-tight">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if(isset($action))
                <div>{{ $action }}</div>
            @endif
        </div>
    @endif
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
