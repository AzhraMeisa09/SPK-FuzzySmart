@extends('layouts.app')
@section('title', 'Manajemen Minggu Penilaian')
@section('page-title', 'Manajemen Minggu Penilaian')

@section('content')
<div x-data="{ 
    showAdd: false, 
    showEdit: false, 
    showDetail: false,
    showDelete: false,
    
    addPeriodeId: '',
    addMingguKe: '',
    
    rawWeeks: {{ Js::from($existingWeeks) }},
    allPeriodes: {{ Js::from($periode->mapWithKeys(fn($p) => [$p->id_periode => ['start' => $p->tanggal_mulai->format('Y-m-d'), 'end' => $p->tanggal_selesai->format('Y-m-d')]])) }},
    
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
            this.addMingguKe = isFinite(maxWeek) ? maxWeek + 1 : 1;
        }
    },
    
    openDetail(m) {
        // Group subkriteria by kriteria name
        const grouped = {};
        (m.subkriteria || []).forEach(s => {
            const kName = s.kriteria ? s.kriteria.nama_kriteria : 'Lainnya';
            if (!grouped[kName]) grouped[kName] = [];
            grouped[kName].push(s);
        });

        let total = 0;
        Object.values(grouped).forEach(arr => total += arr.length);

        this.detailData = {
            id: m.id_minggu,
            periode_label: m.periode.tahun_ajaran.nama + ' - Sem ' + m.periode.semester,
            minggu_ke: m.minggu_ke,
            tema: m.tema || '-',
            tanggal_mulai: m.tanggal_mulai ? m.tanggal_mulai.split('T')[0] : '',
            tanggal_selesai: m.tanggal_selesai ? m.tanggal_selesai.split('T')[0] : '',
            status: m.status,
            groupedSubkriteria: grouped,
            totalSub: total
        };
        this.showDetail = true;
    },

    isSubDisabled(subId, mode) {
        if (mode !== 'add') return false;
        let currentPeriodeId = this.addPeriodeId;
        if (!currentPeriodeId) return false;
        return this.rawWeeks.some(w => 
            w.periode_id == currentPeriodeId && 
            w.subkriteria_ids.includes(subId)
        );
    },
    
    openEdit(m) {
        this.editData = {
            id: m.id_minggu,
            periode_id: m.periode_id,
            minggu_ke: m.minggu_ke,
            tema: m.tema || '',
            tanggal_mulai: m.tanggal_mulai ? m.tanggal_mulai.split('T')[0] : '',
            tanggal_selesai: m.tanggal_selesai ? m.tanggal_selesai.split('T')[0] : '',
            subkriteria_ids: m.subkriteria ? m.subkriteria.map(s => s.id_subkriteria) : []
        };
        this.showEdit = true;
    },
    
    openDelete(m) {
        this.deleteData = { id: m.id_minggu, nama: 'Minggu Ke-' + m.minggu_ke };
        this.showDelete = true;
    },
} " class="space-y-6">

    {{-- HEADER --}}
    <div class="card p-6 shadow-xl border-none">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Penjadwalan Mingguan</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Atur distribusi subkriteria penilaian untuk setiap minggu akademik.</p>
            </div>
            
            <div class="flex flex-wrap gap-3 items-center">
                <form action="{{ route('admin.minggu.index') }}" method="GET" class="w-full lg:w-auto">
                    <div class="search-box lg:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari tema minggu...">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </form>

                <button @click="{{ $periode->count() > 0 ? 'showAdd = true; addPeriodeId = \'' . $periode->first()->id_periode . '\'; suggestWeek();' : 'alert(\'Belum ada periode penilaian yang aktif.\')' }}" 
                        class="btn {{ $periode->count() > 0 ? 'btn-green shadow-lg shadow-green-100' : 'btn-gray opacity-60' }} px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                    Tambah Minggu
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
                    <th>Periode & Semester</th>
                    <th>Identitas</th>
                    <th>Tema Pembelajaran</th>
                    <th>Rentang Waktu</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($minggu as $i => $m)
                    <tr class="hover:bg-var(--bg) transition-colors">
                        <td class="text-var(--text-3) text-[11px] font-bold">{{ $minggu->firstItem() + $i }}</td>
                        <td>
                            <div class="flex flex-col">
                                <span class="font-semibold text-var(--text-1) leading-tight">{{ $m->periode->tahunAjaran->nama }}</span>
                                <span class="text-[10px] font-medium text-var(--text-3) mt-0.5 tracking-wide">Semester {{ $m->periode->semester }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="px-2.5 py-1.5 bg-var(--accent-lt) text-var(--accent) rounded-lg text-[10px] font-bold border border-var(--accent)/10">
                                Minggu Ke-{{ $m->minggu_ke }}
                            </span>
                        </td>
                        <td>
                            <span class="text-xs font-semibold text-var(--text-1) tracking-tight">{{ $m->tema ?: '—' }}</span>
                        </td>
                        <td>
                            <div class="flex flex-col gap-1 text-[11px] font-bold">
                                <div class="flex items-center gap-2 text-var(--text-2)">
                                    <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $m->tanggal_mulai->translatedFormat('d M Y') }}
                                </div>
                                <div class="flex items-center gap-2 text-var(--text-3)">
                                    <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $m->tanggal_selesai->translatedFormat('d M Y') }}
                                </div>
                            </div>
                        </td>
                         <td class="text-center">
                             @if($m->status === 'draft')
                                 <form action="{{ route('admin.minggu.status', $m->id_minggu) }}" method="POST">
                                     @csrf @method('PATCH')
                                     <input type="hidden" name="status" value="aktif">
                                     <button type="submit" class="px-3 py-1.5 bg-amber-50 text-amber-600 border border-amber-100 rounded-xl text-[9px] font-bold hover:bg-amber-600 hover:text-white transition-all">
                                         Draf (Aktifkan)
                                     </button>
                                 </form>
                             @elseif($m->status === 'aktif')
                                 <form action="{{ route('admin.minggu.status', $m->id_minggu) }}" method="POST">
                                     @csrf @method('PATCH')
                                     <input type="hidden" name="status" value="selesai">
                                     <button type="submit" class="px-3 py-1.5 bg-blue-50 text-blue-600 border border-blue-100 rounded-xl text-[9px] font-bold hover:bg-blue-600 hover:text-white transition-all">
                                         Aktif (Kunci)
                                     </button>
                                 </form>
                             @else
                                 <span class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-100 rounded-xl text-[9px] font-bold">
                                     Final
                                 </span>
                             @endif
                         </td>
                        <td>
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openDetail({{ Js::from($m->load(['subkriteria.kriteria', 'periode.tahunAjaran'])) }})" 
                                         class="p-2 rounded-xl bg-white border border-var(--border) text-var(--text-2) hover:text-var(--accent) hover:border-var(--accent) transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                
                                <button @click="openEdit({{ Js::from($m->load('subkriteria')) }})" 
                                        class="p-2 rounded-xl {{ $m->status === 'draft' ? 'bg-blue-50 border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white' : 'bg-gray-50 border-gray-100 text-gray-300 cursor-not-allowed' }} border transition-all shadow-sm" 
                                        :disabled="{{ $m->status !== 'draft' ? 'true' : 'false' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                
                                <button @click="openDelete({{ Js::from($m) }})" 
                                        class="p-2 rounded-xl {{ $m->status === 'draft' ? 'bg-red-50 border-red-100 text-red-600 hover:bg-red-600 hover:text-white' : 'bg-gray-50 border-gray-100 text-gray-300 cursor-not-allowed' }} border transition-all shadow-sm"
                                        :disabled="{{ $m->status !== 'draft' ? 'true' : 'false' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-24 text-var(--text-3) font-medium italic text-sm">Belum ada agenda penilaian mingguan yang dijadwalkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-2xl" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.minggu.store') }}" method="POST">
                @csrf
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Tambah Agenda Minggu</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-8 py-6 space-y-5 max-h-[70vh] overflow-y-auto scrollbar-hide">
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Periode Akademik <span class="text-red-500">*</span></label>
                            <select name="periode_id" x-model="addPeriodeId" @change="suggestWeek()" required class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                @foreach($periode as $p)
                                    <option value="{{ $p->id_periode }}">{{ $p->tahunAjaran->nama }} - Sem {{ $p->semester }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Minggu Ke- <span class="text-red-500">*</span></label>
                            <input type="number" name="minggu_ke" x-model="addMingguKe" min="1" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs text-blue-600">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold">Tema Pembelajaran</label>
                        <input type="text" name="tema" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="Cth: Alam Semesta / Budaya Lokal">
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" required 
                                   :min="allPeriodes[addPeriodeId]?.start" 
                                   :max="allPeriodes[addPeriodeId]?.end"
                                   class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" required 
                                   :min="allPeriodes[addPeriodeId]?.start" 
                                   :max="allPeriodes[addPeriodeId]?.end"
                                   class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold mb-3 block">Subkriteria Target Penilaian</label>
                        <div class="bg-var(--bg) rounded-2xl border border-var(--border) p-5 space-y-4">
                            @php $currentKriteria = ''; @endphp
                            @foreach($subkriteria as $s)
                                @if($s->kriteria && $currentKriteria !== $s->kriteria->nama_kriteria)
                                    <div class="text-[9px] font-bold text-var(--accent) mt-4 first:mt-0 mb-2 pb-1 border-b border-var(--accent)/10">
                                        {{ $s->kriteria->nama_kriteria }}
                                    </div>
                                    @php $currentKriteria = $s->kriteria->nama_kriteria; @endphp
                                @endif
                                <label class="flex items-center gap-3 group" :class="isSubDisabled('{{ $s->id_subkriteria }}', 'add') ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'">
                                    <input type="checkbox" name="subkriteria_ids[]" value="{{ $s->id_subkriteria }}" 
                                           :disabled="isSubDisabled('{{ $s->id_subkriteria }}', 'add')"
                                           class="w-4.5 h-4.5 text-var(--accent) border-var(--border) rounded-lg focus:ring-0">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-bold text-var(--text-2) group-hover:text-var(--text-1) transition-colors">{{ $s->nama_subkriteria }}</span>
                                        <template x-if="isSubDisabled('{{ $s->id_subkriteria }}', 'add')">
                                            <span class="text-[8px] font-bold text-amber-600 mt-0.5 tracking-tight">Terjadwal di minggu lain</span>
                                        </template>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showAdd = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="btn btn-green px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-green-100">Simpan Agenda</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-2xl" @click.stop x-transition.scale.95>
            <form :action="'{{ route('admin.minggu.index') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Edit Agenda Minggu</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-8 py-6 space-y-5 max-h-[70vh] overflow-y-auto scrollbar-hide">
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Periode Akademik <span class="text-red-500">*</span></label>
                            <select name="periode_id" x-model="editData.periode_id" required class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                @foreach($periode as $p)
                                    <option value="{{ $p->id_periode }}">{{ $p->tahunAjaran->nama }} - Sem {{ $p->semester }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Minggu Ke- <span class="text-red-500">*</span></label>
                            <input type="number" name="minggu_ke" x-model="editData.minggu_ke" min="1" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs text-blue-600">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold">Tema Pembelajaran</label>
                        <input type="text" name="tema" x-model="editData.tema" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="Cth: Alam Semesta / Budaya Lokal">
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" x-model="editData.tanggal_mulai" required 
                                   :min="allPeriodes[editData.periode_id]?.start" 
                                   :max="allPeriodes[editData.periode_id]?.end"
                                   class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" x-model="editData.tanggal_selesai" required 
                                   :min="allPeriodes[editData.periode_id]?.start" 
                                   :max="allPeriodes[editData.periode_id]?.end"
                                   class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold mb-3 block">Subkriteria Target Penilaian</label>
                        <div class="bg-var(--bg) rounded-2xl border border-var(--border) p-5 space-y-4">
                            @php $currentKriteria = ''; @endphp
                            @foreach($subkriteria as $s)
                                @if($s->kriteria && $currentKriteria !== $s->kriteria->nama_kriteria)
                                    <div class="text-[9px] font-bold text-var(--accent) mt-4 first:mt-0 mb-2 pb-1 border-b border-var(--accent)/10">
                                        {{ $s->kriteria->nama_kriteria }}
                                    </div>
                                    @php $currentKriteria = $s->kriteria->nama_kriteria; @endphp
                                @endif
                                <label class="flex items-center gap-3 group cursor-pointer">
                                    <input type="checkbox" name="subkriteria_ids[]" value="{{ $s->id_subkriteria }}" 
                                           x-model="editData.subkriteria_ids"
                                           class="w-4.5 h-4.5 text-var(--accent) border-var(--border) rounded-lg focus:ring-0">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-bold text-var(--text-2) group-hover:text-var(--text-1) transition-colors">{{ $s->nama_subkriteria }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showEdit = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="btn btn-green px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-green-100">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL DETAIL --}}
    <template x-teleport="body">
    <div x-show="showDetail" x-transition.opacity @keydown.escape.window="showDetail = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-xl" @click.stop x-transition.scale.95>
            <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-white shadow-sm">
                <div>
                    <h3 class="text-base font-bold text-gray-800" x-text="'Agenda Minggu Ke-' + detailData.minggu_ke"></h3>
                    <p class="text-[10px] text-var(--text-3) font-bold mt-0.5">Rincian Jadwal Penilaian</p>
                </div>
                <button @click="showDetail = false" class="p-2 rounded-xl hover:bg-gray-100 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="px-8 py-6 space-y-8 max-h-[75vh] overflow-y-auto scrollbar-hide">
                {{-- INFO SUMMARY --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100/50">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider">Periode Aktif</span>
                        </div>
                        <p class="text-xs font-bold text-gray-800" x-text="detailData.periode_label"></p>
                    </div>
                    <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-100/50">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-amber-100 rounded-lg text-amber-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider">Tema Terpilih</span>
                        </div>
                        <p class="text-xs font-bold text-gray-800" x-text="detailData.tema"></p>
                    </div>
                    <div class="col-span-2 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <div class="flex items-center justify-center gap-6">
                            <div class="text-center">
                                <span class="text-[9px] font-bold text-gray-400 uppercase block mb-1">Mulai</span>
                                <span class="text-xs font-bold text-gray-700" x-text="detailData.tanggal_mulai"></span>
                            </div>
                            <div class="flex items-center text-gray-300">
                                <div class="w-12 h-[2px] bg-gray-200"></div>
                                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                                <div class="w-12 h-[2px] bg-gray-200"></div>
                            </div>
                            <div class="text-center">
                                <span class="text-[9px] font-bold text-gray-400 uppercase block mb-1">Selesai</span>
                                <span class="text-xs font-bold text-gray-700" x-text="detailData.tanggal_selesai"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUBKRITERIA LIST --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h4 class="text-xs font-bold text-gray-800 flex items-center gap-2">
                            <span class="w-2 h-5 bg-var(--accent) rounded-full"></span>
                            Subkriteria Target Penilaian
                        </h4>
                        <span class="px-2 py-0.5 bg-gray-100 text-[10px] font-bold text-gray-500 rounded-lg" x-text="detailData.totalSub + ' Item'"></span>
                    </div>
                    
                    <div class="space-y-6">
                        <template x-for="(subs, kName) in detailData.groupedSubkriteria" :key="kName">
                            <div class="space-y-3">
                                <div class="text-[9px] font-bold text-var(--accent) uppercase tracking-widest border-b border-var(--accent)/10 pb-1 mb-3" x-text="kName"></div>
                                <div class="grid grid-cols-1 gap-2.5">
                                    <template x-for="s in subs" :key="s.id_subkriteria">
                                        <div class="group relative p-3.5 bg-white border border-gray-100 rounded-2xl hover:border-var(--accent) hover:shadow-md transition-all duration-300">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 rounded-lg bg-var(--bg) flex items-center justify-center text-[9px] font-bold text-var(--accent) border border-var(--border)" x-text="s.id_subkriteria"></div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[11px] font-bold text-gray-800 leading-tight" x-text="s.nama_subkriteria"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="px-8 py-5 border-t border-gray-100 flex justify-end bg-gray-50/50">
                <button @click="showDetail = false" class="px-8 py-2.5 rounded-xl text-xs font-bold text-white bg-gray-800 hover:bg-black transition-all shadow-lg shadow-gray-200">Tutup Rincian</button>
            </div>
        </div>
    </div>
    </template>

    {{-- MODAL HAPUS --}}
    <template x-teleport="body">
    <div x-show="showDelete" x-transition.opacity @keydown.escape.window="showDelete = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition.scale.95>
            <form :action="'{{ route('admin.minggu.index') }}/' + deleteData.id" method="POST">
                @csrf @method('DELETE')
                <div class="px-8 py-10 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-var(--text-1) tracking-tight mb-2">Hapus Jadwal?</h3>
                    <p class="text-sm text-var(--text-3) font-medium mb-6 tracking-tight" x-text="deleteData.nama"></p>
                    <div class="p-4 rounded-xl bg-red-50/50 border border-red-100 text-[10px] font-bold text-red-700">
                        ⚠️ Seluruh input guru pada minggu ini akan dihapus
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
@endsection
