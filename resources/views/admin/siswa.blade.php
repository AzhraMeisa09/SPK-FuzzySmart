@extends('layouts.app')
@section('title', 'Manajemen Siswa')
@section('page-title', 'Manajemen Siswa')

@section('content')

@php
    $totalSiswa = \App\Models\Siswa::count();
    $totalL = \App\Models\Siswa::where('jenis_kelamin', 'L')->count();
    $totalP = \App\Models\Siswa::where('jenis_kelamin', 'P')->count();
    $totalKelasCount = \App\Models\Kelas::count();
    $totalWaliTaut = \App\Models\Siswa::whereNotNull('wali_murid_id')->count();
@endphp

<div x-data="{
    showAdd: {{ $errors->any() && !session('edit_id') ? 'true' : 'false' }},
    showEdit: {{ session('edit_id') ? 'true' : 'false' }},
    showDelete: false,
    photoPreview: null,
    parents: {{ json_encode($waliMurid->pluck('alamat', 'id_user')) }},
    addData: {
        alamat: {{ json_encode(old('alamat', '')) }}
    },
    editData: {
        id: {{ json_encode(old('id_siswa', session('edit_data.id_siswa', ''))) }},
        nama: {{ json_encode(old('name', session('edit_data.name', ''))) }},
        kelas_id: {{ json_encode(old('kelas_id', session('edit_data.kelas_id', ''))) }},
        wali_murid_id: {{ json_encode(old('wali_murid_id', session('edit_data.wali_murid_id', ''))) }},
        tanggal_lahir: {{ json_encode(old('tanggal_lahir', session('edit_data.tanggal_lahir', ''))) }},
        jenis_kelamin: {{ json_encode(old('jenis_kelamin', session('edit_data.jenis_kelamin', ''))) }},
        nama_orang_tua: {{ json_encode(old('nama_orang_tua', session('edit_data.nama_orang_tua', ''))) }},
        no_hp_orang_tua: {{ json_encode(old('no_hp_orang_tua', session('edit_data.no_hp_orang_tua', ''))) }},
        alamat: {{ json_encode(old('alamat', session('edit_data.alamat', ''))) }}
    },
    deleteData: {},
    updateAddress(id, type) {
        if (id && this.parents[id]) {
            if (type === 'add') this.addData.alamat = this.parents[id];
            else this.editData.alamat = this.parents[id];
        }
    },
    handleFileChange(event) {
        const file = event.target.files[0];
        if (file) {
            this.photoPreview = URL.createObjectURL(file);
        } else {
            this.photoPreview = null;
        }
    },
    openEdit(s) { 
        this.photoPreview = null;
        this.editData = {
            id: s.id_siswa,
            nama: s.name,
            kelas_id: s.kelas_id,
            wali_murid_id: s.wali_murid_id || '',
            tanggal_lahir: s.tanggal_lahir ? s.tanggal_lahir.split('T')[0] : '',
            jenis_kelamin: s.jenis_kelamin,
            nama_orang_tua: s.nama_orang_tua || '',
            no_hp_orang_tua: s.no_hp_orang_tua || '',
            alamat: s.alamat || ''
        }; 
        this.showEdit = true; 
    },
    openDelete(s) { this.deleteData = s; this.showDelete = true; }
}" class="space-y-6">

    {{-- ── SUMMARY STATS ── --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
        @php
            $stats = [
                ['label' => 'Total Siswa', 'value' => $totalSiswa, 'color' => '#64748b'],
                ['label' => 'Laki-laki',   'value' => $totalL,     'color' => '#3b82f6'],
                ['label' => 'Perempuan',   'value' => $totalP,     'color' => '#ec4899'],
                ['label' => 'Total Kelas', 'value' => $totalKelasCount, 'color' => '#84934A'],
                ['label' => 'Wali Taut',   'value' => $totalWaliTaut,   'color' => '#f59e0b'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="card p-5 shadow-xl border-none flex flex-col items-center justify-center text-center group hover:translate-y-[-2px] transition-all duration-300">
            <span class="text-[9px] font-bold text-gray-400 mb-2 group-hover:text-gray-500 transition-colors">{{ $s['label'] }}</span>
            <span class="text-2xl font-bold tracking-tighter" style="color: {{ $s['color'] }}">{{ $s['value'] }}</span>
        </div>
        @endforeach
    </div>

    {{-- ── HEADER CARD ── --}}
    <div class="card p-6 shadow-xl border-none">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Manajemen Siswa</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Kelola profil, penempatan kelas, dan data perwalian siswa terdaftar.</p>
            </div>
            
            <div class="flex flex-wrap gap-3 items-center">
                <form action="{{ route('admin.siswa.index') }}" method="GET" class="flex flex-wrap gap-2 items-center w-full lg:w-auto">
                    <div class="search-box lg:w-64">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari nama atau NISN...">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div class="relative">
                        <select name="filter_kelas" onchange="this.form.submit()" class="form-select bg-var(--bg) border-var(--border) rounded-xl text-[13px] font-bold h-[42px] min-w-[140px]" style="padding-left: 16px;">
                            <option value="">Semua Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id_kelas }}" {{ request('filter_kelas') == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <button @click="showAdd = true; photoPreview = null" class="btn btn-green shadow-lg shadow-green-100 px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Siswa
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50/50 border border-green-100 text-green-700 rounded-2xl text-[10px] font-bold flex items-center animate-fade-in shadow-sm">
            <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── TABLE CARD ── --}}
    <div class="card overflow-hidden shadow-xl border-none">
        <table class="tbl">
            <thead>
                <tr>
                    <th class="w-16">No</th>
                    <th>Informasi Siswa</th>
                    <th>NISN & Kelas</th>
                    <th>Wali Murid</th>
                    <th>Kontak Ortu</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($siswa as $i => $s)
                    <tr class="hover:bg-var(--bg) transition-colors">
                        <td class="text-var(--text-3) text-[11px] font-bold">{{ $siswa->firstItem() + $i }}</td>
                        <td class="py-4">
                            <div class="flex items-center gap-4">
                                @if($s->foto)
                                    <img src="{{ asset('storage/' . $s->foto) }}" class="w-10 h-10 rounded-2xl object-cover border border-var(--border) shadow-sm">
                                @else
                                    <div class="w-10 h-10 rounded-2xl bg-var(--accent-lt) flex items-center justify-center text-xs font-bold text-var(--accent) border border-var(--accent)/10 shadow-sm">{{ strtoupper(substr($s->name, 0, 1)) }}</div>
                                @endif
                                <div class="flex flex-col">
                                    <span class="font-semibold text-var(--text-1) leading-tight">{{ $s->name }}</span>
                                    <span class="text-[9px] font-medium tracking-wide mt-0.5 {{ $s->jenis_kelamin == 'L' ? 'text-blue-500' : 'text-pink-500' }}">
                                        {{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4">
                            <div class="flex flex-col gap-1.5">
                                <code class="text-[10px] font-medium bg-gray-100 px-2 py-0.5 rounded-lg text-gray-600 border border-gray-200 inline-block w-fit">{{ $s->id_siswa }}</code>
                                @if($s->kelas)
                                    <span class="badge badge-blue shadow-[0_0_8px_rgba(59,130,246,0.15)]">{{ $s->kelas->nama_kelas }}</span>
                                @else
                                    <span class="text-[10px] text-gray-400 italic">Belum ada kelas</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-4">
                            @if($s->wali->count() > 0)
                                @foreach($s->wali as $w)
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-var(--text-1) tracking-tight">{{ $w->nama_lengkap }}</span>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-[10px] text-gray-400 italic font-bold">Belum diatur</span>
                            @endif
                        </td>
                        <td class="py-4">
                             <span class="text-[10px] font-bold text-var(--text-2) tracking-wider">{{ $s->waliMurid ? $s->waliMurid->no_hp : ($s->no_hp_orang_tua ?? '—') }}</span>
                        </td>
                        <td class="text-center py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.siswa.show', $s->id_siswa) }}" class="p-2 rounded-xl bg-white border border-var(--border) text-var(--text-2) hover:text-var(--accent) hover:border-var(--accent) transition-all shadow-sm group">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> 
                                </a>
                                <button @click="openEdit({{ json_encode($s) }})" class="p-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="openDelete({ id: '{{ $s->id_siswa }}', nama: '{{ addslashes($s->name) }}' })" class="p-2 rounded-xl bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-24 text-var(--text-3) font-medium italic text-sm">Belum ada data siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($siswa->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
            {{ $siswa->links() }}
        </div>
        @endif
    </div>

    {{-- ── MODALS ── --}}
    
    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-2xl" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800 tracking-tight">Tambah Siswa Baru</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="px-8 py-6 max-h-[70vh] overflow-y-auto scrollbar-hide space-y-8">
                    <!-- Data Pokok Siswa -->
                    <div>
                        <h4 class="text-[10px] font-bold text-var(--accent) mb-4 pb-1 border-b border-var(--accent)/10">1. Data Pokok Siswa</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold">Nama Lengkap Siswa <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="Cth: Aditya Pratama">
                            </div>
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold">Custom ID (Opsional)</label>
                                <input type="text" name="id_siswa" value="{{ old('id_siswa') }}" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs text-blue-600" placeholder="Cth: S001">
                            </div>
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="jenis_kelamin" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                    <option value="">Pilih</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                            </div>
                            <div class="form-group md:col-span-2">
                                <label class="form-label text-[10px] font-bold">Alamat / Domisili</label>
                                <textarea name="alamat" x-model="addData.alamat" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs h-20 resize-none p-4" placeholder="Alamat lengkap...">{{ old('alamat') }}</textarea>
                            </div>
                            <div class="form-group md:col-span-2">
                                <label class="form-label text-[10px] font-bold">Foto / Pasfoto</label>
                                <div x-show="photoPreview" class="mt-2 mb-4 flex items-center gap-4 animate-fade-in">
                                    <img :src="photoPreview" class="w-24 h-24 rounded-2xl object-cover border-2 border-var(--accent)/20 shadow-sm">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10px] font-bold text-var(--text-1)">Preview Foto Baru</span>
                                        <button type="button" @click="photoPreview = null; $refs.fileInputAdd.value = ''" class="text-[9px] font-bold text-red-500 hover:text-red-700 underline text-left w-fit">Batalkan</button>
                                    </div>
                                </div>
                                <input type="file" name="foto" x-ref="fileInputAdd" @change="handleFileChange" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" accept="image/*">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Akademik -->
                    <div>
                        <h4 class="text-[10px] font-bold text-var(--accent) mb-4 pb-1 border-b border-var(--accent)/10">2. Akademik & Administrasi</h4>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Penempatan Kelas <span class="text-red-500">*</span></label>
                            <select name="kelas_id" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id_kelas }}" {{ old('kelas_id') == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Data Orang Tua/Wali -->
                    <div>
                        <h4 class="text-[10px] font-bold text-var(--accent) mb-4 pb-1 border-b border-var(--accent)/10">3. Data Wali Murid / Orang Tua</h4>
                        <div class="bg-blue-50/50 p-6 rounded-[2rem] border border-blue-100 mb-6">
                            <div class="form-group !mb-0">
                                <label class="form-label text-[10px] font-bold text-blue-700">Tautkan Akun Wali Murid <span class="text-blue-400 font-normal lowercase">(otomatis)</span></label>
                                <select name="wali_murid_id" @change="updateAddress($event.target.value, 'add')" class="form-select rounded-xl bg-white border-blue-100 font-bold text-xs mt-2">
                                    <option value="">-- Tidak ditautkan ke profil User --</option>
                                    @foreach($waliMurid as $w)
                                        <option value="{{ $w->id_user }}" {{ old('wali_murid_id') == $w->id_user ? 'selected' : '' }}>{{ $w->nama_lengkap }} ({{ $w->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold">Nama Orang Tua Manual</label>
                                <input type="text" name="nama_orang_tua" value="{{ old('nama_orang_tua') }}" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="Alternatif jika tanpa akun">
                            </div>
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold">No. HP Orang Tua Manual</label>
                                <input type="text" name="no_hp_orang_tua" value="{{ old('no_hp_orang_tua') }}" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showAdd = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="btn btn-green px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-green-100">Simpan Siswa</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-2xl" @click.stop x-transition.scale.95>
            <form :action="'{{ route('admin.siswa.index') }}/' + editData.id" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800 tracking-tight">Edit Data Siswa</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="px-8 py-6 max-h-[70vh] overflow-y-auto scrollbar-hide space-y-8">
                    <div>
                        <h4 class="text-[10px] font-bold text-var(--accent) mb-4 pb-1 border-b border-var(--accent)/10">1. Data Pokok Siswa</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold">Nama Lengkap Siswa</label>
                                <input type="text" name="name" x-model="editData.nama" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                            </div>
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold">Custom ID</label>
                                <input type="text" name="id_siswa" x-model="editData.id" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs text-blue-600" readonly>
                            </div>
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" x-model="editData.jenis_kelamin" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" x-model="editData.tanggal_lahir" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                            </div>
                            <div class="form-group md:col-span-2">
                                <label class="form-label text-[10px] font-bold">Alamat / Domisili</label>
                                <textarea name="alamat" x-model="editData.alamat" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs h-20 resize-none p-4"></textarea>
                            </div>
                             <div class="form-group md:col-span-2">
                                <label class="form-label text-[10px] font-bold">Update Foto <span class="text-gray-400 font-normal lowercase">(opsional)</span></label>
                                <div x-show="photoPreview" class="mt-2 mb-4 flex items-center gap-4 animate-fade-in">
                                    <img :src="photoPreview" class="w-24 h-24 rounded-2xl object-cover border-2 border-var(--accent)/20 shadow-sm">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10px] font-bold text-var(--text-1)">Preview Foto Baru</span>
                                        <button type="button" @click="photoPreview = null; $refs.fileInputEdit.value = ''" class="text-[9px] font-bold text-red-500 hover:text-red-700 underline text-left w-fit">Batalkan</button>
                                    </div>
                                </div>
                                <input type="file" name="foto" x-ref="fileInputEdit" @change="handleFileChange" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" accept="image/*">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-[10px] font-bold text-var(--accent) mb-4 pb-1 border-b border-var(--accent)/10">2. Akademik & Administrasi</h4>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Penempatan Kelas</label>
                            <select name="kelas_id" x-model="editData.kelas_id" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[10px] font-bold text-var(--accent) mb-4 pb-1 border-b border-var(--accent)/10">3. Data Wali Murid / Orang Tua</h4>
                        <div class="bg-blue-50/50 p-6 rounded-[2rem] border border-blue-100 mb-6">
                            <div class="form-group !mb-0">
                                <label class="form-label text-[10px] font-bold text-blue-700">Tautkan Akun Wali Murid</label>
                                <select name="wali_murid_id" x-model="editData.wali_murid_id" @change="updateAddress($event.target.value, 'edit')" class="form-select rounded-xl bg-white border-blue-100 font-bold text-xs mt-2">
                                    <option value="">-- Tidak ditautkan ke profil User --</option>
                                    @foreach($waliMurid as $w)
                                        <option value="{{ $w->id_user }}">{{ $w->nama_lengkap }} ({{ $w->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold">Nama Orang Tua Manual</label>
                                <input type="text" name="nama_orang_tua" x-model="editData.nama_orang_tua" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                            </div>
                            <div class="form-group">
                                <label class="form-label text-[10px] font-bold">No. HP Orang Tua Manual</label>
                                <input type="text" name="no_hp_orang_tua" x-model="editData.no_hp_orang_tua" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
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
            <form :action="'{{ route('admin.siswa.index') }}/' + deleteData.id" method="POST">
                @csrf @method('DELETE')
                <div class="px-8 py-10 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-var(--text-1) tracking-tight mb-2">Hapus Data Siswa?</h3>
                    <p class="text-sm text-var(--text-3) font-medium mb-6 tracking-tight" x-text="deleteData.nama"></p>
                    <div class="p-4 rounded-xl bg-red-50/50 border border-red-100 text-[10px] font-bold text-red-700">
                        ⚠️ Seluruh data penilaian terkait siswa ini akan ikut terhapus!
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
