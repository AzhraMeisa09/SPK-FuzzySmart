@extends('layouts.app')
@section('title', 'Finalisasi Template')
@section('page-title', 'Pustaka Rekomendasi')

@section('content')

<div class="space-y-6">

    {{-- ── STEPPER ── --}}
    <div class="card p-4 shadow-xl border-none flex items-center justify-between gap-4">
        <a href="{{ route('admin.template-rekomendasi.index') }}" class="flex items-center gap-3 px-5 py-2.5 bg-green-50 text-green-700 rounded-2xl text-[10px] font-bold hover:bg-green-100 transition-all">
            <span class="w-6 h-6 bg-green-500 text-white rounded-lg flex items-center justify-center">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
            </span>
            Pilih Kriteria
        </a>
        <div class="flex-1 h-px bg-gray-100 mx-2"></div>
        <div class="flex items-center gap-3 px-5 py-2.5 bg-green-50 text-green-700 rounded-2xl text-[10px] font-bold">
            <span class="w-6 h-6 bg-green-500 text-white rounded-lg flex items-center justify-center">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
            </span>
            Subkriteria
        </div>
        <div class="flex-1 h-px bg-gray-100 mx-2"></div>
        <div class="flex items-center gap-3 px-6 py-2.5 bg-var(--accent) text-white rounded-2xl text-[10px] font-bold shadow-lg shadow-green-100">
            <span class="w-6 h-6 bg-white/20 text-white rounded-lg flex items-center justify-center font-bold">3</span>
            Finalisasi
        </div>
    </div>

    {{-- ── HEADER CARD ── --}}
    <div class="card p-6 shadow-xl border-none">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Pratinjau Draft Narasi</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Tinjau dan sesuaikan isi rekomendasi sebelum disimpan secara permanen ke pustaka.</p>
            </div>
            <button type="button" onclick="window.history.back()" class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-black text-xs font-bold hover:bg-gray-50 transition-all shadow-sm">
                Kembali
            </button>
        </div>
    </div>

    {{-- ── ALERTS ── --}}
    @if(collect($drafts)->where('sudah_ada', true)->count() > 0)
        <div class="p-4 bg-amber-50/50 border border-amber-100 text-amber-700 rounded-2xl text-[10px] font-bold flex items-center animate-fade-in shadow-sm">
            <svg class="w-5 h-5 mr-3 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Beberapa template sudah terdaftar di database. Proses simpan akan memperbarui data lama.
        </div>
    @endif

    {{-- ── DRAFT LIST ── --}}
    <form action="{{ route('admin.template-rekomendasi.store-batch') }}" method="POST">
        @csrf
        <div class="space-y-6">
            @foreach($drafts as $index => $d)
                <div class="card p-8 shadow-xl border-none group hover:bg-var(--bg) transition-colors relative overflow-hidden">
                    @if($d['sudah_ada'])
                        <div class="absolute top-0 right-0 px-4 py-1 bg-amber-100 text-amber-700 text-[8px] font-bold rounded-bl-xl shadow-sm border-l border-b border-amber-200">EXISTING DATA</div>
                    @endif
                    
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                        <div class="space-y-4">
                            <div>
                                <span class="text-[9px] font-bold text-gray-400 block mb-1">Mata Evaluasi</span>
                                <span class="text-xs font-bold text-var(--text-1) tracking-tight uppercase">{{ $d['kriteria_nama'] }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-gray-400 block mb-1">Subkriteria</span>
                                <span class="text-sm font-semibold text-var(--accent) tracking-tight">{{ $d['subkriteria_nama'] }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-gray-400 block mb-1">Capaian</span>
                                <span class="badge {{ $d['kategori'] == 'BSB' ? 'badge-bsb' : ($d['kategori'] == 'BSH' ? 'badge-bsh' : 'badge-mb') }}">
                                    {{ $d['kategori'] }}
                                </span>
                            </div>
                        </div>

                        <div class="lg:col-span-3 space-y-4">
                            <input type="hidden" name="templates[{{ $index }}][subkriteria_id]" value="{{ $d['subkriteria_id'] }}">
                            <input type="hidden" name="templates[{{ $index }}][kategori]" value="{{ $d['kategori'] }}">
                            
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold text-gray-500 mb-2 block tracking-wide">Isi Rekomendasi</label>
                                <textarea name="templates[{{ $index }}][isi]" rows="3" required
                                          class="form-input rounded-2xl bg-white border-gray-100 font-medium text-xs p-6 resize-none shadow-md group-hover:border-var(--accent)/30 transition-all">{{ $d['isi'] }}</textarea>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="form-group">
                                    <label class="form-label text-[10px] font-bold text-gray-500 mb-2 block tracking-wide">Prioritas Narasi</label>
                                    <select name="templates[{{ $index }}][prioritas]" required class="form-select rounded-xl bg-white border-gray-100 font-bold text-xs">
                                        <option value="tinggi">Tinggi (Utama)</option>
                                        <option value="sedang">Sedang</option>
                                        <option value="rendah">Rendah</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-[10px] font-bold text-gray-500 mb-2 block tracking-wide">Urutan Tampil</label>
                                    <input type="number" name="templates[{{ $index }}][urutan]" value="{{ $index + 1 }}" class="form-input rounded-xl bg-white border-gray-100 font-bold text-xs">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="flex justify-center pt-8 pb-12">
                <button type="submit" class="px-12 py-4 rounded-2xl bg-var(--accent) text-black border border-gray-200 text-sm font-bold shadow-lg shadow-green-100 hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-3 group">
                    <span class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center group-hover:rotate-12 transition-transform">
                        <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
                    </span>
                    Simpan ke Pustaka
                </button>
            </div>
        </div>
    </form>

</div>

@endsection
