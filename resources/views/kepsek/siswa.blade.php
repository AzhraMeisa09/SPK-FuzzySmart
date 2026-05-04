@extends('layouts.app')
@section('title', 'Data Siswa Sekolah')
@section('page-title', 'Data Siswa')

@section('content')
<div class="space-y-6 pb-20 fade-in">

    {{-- ── FILTER SECTION ── --}}
    <div class="card p-6 shadow-sm border border-gray-100">
        <form action="{{ route('kepsek.siswa') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-3">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Pilih Periode</label>
                <select name="periode_id" class="form-select" onchange="this.form.submit()">
                    @foreach($periodeList as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriodeId == $p->id ? 'selected' : '' }}>{{ $p->nama_periode }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Pencarian</label>
                <div class="search-box">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau NISN siswa...">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div class="md:col-span-3">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Filter Kelas</label>
                <select name="kelas_id" class="form-select">
                    <option value="">Semua Unit Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 flex items-end">
                <button type="submit" class="btn btn-green w-full justify-center h-[42px] rounded-xl shadow-lg shadow-green-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- ── STUDENT TABLE ── --}}
    <div class="card overflow-hidden border-none shadow-xl">
        <table class="tbl">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="w-20 text-center">No</th>
                    <th>Informasi Siswa</th>
                    <th class="hidden md:table-cell">NISN</th>
                    <th>Kelas</th>
                    <th class="text-center">Indeks Capaian</th>
                    <th class="text-center">Status Akhir</th>
                    <th class="text-right pr-8">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($siswa as $s)
                    @php 
                        $eval = $s->evaluasi->first(); 
                        $color = $eval ? ($eval->kategori_akhir === 'BSB' ? 'bsb' : ($eval->kategori_akhir === 'BSH' ? 'bsh' : 'mb')) : 'gray';
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="text-center font-mono text-[10px] font-bold text-gray-300">{{ $siswa->firstItem() + $loop->index }}</td>
                        <td class="py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs shadow-sm bg-indigo-50 text-indigo-500 group-hover:scale-110 transition-transform">
                                    {{ strtoupper(substr($s->nama, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-800 tracking-tight leading-tight mb-1">{{ $s->nama }}</p>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Guru: {{ $s->kelas->guru->first()->nama_lengkap ?? '—' }}</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="hidden md:table-cell">
                            <span class="text-xs font-mono font-bold text-gray-400 tracking-tighter">{{ $s->kode ?: '—' }}</span>
                        </td>
                        <td>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-gray-100/50 border border-gray-100">
                                <span class="text-[10px] font-black text-gray-700 uppercase tracking-tight">{{ $s->kelas->nama_kelas ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($eval)
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-black text-gray-900 tracking-tighter leading-none">{{ number_format($eval->nilai_akhir * 100, 1) }}%</span>
                                    <div class="w-12 h-1 bg-gray-100 rounded-full mt-2 overflow-hidden">
                                        <div class="h-full bg-indigo-500" style="width: {{ $eval->nilai_akhir * 100 }}%"></div>
                                    </div>
                                </div>
                            @else
                                <span class="text-[10px] font-bold text-gray-300 italic tracking-widest uppercase">Ongoing</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($eval)
                                <span class="badge badge-{{ $color }} px-4 py-1 font-bold text-[9px] shadow-sm uppercase">{{ $eval->kategori_akhir }}</span>
                            @else
                                <span class="badge bg-gray-50 text-gray-300 px-3 py-1 font-bold text-[9px] uppercase">Draft</span>
                            @endif
                        </td>
                        <td class="text-right pr-8">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('kepsek.siswa.show', ['id' => $s->id, 'periode_id' => $selectedPeriodeId]) }}" class="p-2 rounded-xl bg-white border border-gray-100 text-gray-400 hover:text-indigo-500 hover:border-indigo-500 transition-all shadow-sm group" title="Detail Siswa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> 
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-24 text-center">
                            <div class="w-20 h-20 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6 border border-gray-100 shadow-inner">
                                <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <h4 class="text-sm font-black text-gray-900 tracking-tight">Data Tidak Ditemukan</h4>
                            <p class="text-xs text-gray-400 mt-2">Tidak ada data siswa yang sesuai dengan filter Anda.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($siswa->hasPages())
            <div class="px-8 py-5 bg-gray-50/50 border-t border-gray-100">
                {{ $siswa->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
