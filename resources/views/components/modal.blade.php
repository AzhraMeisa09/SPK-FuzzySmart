@props(['name', 'title' => '', 'show' => false])

<div x-data="{ show: @js($show) }"
     x-show="show"
     x-on:open-modal.window="if ($event.detail.name === '{{ $name }}') show = true"
     x-on:close-modal.window="if ($event.detail.name === '{{ $name }}') show = false"
     x-on:keydown.escape.window="show = false"
     class="modal-backdrop"
     :class="{ 'open': show }"
     x-cloak>
    
    <div class="modal-box" @click.away="show = false">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">
                <span x-text="isEdit ? 'Edit Kategori Nilai' : 'Tambah Kategori Nilai'"></span>
            </h3>
            <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>
