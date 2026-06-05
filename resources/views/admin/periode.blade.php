@extends('layouts.app')
@section('title', 'Manajemen Periode Penilaian')
@section('page-title', 'Manajemen Periode Penilaian')

@section('content')
<div x-data="{ 
    showAdd: false, 
    showEdit: false, 
    showDelete: false,
    editData: {
        id: '',
        nama_periode: '',
        tahun_ajaran_id: '',
        semester: '',
        tanggal_mulai: '',
        tanggal_selesai: '',
        kelas_ids: []
    },
    deleteData: { id: '', nama: '' },
    
    openEdit(p) {
        this.editData = {
            id: p.id_periode,
            nama_periode: p.nama_periode,
            tahun_ajaran_id: p.tahun_ajaran_id,
            semester: p.semester,
            tanggal_mulai: p.tanggal_mulai ? p.tanggal_mulai.split('T')[0] : '',
            tanggal_selesai: p.tanggal_selesai ? p.tanggal_selesai.split('T')[0] : '',
            kelas_ids: p.kelas ? p.kelas.map(k => k.id_kelas) : []
        };
        this.showEdit = true;
    },
    
    openDelete(p) {
        this.deleteData = { id: p.id_periode, nama: p.nama_periode };
        this.showDelete = true;
    }
}" class="space-y-6">

    {{-- HEADER --}}
    <div class="card p-6 shadow-xl border-none">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Periode Akademik & SPK</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Manajemen jendela waktu penilaian dan kalkulasi Fuzzy SMART per semester.</p>
            </div>
            
            <div class="flex flex-wrap gap-3 items-center">
                <form action="{{ route('admin.periode.index') }}" method="GET" class="w-full lg:w-auto">
                    <div class="search-box lg:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari periode...">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </form>

                <button @click="showAdd = true" class="btn btn-green shadow-lg shadow-green-100 px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                    Tambah Periode
                </button>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="p-4 bg-green-50/50 border border-green-100 text-green-700 rounded-2xl text-xs font-bold flex items-center animate-fade-in shadow-sm">
            <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- MAIN TABLE --}}
    <div class="card overflow-hidden shadow-xl border-none">
        <table class="tbl">
            <thead>
                <tr>
                    <th class="w-16">No</th>
                    <th>Informasi Periode</th>
                    <th>Distribusi Kelas</th>
                    <th>Jangka Waktu</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($periode as $i => $p)
                    <tr class="hover:bg-var(--bg) transition-colors">
                        <td class="text-var(--text-3) text-[11px] font-bold">{{ $periode->firstItem() + $i }}</td>
                        <td>
                            <div class="flex flex-col gap-1">
                                <span class="font-semibold text-var(--text-1) tracking-tight">{{ $p->nama_periode }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-var(--accent-lt) text-var(--accent) rounded text-[9px] font-bold tracking-wide">{{ $p->tahunAjaran->nama }}</span>
                                    <span class="text-[10px] font-medium {{ $p->semester == 'ganjil' ? 'text-amber-600' : 'text-blue-600' }} tracking-wide">
                                        Semester {{ ucfirst($p->semester) }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1.5 max-w-xs">
                                @foreach($p->kelas as $kls)
                                    <span class="px-2 py-1 bg-white border border-var(--border) text-var(--text-2) rounded-lg text-[10px] font-bold tracking-tight shadow-sm hover:border-var(--accent) transition-colors">
                                        {{ $kls->nama_kelas }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col gap-1 text-[11px] font-bold">
                                <div class="flex items-center gap-2 text-var(--text-2)">
                                    <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $p->tanggal_mulai->translatedFormat('d M Y') }}
                                </div>
                                <div class="flex items-center gap-2 text-var(--text-3)">
                                    <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                    {{ $p->tanggal_selesai->translatedFormat('d M Y') }}
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $statusBadge = match($p->status) {
                                    'final'  => '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100"><span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>Final</span>',
                                    'proses' => '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100"><span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>Proses</span>',
                                    'aktif'  => '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold bg-green-50 text-green-700 border border-green-100"><span class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></span>Aktif</span>',
                                    default  => '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold bg-gray-50 text-gray-400 border border-gray-100"><span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>Draft</span>',
                                };
                            @endphp
                            {!! $statusBadge !!}

                            {{-- Progress validasi jika status proses --}}
                            @if($p->status === 'proses')
                                @php $prog = $p->getValidasiProgress(); @endphp
                                <div class="mt-2">
                                    <div class="flex items-center justify-center gap-1 mb-1">
                                        <span class="text-[9px] font-bold text-amber-600">{{ $prog['done'] }}/{{ $prog['total'] }} Divalidasi</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 mx-auto" style="max-width: 80px;">
                                        <div class="h-1.5 rounded-full {{ $prog['done'] == $prog['total'] ? 'bg-green-500' : 'bg-amber-400' }}"
                                             style="width: {{ $prog['total'] > 0 ? ($prog['done'] / $prog['total']) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </td>
                         <td class="text-center">
                             <div class="flex items-center justify-center gap-1.5 flex-wrap">

                                {{-- Tombol Aktif/Non-Aktif (hanya untuk status draft/aktif) --}}
                                @if(in_array($p->status, ['draft', 'aktif']))
                                    <form action="{{ route('admin.periode.toggle', $p) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="p-2 rounded-xl {{ $p->is_aktif ? 'bg-green-50 border border-green-100 text-green-600' : 'bg-gray-50 border border-gray-100 text-gray-400' }} hover:scale-105 transition-all"
                                                title="{{ $p->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </button>
                                    </form>
                                @endif

                                {{-- TOMBOL PROSES EVALUASI (aktif → proses SPK) --}}
                                @if($p->canBeFinalized())
                                    <form action="{{ route('admin.periode.finalize', $p) }}" method="POST" id="form-proses-{{ $p->id_periode }}">
                                        @csrf
                                        <button type="button"
                                                onclick="confirmProses('{{ $p->id_periode }}')"
                                                class="p-2 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-600 hover:text-white hover:scale-105 transition-all"
                                                title="Proses Evaluasi (Jalankan SPK)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    </form>
                                @endif

                                {{-- TOMBOL PUBLIKASI (proses → final, setelah semua guru validasi) --}}
                                @if($p->status === 'proses')
                                    @php $prog = $p->getValidasiProgress(); @endphp
                                    <form action="{{ route('admin.periode.publish', $p->id_periode) }}" method="POST" id="form-publish-{{ $p->id_periode }}">
                                        @csrf
                                        <button type="button"
                                                onclick="confirmPublish('{{ $p->id_periode }}', {{ $prog['pending'] }}, {{ $prog['total'] }})"
                                                class="p-2 rounded-xl {{ $prog['pending'] === 0 ? 'bg-green-50 border border-green-200 text-green-700 hover:bg-green-600 hover:text-white' : 'bg-amber-50 border border-amber-200 text-amber-600 hover:bg-amber-500 hover:text-white' }} hover:scale-105 transition-all"
                                                title="{{ $prog['pending'] === 0 ? 'Publikasikan Hasil' : 'Masih ada ' . $prog['pending'] . ' evaluasi belum divalidasi guru' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    </form>
                                @endif

                                 @if(!$p->isFinal() && $p->status !== 'proses')
                                     <button @click="openEdit({{ Js::from($p) }})"
                                             class="p-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                     </button>
                                     <button @click="openDelete({{ Js::from($p) }})"
                                             class="p-2 rounded-xl bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                     </button>
                                 @elseif($p->isFinal())
                                     <div class="flex items-center gap-1 text-[9px] font-bold text-gray-400 bg-gray-100 px-3 py-1.5 rounded-lg border border-gray-200">
                                         <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                         Terkunci
                                     </div>
                                 @endif
                             </div>
                         </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-24 text-var(--text-3) font-medium italic text-sm">Belum ada periode penilaian yang dikonfigurasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-2xl" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.periode.store') }}" method="POST">
                @csrf
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Tambah Periode Penilaian</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-8 py-6 space-y-5">
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold">Label Keterangan Periode <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_periode" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="Cth: Penilaian Tengah Semester Ganjil">
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Tahun Ajaran <span class="text-red-500">*</span></label>
                            <select name="tahun_ajaran_id" required class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach($tahun_ajaran as $ta)
                                    <option value="{{ $ta->id_tahun_ajaran }}">{{ $ta->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Semester <span class="text-red-500">*</span></label>
                            <select name="semester" required class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                <option value="ganjil">Ganjil</option>
                                <option value="genap">Genap</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold mb-3 block">Kelas Terdaftar <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 p-5 bg-var(--bg) rounded-2xl border border-var(--border)">
                            @foreach($kelas as $k)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="kelas_ids[]" value="{{ $k->id_kelas }}" class="w-4.5 h-4.5 text-var(--accent) border-var(--border) rounded-lg focus:ring-0">
                                    <span class="text-[11px] font-bold text-var(--text-2) group-hover:text-var(--text-1) transition-colors">{{ $k->nama_kelas }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showAdd = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="btn btn-green px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-green-100">Simpan Periode</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-2xl" @click.stop x-transition.scale.95>
            <form :action="'{{ route('admin.periode.index') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-var(--text-1) tracking-tight">Edit Periode Penilaian</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-8 py-6 space-y-5">
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold">Label Keterangan Periode</label>
                        <input type="text" name="nama_periode" x-model="editData.nama_periode" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" x-model="editData.tahun_ajaran_id" required class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                @foreach($tahun_ajaran as $ta)
                                    <option value="{{ $ta->id_tahun_ajaran }}">{{ $ta->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Semester</label>
                            <select name="semester" x-model="editData.semester" required class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                <option value="ganjil">Ganjil</option>
                                <option value="genap">Genap</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" x-model="editData.tanggal_mulai" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" x-model="editData.tanggal_selesai" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold mb-3 block">Kelas Terdaftar</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 p-5 bg-var(--bg) rounded-2xl border border-var(--border)">
                            @foreach($kelas as $k)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="kelas_ids[]" value="{{ $k->id_kelas }}" x-model="editData.kelas_ids" class="w-4.5 h-4.5 text-var(--accent) border-var(--border) rounded-lg focus:ring-0">
                                    <span class="text-[11px] font-bold text-var(--text-2) group-hover:text-var(--text-1) transition-colors">{{ $k->nama_kelas }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showEdit = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="btn btn-blue px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-100">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL HAPUS --}}
    <template x-teleport="body">
    <div x-show="showDelete" x-transition.opacity @keydown.escape.window="showDelete = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-sm" @click.stop x-transition.scale.95>
            <form :action="'{{ route('admin.periode.index') }}/' + deleteData.id" method="POST">
                @csrf @method('DELETE')
                <div class="px-8 py-10 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-var(--text-1) tracking-tight mb-2">Hapus Periode?</h3>
                    <p class="text-sm text-var(--text-3) font-medium mb-6 tracking-tight" x-text="deleteData.nama"></p>
                    <div class="p-4 rounded-xl bg-red-50/50 border border-red-100 text-[10px] font-bold text-red-700">
                        ⚠️ Seluruh data nilai terkait periode ini akan hilang!
                    </div>
                </div>
                <div class="px-8 pb-8 flex gap-3">
                    <button type="button" @click="showDelete = false" class="flex-1 px-4 py-3 rounded-xl text-xs font-bold text-var(--text-3) bg-gray-100 hover:bg-gray-200 transition-all">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 transition-all shadow-lg shadow-red-100">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>

@push('scripts')
<script>
    function confirmProses(periodeId) {
        Swal.fire({
            title: 'Proses Evaluasi (SPK)?',
            html: "<p class='text-sm text-gray-600'>Sistem akan menjalankan kalkulasi <strong>Fuzzy SMART</strong> untuk seluruh siswa pada periode ini.</p><br><p class='text-xs text-amber-600 font-bold'>Setelah diproses, guru perlu memvalidasi setiap hasil sebelum dapat dipublikasikan.</p>",
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Ya, Proses Sekarang',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3b82f6',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-proses-' + periodeId).submit();
            }
        });
    }

    function confirmPublish(periodeId, pending, total) {
        if (pending > 0) {
            Swal.fire({
                title: 'Belum Semua Divalidasi',
                html: `<p class='text-sm text-gray-600'>Masih <strong>${pending} dari ${total}</strong> evaluasi yang belum divalidasi guru.</p><br><p class='text-xs text-gray-500'>Minta guru untuk menyelesaikan validasi terlebih dahulu sebelum dipublikasikan.</p>`,
                icon: 'warning',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#f59e0b',
            });
            return;
        }
        Swal.fire({
            title: 'Publikasikan Hasil Evaluasi?',
            html: "<p class='text-sm text-gray-600'>Seluruh guru sudah memvalidasi. Setelah dipublikasikan, hasil akan langsung dapat dilihat oleh <strong>wali murid</strong> dan <strong>kepala sekolah</strong>.</p>",
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: '🎉 Ya, Publikasikan!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-publish-' + periodeId).submit();
            }
        });
    }
</script>
@endpush
@endsection
