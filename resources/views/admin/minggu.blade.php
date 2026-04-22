@extends('layouts.app')

@section('title', 'Manajemen Minggu Penilaian')

@section('content')
<div x-data="{ 
    showAdd: false, 
    showEdit: false, 
    showDetail: false,
    showDelete: false,
    
    addPeriodeId: '',
    addMingguKe: '',
    
    rawWeeks: {{ Js::from($existingWeeks) }},
    
    editData: {
        id: '',
        periode_id: '',
        minggu_ke: '',
        tema: '',
        tanggal_mulai: '',
        tanggal_selesai: '',
        subkriteria_ids: []
    },
    
    detailData: {
        id: '',
        periode_label: '',
        minggu_ke: '',
        tema: '',
        tanggal_mulai: '',
        tanggal_selesai: '',
        status: '',
        subkriteria: []
    },
    
    deleteData: { id: '', nama: '' },
    
    suggestWeek() {
        if (!this.addPeriodeId) {
            this.addMingguKe = '';
            return;
        }
        const weeks = this.rawWeeks.filter(w => w.periode_id == this.addPeriodeId);
        if (weeks.length === 0) {
            this.addMingguKe = 1;
        } else {
            const maxWeek = Math.max(...weeks.map(w => w.minggu_ke));
            this.addMingguKe = maxWeek + 1;
        }
    },
    
    openDetail(m) {
        this.detailData = {
            id: m.id,
            periode_label: m.periode.tahun_ajaran.nama + ' - Sem ' + m.periode.semester,
            minggu_ke: m.minggu_ke,
            tema: m.tema || '-',
            tanggal_mulai: m.tanggal_mulai ? m.tanggal_mulai.split('T')[0] : '',
            tanggal_selesai: m.tanggal_selesai ? m.tanggal_selesai.split('T')[0] : '',
            status: m.status,
            subkriteria: m.subkriteria || []
        };
        this.showDetail = true;
    },
    
    openEdit(m) {
        this.editData = {
            id: m.id,
            periode_id: m.periode_id,
            minggu_ke: m.minggu_ke,
            tema: m.tema || '',
            tanggal_mulai: m.tanggal_mulai ? m.tanggal_mulai.split('T')[0] : '',
            tanggal_selesai: m.tanggal_selesai ? m.tanggal_selesai.split('T')[0] : '',
            subkriteria_ids: m.subkriteria ? m.subkriteria.map(s => s.id) : []
        };
        this.showEdit = true;
    },
    
    openDelete(m) {
        this.deleteData = { id: m.id, nama: 'Minggu Ke-' + m.minggu_ke };
        this.showDelete = true;
    }
}" class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Minggu Penilaian</h1>
            <p class="text-sm text-slate-500 font-medium tracking-tight lh-relaxed">Jadwalkan subkriteria yang akan dinilai oleh guru setiap minggunya.</p>
        </div>
        <button @click="{{ $periode->count() > 0 ? 'showAdd = true; addPeriodeId = \'' . $periode->first()->id . '\'; suggestWeek();' : 'alert(\'Belum ada periode penilaian yang aktif. Silakan aktifkan periode terlebih dahulu di menu Periode Penilaian.\')' }}" 
                class="btn {{ $periode->count() > 0 ? 'btn-green' : 'btn-gray opacity-60' }} shadow-lg shadow-green-100">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Minggu
        </button>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium flex items-center animate-fade-in">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium flex items-center animate-shake">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-medium space-y-1 mb-4 flex flex-col">
            <p class="font-black uppercase tracking-widest text-[10px] mb-1">Terjadi Kesalahan Input:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.minggu.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm mt-5 mb-4">
        <div class="relative flex-1 w-full">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full py-2.5" style="padding-left: 42px;" placeholder="Cari tema minggu...">
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
                    <tr class="bg-slate-50 border-b border-slate-100 uppercase text-[10px] font-black text-slate-400">
                        <th class="px-6 py-4 tracking-wider">No</th>
                        <th class="px-6 py-4 tracking-wider">Periode</th>
                        <th class="px-6 py-4 tracking-wider">Minggu Ke</th>
                        <th class="px-6 py-4 tracking-wider">Tema</th>
                        <th class="px-6 py-4 tracking-wider">Rentang Tanggal</th>
                        <th class="px-6 py-4 tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($minggu as $i => $m)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4 text-xs font-bold text-slate-400">{{ $minggu->firstItem() + $i }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-700 leading-tight">{{ $m->periode->tahunAjaran->nama }}</span>
                                    <span class="text-[10px] font-black uppercase text-slate-400">Sem {{ $m->periode->semester }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-black italic">
                                    M-{{ $m->minggu_ke }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-700 truncate max-w-[150px]">
                                {{ $m->tema ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col text-[11px] space-y-0.5">
                                    <span class="text-slate-500 font-medium">Mulai: <span class="text-slate-800 font-bold">{{ $m->tanggal_mulai->format('d/m/Y') }}</span></span>
                                    <span class="text-slate-500 font-medium">Selesai: <span class="text-slate-800 font-bold">{{ $m->tanggal_selesai->format('d/m/Y') }}</span></span>
                                </div>
                            </td>
                             <td class="px-6 py-4 text-center">
                                 @if($m->status === 'draft')
                                     <form action="{{ route('admin.minggu.status', $m) }}" method="POST">
                                         @csrf @method('PATCH')
                                         <input type="hidden" name="status" value="aktif">
                                         <button type="submit" class="px-3 py-1 bg-amber-50 text-amber-600 border border-amber-200 rounded-lg text-[10px] font-black uppercase hover:bg-amber-100 transition-colors">
                                             Draf (Aktifkan?)
                                         </button>
                                     </form>
                                 @elseif($m->status === 'aktif')
                                     <form action="{{ route('admin.minggu.status', $m) }}" method="POST" onsubmit="return confirm('Finalisasi Minggu akan mengunci input guru dan menjadikannya laporan wali murid. Lanjutkan?')">
                                         @csrf @method('PATCH')
                                         <input type="hidden" name="status" value="selesai">
                                         <button type="submit" class="px-3 py-1 bg-blue-50 text-blue-600 border border-blue-200 rounded-lg text-[10px] font-black uppercase hover:bg-blue-100 transition-colors">
                                             Aktif (Finalisasi?)
                                         </button>
                                     </form>
                                 @else
                                     <span class="px-3 py-1 bg-green-100 text-green-700 border border-green-200 rounded-lg text-[10px] font-black uppercase">
                                         Final Minggu
                                     </span>
                                 @endif
                             </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <button @click="openDetail({{ Js::from($m->load(['subkriteria', 'periode.tahunAjaran'])) }})" class="btn btn-xs btn-gray" title="Detail">Detail</button>
                                    
                                    <button @click="openEdit({{ Js::from($m->load('subkriteria')) }})" 
                                            class="btn btn-xs {{ $m->status === 'draft' ? 'btn-blue' : 'btn-gray opacity-50 cursor-not-allowed' }}" 
                                            :disabled="{{ $m->status !== 'draft' ? 'true' : 'false' }}"
                                            title="Edit Data">Edit</button>
                                    
                                    <button @click="openDelete({{ Js::from($m) }})" class="btn btn-xs btn-gray text-red-500" title="Hapus Data">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm italic">Belum ada jadwal minggu penilaian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($minggu->hasPages())
            <div class="px-6 py-4 border-t border-slate-50 text-xs">
                {{ $minggu->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden" @click.outside="showAdd = false" x-transition.scale.95>
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Tambah Minggu Baru</h3>
                <button @click="showAdd = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.minggu.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-wider">Pilih Periode</label>
                        <select name="periode_id" x-model="addPeriodeId" @change="suggestWeek()" required class="form-input w-full bg-slate-50 font-bold">
                            @foreach($periode as $p)
                                <option value="{{ $p->id }}">{{ $p->tahunAjaran->nama }} - Sem {{ $p->semester }} (AKTIF)</option>
                            @endforeach
                            @if($periode->isEmpty())
                                <option value="">Tidak ada periode aktif</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-wider">Minggu Ke-</label>
                        <input type="number" name="minggu_ke" x-model="addMingguKe" min="1" required class="form-input w-full bg-slate-50 font-black text-blue-600">
                    </div>
                    <div class="form-group col-span-2">
                        <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-wider">Tema / Keterangan (Opsional)</label>
                        <input type="text" name="tema" class="form-input w-full" placeholder="Contoh: Alam Semesta">
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-wider">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required class="form-input w-full">
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-wider">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required class="form-input w-full">
                    </div>
                </div>

                <div class="form-group">
                    <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-wider">Pilih Subkriteria yang Dinilai</label>
                    <div class="bg-slate-50 rounded-xl border border-slate-100 p-4 max-h-64 overflow-y-auto">
                        @php $currentKriteria = ''; @endphp
                        @foreach($subkriteria as $s)
                            @if($currentKriteria !== $s->kriteria->nama)
                                <div class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mt-4 first:mt-0 mb-2 pb-1 border-b border-indigo-100">
                                    {{ $s->kriteria->nama }}
                                </div>
                                @php $currentKriteria = $s->kriteria->nama; @endphp
                            @endif
                            <label class="flex items-center space-x-3 cursor-pointer group mb-2 last:mb-0">
                                <input type="checkbox" name="subkriteria_ids[]" value="{{ $s->id }}" class="w-4 h-4 text-green-600 border-slate-300 rounded focus:ring-green-500">
                                <span class="text-[11px] font-bold text-slate-600 group-hover:text-slate-800 transition-colors">{{ $s->nama }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="showAdd = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-green shadow-lg shadow-green-100 px-8">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL DETAIL --}}
    <template x-teleport="body">
    <div x-show="showDetail" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden" @click.outside="showDetail = false" x-transition.scale.95>
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-blue-600 text-white">
                <h3 class="text-lg font-black tracking-tight" x-text="'Detail Minggu Ke-' + detailData.minggu_ke"></h3>
                <button @click="showDetail = false" class="hover:text-white/70 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[80vh]">
                <div class="grid grid-cols-2 gap-6 mb-8 bg-slate-50 p-4 rounded-xl border border-slate-100 italic">
                    <div>
                        <p class="text-[10px] font-black uppercase text-slate-400 mb-1">Periode</p>
                        <p class="text-sm font-bold text-slate-700" x-text="detailData.periode_label"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-slate-400 mb-1">Status</p>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase" 
                              :class="{
                                  'bg-amber-100 text-amber-600': detailData.status === 'draft',
                                  'bg-blue-100 text-blue-600': detailData.status === 'aktif',
                                  'bg-green-100 text-green-600': detailData.status === 'selesai'
                              }" x-text="detailData.status === 'selesai' ? 'Final Minggu' : detailData.status"></span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-slate-400 mb-1">Tema</p>
                        <p class="text-sm font-bold text-slate-700" x-text="detailData.tema"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-slate-400 mb-1">Rentang Tanggal</p>
                        <p class="text-sm font-bold text-slate-700" x-text="detailData.tanggal_mulai + ' s/d ' + detailData.tanggal_selesai"></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-xs font-black uppercase text-slate-500 tracking-wider">Subkriteria yang Dijadwalkan:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <template x-for="s in detailData.subkriteria" :key="s.id">
                            <div class="flex items-center p-3 bg-white border border-slate-100 rounded-lg shadow-sm">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-xs font-bold text-slate-700" x-text="s.nama"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end box-shadow border-t border-slate-100">
                <button @click="showDetail = false" class="btn btn-gray px-12">Tutup</button>
            </div>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden" @click.outside="showEdit = false" x-transition.scale.95>
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-black text-slate-800">Edit Minggu Penilaian</h3>
                <button @click="showEdit = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="'{{ route('admin.minggu.index') }}/' + editData.id" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-wider">Pilih Periode</label>
                        <select name="periode_id" x-model="editData.periode_id" required class="form-input w-full">
                            @foreach($periode as $p)
                                <option value="{{ $p->id }}">{{ $p->tahunAjaran->nama }} - Sem {{ $p->semester }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-wider">Minggu Ke-</label>
                        <input type="number" name="minggu_ke" x-model="editData.minggu_ke" min="1" required class="form-input w-full bg-slate-50">
                    </div>
                    <div class="form-group col-span-2">
                        <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-wider">Tema / Keterangan</label>
                        <input type="text" name="tema" x-model="editData.tema" class="form-input w-full">
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-wider">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" x-model="editData.tanggal_mulai" required class="form-input w-full">
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-wider">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" x-model="editData.tanggal_selesai" required class="form-input w-full">
                    </div>
                </div>

                <div class="form-group">
                    <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-wider">Pilih Subkriteria yang Dinilai</label>
                    <div class="bg-slate-50 rounded-xl border border-slate-100 p-4 max-h-64 overflow-y-auto">
                        @php $currentKriteria = ''; @endphp
                        @foreach($subkriteria as $s)
                            @if($currentKriteria !== $s->kriteria->nama)
                                <div class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mt-4 first:mt-0 mb-2 pb-1 border-b border-indigo-100">
                                    {{ $s->kriteria->nama }}
                                </div>
                                @php $currentKriteria = $s->kriteria->nama; @endphp
                            @endif
                            <label class="flex items-center space-x-3 cursor-pointer group mb-2 last:mb-0">
                                <input type="checkbox" name="subkriteria_ids[]" value="{{ $s->id }}" x-model="editData.subkriteria_ids" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                                <span class="text-[11px] font-bold text-slate-600 group-hover:text-slate-800 transition-colors">{{ $s->nama }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="showEdit = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-blue shadow-lg shadow-blue-100 px-8">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL HAPUS --}}
    <template x-teleport="body">
    <div x-show="showDelete" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden" @click.outside="showDelete = false" x-transition.scale.95>
            <div class="p-6 text-center text-sm">
                <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg font-black text-slate-800 mb-2">Hapus Minggu?</h3>
                <p class="text-slate-500 mb-6 font-medium leading-relaxed italic">Anda akan menghapus <span class="font-bold text-slate-900" x-text="deleteData.nama"></span>.</p>
                
                <form :action="'{{ route('admin.minggu.index') }}/' + deleteData.id" method="POST" class="flex flex-col gap-2">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-red w-full justify-center py-3">Hapus Selamanya</button>
                    <button type="button" @click="showDelete = false" class="btn btn-gray w-full justify-center py-3">Kembali</button>
                </form>
            </div>
        </div>
    </div>
    </template>

</div>
@endsection
