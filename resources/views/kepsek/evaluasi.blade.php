@extends('layouts.app')
@section('title', 'Evaluasi Hasil Siswa')
@section('page-title', 'Evaluasi Hasil')

@section('content')
<div class="space-y-6 pb-20 fade-in">

    {{-- ── SUMMARY STATS ── --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        @php
            $evalStats = [
                ['label' => 'Total Dievaluasi', 'value' => $stats['total'], 'color' => 'indigo', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['label' => 'Kategori BSB', 'value' => $stats['bsb'], 'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                ['label' => 'Kategori BSH', 'value' => $stats['bsh'], 'color' => 'amber', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                ['label' => 'Kategori MB', 'value' => $stats['mb'], 'color' => 'rose', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
            ];
        @endphp
        @foreach($evalStats as $s)
        <div class="card p-5 group hover:border-var(--accent)/20 transition-all border-l-4 border-{{ $s['color'] }}-500">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-{{ $s['color'] }}-50 text-{{ $s['color'] }}-600 shadow-sm group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">{{ $s['label'] }}</p>
                    <p class="text-xl font-black text-gray-900 tracking-tighter leading-none">{{ $s['value'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── FILTER & SEARCH ── --}}
    <div class="card p-6 shadow-sm border border-gray-100">
        <form action="{{ route('kepsek.evaluasi') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-3">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Pilih Periode</label>
                <select name="periode_id" class="form-select" onchange="this.form.submit()">
                    @foreach($periodeList as $p)
                        <option value="{{ $p->id_periode }}" {{ $selectedPeriodeId == $p->id_periode ? 'selected' : '' }}>{{ $p->nama_periode }} - {{ $p->tahunAjaran->nama ?? '—' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Pencarian Siswa</label>
                <div class="search-box">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/NISN...">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Unit Kelas</label>
                <select name="kelas_id" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id_kelas }}" {{ $selectedKelasId == $kelas->id_kelas ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Status Capaian</label>
                <select name="kategori" class="form-select">
                    <option value="">Semua</option>
                    <option value="BSB" {{ request('kategori') == 'BSB' ? 'selected' : '' }}>BSB</option>
                    <option value="BSH" {{ request('kategori') == 'BSH' ? 'selected' : '' }}>BSH</option>
                    <option value="MB" {{ request('kategori') == 'MB' ? 'selected' : '' }}>MB</option>
                </select>
            </div>
            <div class="md:col-span-2 flex items-end">
                <button type="submit" class="btn btn-indigo w-full justify-center h-[42px] rounded-xl shadow-lg shadow-indigo-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- ── EVALUATION TABLE ── --}}
    <div class="card overflow-hidden border-none shadow-xl">
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="w-16 text-center">RANK</th>
                        <th>Informasi Siswa</th>
                        <th class="text-center">Indeks Akhir</th>
                        <th class="text-center w-48">Progress Visual</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-right pr-8">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($evaluasi as $index => $eval)
                        <tr class="hover:bg-var(--bg) transition-all cursor-pointer group" onclick="toggleDetail('detail-{{ $eval->id_evaluasi }}')">
                            <td class="text-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-[10px] font-black {{ $index < 3 ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $evaluasi->firstItem() + $index }}
                                </div>
                            </td>
                            <td class="py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs shadow-sm bg-white border border-gray-100 group-hover:border-indigo-200 group-hover:text-indigo-600 transition-all">
                                        {{ strtoupper(substr($eval->siswa->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-800 tracking-tight leading-none mb-1">{{ $eval->siswa->name }}</p>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                            {{ $eval->siswa->kelas->nama_kelas ?? '—' }} • NISN: {{ $eval->siswa->kode ?: '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="text-sm font-black text-gray-900 tracking-tighter">{{ number_format($eval->nilai_akhir * 100, 1) }}%</span>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        @php 
                                            $progColor = $eval->kategori_akhir === 'BSB' ? 'progress-green' : ($eval->kategori_akhir === 'BSH' ? 'progress-yellow' : 'progress-red');
                                        @endphp
                                        <div class="h-full {{ $progColor }} transition-all duration-1000" style="width: {{ $eval->nilai_akhir * 100 }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-400">{{ round($eval->nilai_akhir * 100) }}%</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @php $color = $eval->kategori_akhir === 'BSB' ? 'bsb' : ($eval->kategori_akhir === 'BSH' ? 'bsh' : 'mb'); @endphp
                                <div class="flex flex-col items-center gap-1.5">
                                    <span class="badge badge-{{ $color }} px-4 py-1 text-[9px] font-black uppercase shadow-sm">{{ match($eval->kategori_akhir) { 'BSB' => 'Berkembang Sangat Baik (BSB)', 'BSH' => 'Berkembang Sesuai Harapan (BSH)', 'MB' => 'Mulai Berkembang (MB)', default => $eval->kategori_akhir } }}</span>
                                </div>
                            </td>
                            <td class="text-right pr-8">
                                <div class="flex justify-end">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-all">
                                        <svg class="w-4 h-4 transform group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {{-- ── EXPANDABLE DETAIL ROW ── --}}
                        <tr id="detail-{{ $eval->id_evaluasi }}" class="hidden bg-gray-50/30">
                            <td colspan="6" class="p-8">
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                                    {{-- Criteria Breakdown --}}
                                    <div class="lg:col-span-5 space-y-4">
                                        <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                            <span class="w-6 h-px bg-gray-200"></span>
                                            Analisis Aspek Kriteria
                                        </h5>
                                        <div class="grid grid-cols-1 gap-2.5">
                                            @foreach($kriteriaList as $k)
                                                @php 
                                                    $detail = $eval->detail->filter(fn($d) => $d->subkriteria->kriteria_id == $k->id_kriteria);
                                                    $avg = $detail->avg('nilai_crisp');
                                                    $katObj = \App\Models\KategoriNilai::findByNilai($avg ?? 0);
                                                    $kat = $katObj ? $katObj->nama : 'MB';
                                                    $katColorClass = $kat === 'BSB' ? 'text-emerald-600' : ($kat === 'BSH' ? 'text-amber-600' : 'text-rose-600');
                                                @endphp
                                                <div class="flex items-center justify-between p-3.5 bg-white rounded-2xl border border-gray-100 shadow-sm hover:border-indigo-100 transition-all group/crit">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-[10px] font-black text-gray-400 group-hover/crit:bg-indigo-50 group-hover/crit:text-indigo-500 transition-colors">
                                                            {{ $k->kode }}
                                                        </div>
                                                        <span class="text-xs font-black text-gray-700 tracking-tight">{{ $k->nama_kriteria }}</span>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-sm font-black text-gray-900 tracking-tighter">{{ $score }}%</p>
                                                        <p class="text-[9px] font-black {{ $katColorClass }} uppercase tracking-widest">{{ $kat }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Recommendation & Notes --}}
                                    <div class="lg:col-span-7 space-y-6">
                                        <div class="card p-6 bg-white shadow-sm border border-gray-100">
                                            <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                                Perspektif Guru
                                            </h5>
                                            <div class="relative pl-6">
                                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-100 rounded-full"></div>
                                                <p class="text-sm text-gray-600 leading-relaxed italic font-medium">"{{ $eval->catatan_guru ?: 'Belum ada evaluasi kualitatif dari guru pendamping.' }}"</p>
                                            </div>
                                        </div>

                                        <div class="card p-6 bg-indigo-50/30 border border-indigo-100">
                                            <h5 class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0012 18.75c-1.03 0-1.9-.4-2.593-.979l-.547-.548z"/></svg>
                                                Rekomendasi Strategis SPK
                                            </h5>
                                            <p class="text-sm text-indigo-900 leading-relaxed font-bold tracking-tight">
                                                {{ $eval->rekomendasi ?: 'Analisis rekomendasi otomatis belum tersedia.' }}
                                            </p>
                                        </div>

                                        <div class="flex justify-end gap-3 pt-4">
                                            <a href="{{ route('kepsek.siswa.show', ['id' => $eval->siswa_id, 'periode_id' => $selectedPeriodeId]) }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest bg-white border border-gray-100 text-gray-600 hover:bg-gray-50 transition-all">
                                                Profil Siswa
                                            </a>
                                            <form action="{{ route('kepsek.laporan.generate-word') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="siswa_id" value="{{ $eval->siswa_id }}">
                                                <input type="hidden" name="periode_id" value="{{ $periodeAktif->id_periode ?? '' }}">
                                                <button type="submit" class="btn btn-indigo px-8 rounded-xl font-black uppercase tracking-widest text-[10px] shadow-lg shadow-indigo-100">
                                                    Cetak Rapor
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-24 text-center">
                                <div class="w-20 h-20 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6 border border-gray-100 shadow-inner">
                                    <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <h4 class="text-sm font-black text-gray-900 tracking-tight">Evaluasi Belum Tersedia</h4>
                                <p class="text-xs text-gray-400 mt-2">Data hasil SPK belum dipublikasikan atau filter tidak sesuai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($evaluasi instanceof \Illuminate\Pagination\LengthAwarePaginator && $evaluasi->hasPages())
            <div class="px-8 py-5 bg-gray-50/50 border-t border-gray-100">
                {{ $evaluasi->links() }}
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    function toggleDetail(id) {
        const el = document.getElementById(id);
        const isHidden = el.classList.contains('hidden');
        
        // Close all other details
        document.querySelectorAll('[id^="detail-"]').forEach(row => {
            if (row.id !== id) row.classList.add('hidden');
        });

        if (isHidden) {
            el.classList.remove('hidden');
            el.classList.add('animate-fade-in');
        } else {
            el.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection
