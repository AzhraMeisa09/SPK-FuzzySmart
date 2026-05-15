@extends('layouts.app')
@section('title', 'Manajemen Kelas')
@section('page-title', 'Manajemen Kelas')

@section('content')

<div x-data="{
    showAdd: {{ $errors->any() && !session('edit_id') ? 'true' : 'false' }},
    showEdit: {{ session('edit_id') ? 'true' : 'false' }},
    showDelete: false,
    editData: {
        id: '{{ old('id_kelas', session('edit_data.id_kelas')) }}',
        nama: '{{ old('nama_kelas', session('edit_data.nama_kelas')) }}',
        tahun_ajaran_id: '{{ old('tahun_ajaran_id', session('edit_data.tahun_ajaran_id')) }}',
        guru_ids: {{ json_encode(old('guru_ids', session('edit_data.guru_ids', []))) }}
    },
    deleteData: {},
    openEdit(k) { 
        this.editData = {
            id: k.id_kelas,
            nama: k.nama_kelas,
            tahun_ajaran_id: k.tahun_ajaran_id,
            guru_ids: k.guru.map(g => g.id_user.toString())
        }; 
        this.showEdit = true; 
    },
    openDelete(k) { this.deleteData = k; this.showDelete = true; }
}" class="space-y-6">

    {{-- ── HEADER CARD ── --}}
    <div class="card p-6 shadow-xl border-none">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Manajemen Ruang Kelas</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Kelola pengorganisasian kelas, tahun ajaran, dan guru pengampu.</p>
            </div>
            
            <div class="flex flex-wrap gap-3 items-center">
                <form action="{{ route('admin.kelas.index') }}" method="GET" class="w-full lg:w-auto">
                    <div class="search-box lg:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari nama kelas...">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </form>

                <button @click="showAdd = true" class="btn btn-green shadow-lg shadow-green-100 px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Kelas
                </button>
            </div>
        </div>
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="card overflow-hidden shadow-xl border-none">
        <table class="tbl">
            <thead>
                <tr>
                    <th class="w-20 text-center">No</th>
                    <th>Nama Kelas</th>
                    <th>Tahun Ajaran</th>
                    <th>Guru Pengampu</th>
                    <th class="text-center">Kapasitas</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kelas as $i => $k)
                    <tr class="hover:bg-var(--bg) transition-colors group">
                        <td class="text-center py-4 text-var(--text-3) text-xs font-bold">{{ $kelas->firstItem() + $i }}</td>
                        <td class="py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-green-50 text-green-600 font-bold text-sm flex items-center justify-center border border-green-100 shadow-sm">{{ substr($k->nama_kelas, 0, 1) }}</div>
                                <span class="font-semibold text-var(--text-1) tracking-tight">{{ $k->nama_kelas }}</span>
                            </div>
                        </td>
                        <td class="py-4">
                            <span class="text-xs font-bold text-var(--text-2) bg-gray-100 px-2.5 py-1 rounded-lg border border-gray-200">{{ $k->tahunAjaran->nama ?? '-' }}</span>
                        </td>
                        <td class="py-4">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($k->guru as $g)
                                    <span class="badge badge-blue shadow-sm">{{ $g->nama_lengkap }}</span>
                                @empty
                                    <span class="text-[10px] text-var(--text-3) italic font-medium">Belum ada guru pengampu</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="text-center py-4">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="text-xs font-bold text-var(--text-1)">{{ $k->siswa->count() }}</span>
                                <span class="text-[10px] text-var(--text-3) font-medium">Siswa</span>
                            </div>
                        </td>
                        <td class="text-center py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openEdit({{ json_encode($k) }})" class="p-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="openDelete({ id: '{{ $k->id_kelas }}', nama: '{{ addslashes($k->nama_kelas) }}' })" class="p-2 rounded-xl bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-24 text-var(--text-3) font-medium italic text-sm">Belum ada data kelas yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($kelas->hasPages())
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
            {{ $kelas->links() }}
        </div>
        @endif
    </div>

    {{-- ── MODALS ── --}}
    
    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-lg" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Tambah Kelas</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="px-8 py-6 space-y-5">
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Nama Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kelas" value="{{ old('nama_kelas') }}" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm @error('nama_kelas') border-red-500 @enderror" placeholder="Contoh: Anggrek A">
                        @error('nama_kelas') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <select name="tahun_ajaran_id" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm @error('tahun_ajaran_id') border-red-500 @enderror">
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($tahunAjaran as $ta)
                                <option value="{{ $ta->id_tahun_ajaran }}" {{ old('tahun_ajaran_id') == $ta->id_tahun_ajaran ? 'selected' : '' }}>
                                    {{ $ta->nama }} {{ $ta->is_aktif ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group" x-data="{
                        open: false,
                        selected: {{ json_encode(old('guru_ids', [])) }},
                        options: [
                            @foreach($guru as $g)
                                { id: '{{ $g->id_user }}', name: '{{ addslashes($g->nama_lengkap) }}' },
                            @endforeach
                        ],
                        get selectedNames() {
                            if (this.selected.length === 0) return 'Pilih Guru Pengampu...';
                            let names = this.options.filter(o => this.selected.includes(o.id_user ? o.id_user.toString() : o.id.toString())).map(o => o.name);
                            if (names.length > 2) return names.slice(0, 2).join(', ') + '... (+' + (names.length - 2) + ')';
                            return names.join(', ');
                        }
                    }">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Guru Pengampu</label>
                        <div class="relative">
                            <div @click="open = !open" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm flex justify-between items-center cursor-pointer min-h-[46px]">
                                <span x-text="selectedNames" :class="selected.length === 0 ? 'text-gray-400 font-medium' : 'text-var(--text-1)'"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            
                            <div x-show="open" @click.away="open = false" class="absolute z-[60] w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl max-h-60 overflow-y-auto p-2" x-transition x-cloak>
                                <template x-for="option in options" :key="option.id">
                                    <label class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 rounded-xl cursor-pointer transition-colors group">
                                        <input type="checkbox" :value="option.id" x-model="selected" name="guru_ids[]" class="w-5 h-5 rounded-lg text-var(--accent) focus:ring-var(--accent) border-gray-300">
                                        <span x-text="option.name" class="text-xs font-bold text-gray-700 group-hover:text-var(--accent) transition-colors"></span>
                                    </label>
                                </template>
                                <div x-show="options.length === 0" class="px-4 py-3 text-xs text-gray-400 italic">Belum ada data guru pengampu.</div>
                            </div>
                        </div>
                        @error('guru_ids.*') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showAdd = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="px-8 py-2.5 rounded-xl font-bold text-white bg-green-600 hover:bg-green-700 transition-all shadow-lg shadow-green-100">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-lg" @click.stop x-transition.scale.95>
            <form :action="'{{ route('admin.kelas.index') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Edit Kelas</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="px-8 py-6 space-y-5">
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Nama Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kelas" x-model="editData.nama" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm">
                    </div>

                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <select name="tahun_ajaran_id" x-model="editData.tahun_ajaran_id" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm">
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($tahunAjaran as $ta)
                                <option value="{{ $ta->id_tahun_ajaran }}">{{ $ta->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" x-data="{
                        open: false,
                        options: [
                            @foreach($guru as $g)
                                { id: '{{ $g->id_user }}', name: '{{ addslashes($g->nama_lengkap) }}' },
                            @endforeach
                        ],
                        get selectedNames() {
                            if (!editData.guru_ids || editData.guru_ids.length === 0) return 'Pilih Guru Pengampu...';
                            let names = this.options.filter(o => editData.guru_ids.includes(o.id_user ? o.id_user.toString() : o.id.toString())).map(o => o.name);
                            if (names.length > 2) return names.slice(0, 2).join(', ') + '... (+' + (names.length - 2) + ')';
                            return names.join(', ');
                        }
                    }">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Guru Pengampu</label>
                        <div class="relative">
                            <div @click="open = !open" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm flex justify-between items-center cursor-pointer min-h-[46px]">
                                <span x-text="selectedNames" class="text-var(--text-1)"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            
                            <div x-show="open" @click.away="open = false" class="absolute z-[60] w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl max-h-60 overflow-y-auto p-2" x-transition x-cloak>
                                <template x-for="option in options" :key="option.id">
                                    <label class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 rounded-xl cursor-pointer transition-colors group">
                                        <input type="checkbox" :value="option.id" x-model="editData.guru_ids" name="guru_ids[]" class="w-5 h-5 rounded-lg text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span x-text="option.name" class="text-xs font-bold text-gray-700 group-hover:text-blue-600 transition-colors"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showEdit = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="px-8 py-2.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL HAPUS --}}
    <template x-teleport="body">
    <div x-show="showDelete" x-transition.opacity @keydown.escape.window="showDelete = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition.scale.95>
            <form :action="'{{ route('admin.kelas.index') }}/' + deleteData.id" method="POST">
                @csrf @method('DELETE')
                <div class="px-8 py-10 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-var(--text-1) tracking-tight mb-2">Hapus Kelas?</h3>
                    <p class="text-sm text-var(--text-3) font-medium mb-6 tracking-tight" x-text="deleteData.nama"></p>
                    <p class="text-[10px] text-red-600 bg-red-50 border border-red-100 rounded-xl p-4 leading-relaxed italic">⚠️ Data siswa dalam kelas ini tidak dapat dihapus secara otomatis. Pastikan memindahkan siswa ke kelas lain sebelum menghapus.</p>
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
