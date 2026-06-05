@extends('layouts.app')
@section('title', 'Validasi Evaluasi — ' . $evaluasi->siswa->name)
@section('page-title', 'Form Validasi Guru')

@section('content')
<div class="space-y-6 fade-in pb-12">

    {{-- ── NAVIGASI ATAS ── --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('guru.validasi.index') }}" class="group flex items-center gap-2" style="color: var(--text-2);">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--bg); border: 1px solid var(--border);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </div>
            <span class="text-xs font-semibold uppercase tracking-widest">Kembali ke Daftar</span>
        </a>
        <div class="flex items-center gap-2">
            @if($evaluasi->isValidatedByGuru())
                <span class="px-3 py-1.5 text-[10px] font-bold uppercase rounded-lg bg-green-100 text-green-700 border border-green-200">
                    ✓ Sudah Divalidasi
                </span>
            @else
                <span class="px-3 py-1.5 text-[10px] font-bold uppercase rounded-lg bg-amber-100 text-amber-700 border border-amber-200">
                    ⏳ Menunggu Validasi
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- ── KOLOM KIRI: INFO SISWA & HASIL SPK ── --}}
        <div class="lg:col-span-7 space-y-5">

            {{-- Profil Siswa --}}
            <div class="card p-5">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-black shadow-inner" style="background: var(--accent-lt); color: var(--accent);">
                        {{ strtoupper(substr($evaluasi->siswa->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="badge badge-blue text-[9px]">{{ $evaluasi->periode->nama_periode ?? '—' }}</span>
                            @if($evaluasi->isKategoriDiubahGuru())
                                <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-rose-100 text-rose-700 border border-rose-200">Kategori Diubah Guru</span>
                            @endif
                        </div>
                        <h1 class="text-xl font-bold tracking-tight" style="color: var(--text-1);">{{ $evaluasi->siswa->name }}</h1>
                        <p class="text-[10px] font-bold uppercase tracking-widest mt-1" style="color: var(--text-3);">
                            {{ $evaluasi->siswa->kelas->nama_kelas ?? '—' }}
                            &bull; NISN: {{ $evaluasi->siswa->kode ?: $evaluasi->siswa->id_siswa ?: '—' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Hasil Rekomendasi Sistem --}}
            <div class="card p-5">
                <div class="flex items-center gap-3 mb-4 pb-4" style="border-bottom: 1px solid var(--border);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent-lt); color: var(--accent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold" style="color: var(--text-1);">Hasil Rekomendasi Sistem (Fuzzy SMART)</h3>
                        <p class="text-[10px]" style="color: var(--text-3);">Hasil perhitungan otomatis — belum menjadi keputusan final</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div class="p-4 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                        <p class="text-[9px] font-bold uppercase tracking-widest mb-1" style="color: var(--text-3);">Nilai SPK (Va)</p>
                        <p class="text-2xl font-black" style="color: var(--text-1);">{{ number_format($evaluasi->nilai_akhir, 3) }}</p>
                        <div class="progress-track mt-2">
                            <div class="progress-fill {{ $evaluasi->kategori_rekomendasi_sistem === 'BSB' ? 'progress-green' : ($evaluasi->kategori_rekomendasi_sistem === 'BSH' ? 'progress-yellow' : 'progress-red') }}"
                                 style="width: {{ $evaluasi->nilai_akhir * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                        <p class="text-[9px] font-bold uppercase tracking-widest mb-2" style="color: var(--text-3);">Kategori Rekomendasi</p>
                        @php $ks = $evaluasi->kategori_rekomendasi_sistem ?? $evaluasi->kategori_akhir; @endphp
                        <span class="badge badge-{{ match($ks) { 'BSB' => 'bsb', 'BSH' => 'bsh', default => 'mb' } }} px-3 py-1 font-bold text-[10px]">
                            {{ match($ks) { 'BSB' => 'Berkembang Sangat Baik (BSB)', 'BSH' => 'Berkembang Sesuai Harapan (BSH)', 'MB' => 'Mulai Berkembang (MB)', default => $ks } }}
                        </span>
                    </div>
                </div>

                {{-- Detail per kriteria --}}
                @if($details->isNotEmpty())
                    <div class="space-y-3">
                        <p class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">Rincian per Aspek Perkembangan</p>
                        @foreach($details as $aspek => $items)
                            @php
                                $avgAspek = $items->avg('nilai_crisp');
                                $katAspek = $avgAspek >= 85 ? 'BSB' : ($avgAspek >= 70 ? 'BSH' : 'MB');
                            @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                                <div>
                                    <p class="text-xs font-bold" style="color: var(--text-1);">{{ $aspek }}</p>
                                    <p class="text-[9px]" style="color: var(--text-3);">{{ $items->count() }} indikator</p>
                                </div>
                                <div class="text-right flex items-center gap-2">
                                    <span class="text-xs font-black" style="color: var(--text-1);">{{ number_format($avgAspek, 1) }}%</span>
                                    <span class="badge badge-{{ match($katAspek) { 'BSB' => 'bsb', 'BSH' => 'bsh', default => 'mb' } }} text-[9px]">{{ $katAspek }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Rekomendasi sistem --}}
                @if($evaluasi->rekomendasi)
                    <div class="mt-4 p-4 rounded-xl" style="background: var(--accent-lt); border: 1px solid var(--border);">
                        <p class="text-[9px] font-bold uppercase tracking-widest mb-2" style="color: var(--accent);">Rekomendasi Sistem</p>
                        <p class="text-xs italic leading-relaxed" style="color: var(--text-2);">&ldquo;{{ $evaluasi->rekomendasi }}&rdquo;</p>
                    </div>
                @endif
            </div>

            {{-- Portofolio Singkat --}}
            @if($portofolio->isNotEmpty())
                <div class="card p-5">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest mb-4" style="color: var(--text-3);">Portofolio Anak ({{ $portofolio->count() }} entri)</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($portofolio->take(6) as $p)
                            <div class="rounded-xl overflow-hidden" style="border: 1px solid var(--border);">
                                @if($p->images->count() > 0)
                                    <img src="{{ asset('storage/' . $p->images->first()->file_path) }}" class="w-full h-24 object-cover">
                                @endif
                                <div class="p-2">
                                    <p class="text-[10px] font-bold truncate" style="color: var(--text-1);">{{ $p->judul }}</p>
                                    <p class="text-[9px]" style="color: var(--text-3);">Minggu {{ $p->minggu->minggu_ke ?? '?' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- ── KOLOM KANAN: FORM VALIDASI ── --}}
        <div class="lg:col-span-5">
            <div class="card p-6 sticky top-4">
                <div class="flex items-center gap-3 mb-5 pb-5" style="border-bottom: 1px solid var(--border);">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: var(--accent-lt); color: var(--accent);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold" style="color: var(--text-1);">Keputusan Guru</h2>
                        <p class="text-[10px]" style="color: var(--text-3);">Anda sebagai validator akhir</p>
                    </div>
                </div>

                @if($evaluasi->periode->status === 'final')
                    {{-- Periode sudah final: hanya tampilkan, tidak bisa diedit --}}
                    <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-200 mb-4">
                        <p class="text-xs font-bold text-indigo-700">🔒 Periode ini sudah dipublikasikan (Final). Keputusan tidak dapat diubah.</p>
                    </div>
                    @if($evaluasi->isValidatedByGuru())
                        <div class="space-y-3">
                            <div class="p-3 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                                <p class="text-[9px] font-bold uppercase mb-1" style="color: var(--text-3);">Kategori Keputusan</p>
                                @php $kg = $evaluasi->kategori_keputusan_guru; @endphp
                                <span class="badge badge-{{ match($kg) { 'BSB' => 'bsb', 'BSH' => 'bsh', default => 'mb' } }} font-bold">{{ $kg }}</span>
                            </div>
                            <div class="p-3 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                                <p class="text-[9px] font-bold uppercase mb-1" style="color: var(--text-3);">Catatan Guru</p>
                                <p class="text-xs italic leading-relaxed" style="color: var(--text-2);">"{{ $evaluasi->catatan_guru }}"</p>
                            </div>
                        </div>
                    @endif
                @else
                    {{-- Form Validasi --}}
                    <form action="{{ route('guru.validasi.submit', $evaluasi->id_evaluasi) }}" method="POST" class="space-y-5">
                        @csrf

                        {{-- Info perbandingan --}}
                        <div class="p-3 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                            <p class="text-[9px] font-bold uppercase tracking-widest mb-1" style="color: var(--text-3);">Rekomendasi Sistem</p>
                            @php $ks = $evaluasi->kategori_rekomendasi_sistem ?? $evaluasi->kategori_akhir; @endphp
                            <span class="badge badge-{{ match($ks) { 'BSB' => 'bsb', 'BSH' => 'bsh', default => 'mb' } }} text-[9px] font-bold">
                                {{ $ks }} — {{ match($ks) { 'BSB' => 'Berkembang Sangat Baik', 'BSH' => 'Berkembang Sesuai Harapan', 'MB' => 'Mulai Berkembang', default => $ks } }}
                            </span>
                            <p class="text-[9px] mt-1.5" style="color: var(--text-3);">Anda dapat menyetujui atau mengubah kategori ini berdasarkan observasi langsung.</p>
                        </div>

                        {{-- Pilih Kategori --}}
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest block mb-2" style="color: var(--text-2);">
                                Keputusan Kategori Akhir *
                            </label>
                            <div class="grid grid-cols-3 gap-2" id="kategoriSelector">
                                @foreach(['BSB' => ['label' => 'Berkembang Sangat Baik', 'color' => 'border-emerald-400 bg-emerald-50 text-emerald-700'], 'BSH' => ['label' => 'Berkembang Sesuai Harapan', 'color' => 'border-amber-400 bg-amber-50 text-amber-700'], 'MB' => ['label' => 'Mulai Berkembang', 'color' => 'border-rose-400 bg-rose-50 text-rose-700']] as $k => $meta)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="kategori_keputusan_guru" value="{{ $k }}" class="sr-only"
                                               {{ (old('kategori_keputusan_guru', $evaluasi->kategori_keputusan_guru ?? $ks) === $k) ? 'checked' : '' }}>
                                        <div class="p-3 rounded-xl text-center border-2 transition-all {{ (old('kategori_keputusan_guru', $evaluasi->kategori_keputusan_guru ?? $ks) === $k) ? $meta['color'] . ' border-2' : 'border-gray-100 bg-gray-50 text-gray-500' }} hover:border-gray-300 kategori-option"
                                             data-value="{{ $k }}" data-active-class="{{ $meta['color'] }}">
                                            <p class="text-sm font-black leading-none">{{ $k }}</p>
                                            <p class="text-[9px] font-bold mt-1 leading-snug">{{ $meta['label'] }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('kategori_keputusan_guru')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Catatan Guru --}}
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest block mb-2" style="color: var(--text-2);">
                                Catatan Evaluasi *
                            </label>
                            <textarea name="catatan_guru" rows="5"
                                      class="form-input text-xs resize-y min-h-[100px]"
                                      placeholder="Tuliskan catatan observasi, perkembangan anak, dan masukan untuk orang tua...">{{ old('catatan_guru', $evaluasi->catatan_guru) }}</textarea>
                            @error('catatan_guru')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-[9px] mt-1" style="color: var(--text-3);">Minimal 10 karakter. Catatan ini akan terlihat oleh wali murid setelah periode dipublikasikan.</p>
                        </div>

                        {{-- Tombol Submit --}}
                        <button type="submit" class="btn btn-green w-full justify-center py-3 text-sm font-bold"
                                onclick="return confirm('Konfirmasi: Anda akan menyimpan keputusan validasi ini. Lanjutkan?')">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Simpan Keputusan Validasi
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

</div>

<script>
// Kategori selector visual feedback
document.querySelectorAll('input[name="kategori_keputusan_guru"]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.kategori-option').forEach(opt => {
            opt.className = opt.className.replace(/border-emerald-400 bg-emerald-50 text-emerald-700|border-amber-400 bg-amber-50 text-amber-700|border-rose-400 bg-rose-50 text-rose-700/g, '');
            opt.classList.add('border-gray-100', 'bg-gray-50', 'text-gray-500');
        });
        const selectedOpt = radio.nextElementSibling;
        if (selectedOpt) {
            const activeClass = selectedOpt.dataset.activeClass;
            selectedOpt.classList.remove('border-gray-100', 'bg-gray-50', 'text-gray-500');
            activeClass.split(' ').forEach(c => selectedOpt.classList.add(c));
        }
    });
});
</script>
@endsection
