@extends('layouts.app')

@section('title', 'Manajemen Periode Penilaian')

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
            id: p.id,
            nama_periode: p.nama_periode,
            tahun_ajaran_id: p.tahun_ajaran_id,
            semester: p.semester,
            tanggal_mulai: p.tanggal_mulai ? p.tanggal_mulai.split('T')[0] : '',
            tanggal_selesai: p.tanggal_selesai ? p.tanggal_selesai.split('T')[0] : '',
            kelas_ids: p.kelas ? p.kelas.map(k => k.id) : []
        };
        this.showEdit = true;
    },
    
    openDelete(p) {
        this.deleteData = { id: p.id, nama: p.nama_periode };
        this.showDelete = true;
    }
}" class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Periode Penilaian</h1>
            <p class="text-sm text-slate-500">Manajemen jadwal penilaian per semester dan per kelas.</p>
        </div>
        <button @click="showAdd = true" class="btn btn-green shadow-lg shadow-green-100">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Periode
        </button>
    </div>

    {{-- STATS / ALERTS --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium flex items-center">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium flex items-center">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.periode.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm mt-5 mb-4">
        <div class="relative flex-1 w-full">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full py-2.5" style="padding-left: 42px;" placeholder="Cari periode...">
        </div>
        <div class="flex gap-2 w-full md:w-auto shrink-0 md:pl-2">
            <button type="submit" class="btn btn-blue py-2.5 px-6 shadow-sm">Cari</button>
        </div>
    </form>

    {{-- MAIN TABLE --}}
    <div class="card overflow-hidden border-none shadow-sm bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">No</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">Tahun Ajaran</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">Periode / Semester</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">Kelas Terkait</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">Rentang Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($periode as $i => $p)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4 text-xs font-bold text-slate-400">{{ $periode->firstItem() + $i }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-slate-700 block">{{ $p->tahunAjaran->nama }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-800 leading-tight">{{ $p->nama_periode }}</span>
                                    <span class="text-[10px] font-black uppercase {{ $p->semester == 'ganjil' ? 'text-amber-600' : 'text-blue-600' }}">
                                        Semester {{ $p->semester }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @foreach($p->kelas as $kls)
                                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[10px] font-bold border border-indigo-100">
                                            {{ $kls->nama_kelas }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col text-xs space-y-0.5">
                                    <span class="text-slate-500"><span class="font-bold text-slate-700">Mulai:</span> {{ $p->tanggal_mulai->format('d/m/Y') }}</span>
                                    <span class="text-slate-500"><span class="font-bold text-slate-700">Selesai:</span> {{ $p->tanggal_selesai->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.periode.toggle', $p) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="focus:outline-none">
                                        {!! $p->status_badge !!}
                                    </button>
                                </form>
                            </td>
                             <td class="px-6 py-4 text-right">
                                 <div class="flex justify-end gap-1">
                                     @if($p->canBeFinalized())
                                         <form action="{{ route('admin.periode.finalize', $p) }}" method="POST" onsubmit="return confirm('Finalisasi periode akan mengunci seluruh data dan menghitung skor SPK. Lanjutkan?')">
                                             @csrf
                                             <button type="submit" class="btn btn-xs btn-green" title="Finalisasi & Hitung SPK">Finalisasi</button>
                                         </form>
                                     @endif

                                     @if(!$p->isFinal())
                                         <button @click="openEdit({{ Js::from($p) }})" class="btn btn-xs btn-blue" title="Edit Data">Edit</button>
                                         <button @click="openDelete({{ Js::from($p) }})" class="btn btn-xs btn-gray text-red-500" title="Hapus Data">
                                             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                         </button>
                                     @else
                                         <span class="text-[10px] font-bold text-slate-400 italic">Terkunci</span>
                                     @endif
                                 </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="text-sm text-slate-400 font-medium">Belum ada periode penilaian yang dibuat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($periode->hasPages())
            <div class="px-6 py-4 border-t border-slate-50">
                {{ $periode->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden" @click.outside="showAdd = false" x-transition.scale.95>
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-black text-slate-800">Tambah Periode Baru</h3>
                <button @click="showAdd = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.periode.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group col-span-2">
                        <label class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 block">Nama Periode / Keterangan</label>
                        <input type="text" name="nama_periode" required class="form-input w-full" placeholder="Contoh: Penilaian Tengah Semester Ganjil">
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 block">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" required class="form-input w-full">
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($tahun_ajaran as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 block">Semester</label>
                        <select name="semester" required class="form-input w-full">
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 block">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required class="form-input w-full">
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 block">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required class="form-input w-full">
                    </div>
                </div>

                <div class="form-group">
                    <label class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 block">Pilih Kelas</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                        @foreach($kelas as $k)
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="checkbox" name="kelas_ids[]" value="{{ $k->id }}" class="w-4 h-4 text-green-600 border-slate-300 rounded focus:ring-green-500">
                                <span class="text-xs font-bold text-slate-600 group-hover:text-slate-800 transition-colors">{{ $k->nama_kelas }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="showAdd = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-green shadow-lg shadow-green-100">Simpan Periode</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden" @click.outside="showEdit = false" x-transition.scale.95>
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-black text-slate-800">Edit Periode</h3>
                <button @click="showEdit = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="'{{ route('admin.periode.index') }}/' + editData.id" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group col-span-2">
                        <label class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 block">Nama Periode / Keterangan</label>
                        <input type="text" name="nama_periode" x-model="editData.nama_periode" required class="form-input w-full">
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 block">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" x-model="editData.tahun_ajaran_id" required class="form-input w-full">
                            @foreach($tahun_ajaran as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 block">Semester</label>
                        <select name="semester" x-model="editData.semester" required class="form-input w-full">
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 block">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" x-model="editData.tanggal_mulai" required class="form-input w-full">
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 block">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" x-model="editData.tanggal_selesai" required class="form-input w-full">
                    </div>
                </div>

                <div class="form-group">
                    <label class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 block">Pilih Kelas</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                        @foreach($kelas as $k)
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="checkbox" name="kelas_ids[]" value="{{ $k->id }}" x-model="editData.kelas_ids" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                                <span class="text-xs font-bold text-slate-600 group-hover:text-slate-800 transition-colors">{{ $k->nama_kelas }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="showEdit = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-blue shadow-lg shadow-blue-100">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL HAPUS --}}
    <template x-teleport="body">
    <div x-show="showDelete" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden" @click.outside="showDelete = false" x-transition.scale.95>
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg font-black text-slate-800 mb-2">Hapus Periode?</h3>
                <p class="text-sm text-slate-500 mb-6">Anda akan menghapus periode <span class="font-bold text-slate-800" x-text="deleteData.nama"></span>. Tindakan ini tidak dapat dibatalkan.</p>
                
                <form :action="'{{ route('admin.periode.index') }}/' + deleteData.id" method="POST" class="flex flex-col gap-2">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-red w-full justify-center py-3">Ya, Hapus Sekarang</button>
                    <button type="button" @click="showDelete = false" class="btn btn-gray w-full justify-center py-3">Batalkan</button>
                </form>
            </div>
        </div>
    </div>
    </template>

</div>
@endsection
