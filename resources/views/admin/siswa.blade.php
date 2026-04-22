@extends('layouts.app')
@section('title', 'Manajemen Siswa')
@section('page-title', 'Manajemen Siswa')

@section('content')

<div x-data="{
    showAdd: {{ $errors->any() && !session('edit_id') ? 'true' : 'false' }},
    showEdit: {{ session('edit_id') ? 'true' : 'false' }},
    showDelete: false,
    editData: {
        id: '{{ old('id', session('edit_data.id')) }}',
        kode: '{{ old('kode', session('edit_data.kode')) }}',
        nama: '{{ old('nama', session('edit_data.nama')) }}',
        kelas_id: '{{ old('kelas_id', session('edit_data.kelas_id')) }}',
        wali_murid_id: '{{ old('wali_murid_id', session('edit_data.wali_murid_id')) }}',
        tanggal_lahir: '{{ old('tanggal_lahir', session('edit_data.tanggal_lahir')) }}',
        jenis_kelamin: '{{ old('jenis_kelamin', session('edit_data.jenis_kelamin')) }}',
        nama_orang_tua: '{{ old('nama_orang_tua', session('edit_data.nama_orang_tua')) }}',
        no_hp_orang_tua: '{{ old('no_hp_orang_tua', session('edit_data.no_hp_orang_tua')) }}',
        alamat: '{{ old('alamat', session('edit_data.alamat')) }}'
    },
    deleteData: {},
    openEdit(s) { 
        this.editData = {
            id: s.id,
            kode: s.kode || '',
            nama: s.nama,
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
}" class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-base font-black text-gray-900">Manajemen Siswa</h1>
            <p class="text-xs text-gray-500">{{ $siswa->total() }} siswa terdaftar</p>
        </div>
        <button @click="showAdd = true" class="btn btn-green">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Siswa
        </button>
    </div>

    <form action="{{ route('admin.siswa.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm mt-5 mb-4">
        <div class="relative flex-1 w-full">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full py-2.5" style="padding-left: 42px;" placeholder="Cari nama atau NISN...">
        </div>
        <div class="flex gap-2 w-full md:w-auto shrink-0 md:pl-2">
            <button type="submit" class="btn btn-blue py-2.5 px-6 shadow-sm">Cari</button>
            <select name="filter_kelas" onchange="this.form.submit()" class="form-select w-full md:w-48 py-2.5 bg-gray-50 border-gray-200">
                <option value="">Semua Kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" {{ request('filter_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="card overflow-hidden">
        <table class="tbl">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>NISN</th>
                    <th>Kelas</th>
                    <th>Wali Murid</th>
                    <th>No. HP Orang Tua</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswa as $i => $s)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="text-gray-400 text-xs">{{ $siswa->firstItem() + $i }}</td>
                        <td>
                            <div class="flex items-center gap-2.5">
                                @if($s->foto)
                                    <img src="{{ asset('storage/' . $s->foto) }}" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-black text-blue-600">{{ strtoupper(substr($s->nama, 0, 1)) }}</div>
                                @endif
                                <span class="font-semibold text-gray-800">{{ $s->nama }}</span>
                            </div>
                        </td>
                        <td><code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded text-gray-600 font-mono">{{ $s->kode ?? '-' }}</code></td>
                        <td>
                            @if($s->kelas)
                                <span class="badge badge-blue">{{ $s->kelas->nama_kelas }}</span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="text-xs text-gray-600 font-medium">
                            @if($s->waliMurid)
                                {{ $s->waliMurid->nama_lengkap }} <span class="text-blue-500 ml-1" title="Terkoneksi Akun">✓</span>
                            @else
                                {{ $s->nama_orang_tua ?? '-' }}
                            @endif
                        </td>
                        <td class="text-xs text-gray-500">
                            {{ $s->waliMurid ? $s->waliMurid->no_hp : ($s->no_hp_orang_tua ?? '-') }}
                        </td>
                        <td>
                            <div class="flex gap-1.5">
                                <a href="{{ route('admin.siswa.show', $s->id) }}" class="btn btn-xs outline-gray text-gray-600 px-2 group hover:text-green-600 hover:border-green-200" title="Detail Profil">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> 
                                </a>
                                <button @click="openEdit({{ json_encode($s) }})" class="btn btn-xs btn-blue" title="Edit Data">Edit</button>
                                <button @click="openDelete({ id: {{ $s->id }}, nama: '{{ addslashes($s->nama) }}', nisn: '{{ $s->kode }}' })" class="btn btn-xs btn-gray text-red-500" title="Hapus Data">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-400">Belum ada data siswa.</td>
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

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-2xl" @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="font-black text-gray-900">Tambah Siswa Baru</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-lg hover:bg-gray-200 text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="px-6 py-5 max-h-[70vh] overflow-y-auto space-y-6">
                    <!-- Data Pokok Siswa -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 mb-3 border-b pb-1">1. Data Pokok Siswa</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap Siswa <span class="text-red-500">*</span></label>
                                <input type="text" name="nama" value="{{ old('nama') }}" class="form-input @error('nama') border-red-500 @enderror" placeholder="Cth: Aditya Pratama">
                                @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">NISN / Kode <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                <input type="text" name="kode" value="{{ old('kode') }}" class="form-input @error('kode') border-red-500 @enderror" placeholder="Cth: 0012345678">
                                @error('kode') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') border-red-500 @enderror">
                                    <option value="">Pilih</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-input @error('tanggal_lahir') border-red-500 @enderror">
                                @error('tanggal_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group md:col-span-2">
                                <label class="form-label">Alamat / Domisili</label>
                                <textarea name="alamat" class="form-input h-20 @error('alamat') border-red-500 @enderror" placeholder="Alamat lengkap...">{{ old('alamat') }}</textarea>
                            </div>
                            <div class="form-group md:col-span-2">
                                <label class="form-label">Foto / Pasfoto (Opsional)</label>
                                <input type="file" name="foto" class="form-input" accept="image/*">
                                @error('foto') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Akademik -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 mb-3 border-b pb-1">2. Akademik &amp; Administrasi</h4>
                        <div class="form-group">
                            <label class="form-label">Penempatan Kelas <span class="text-red-500">*</span></label>
                            <select name="kelas_id" class="form-select @error('kelas_id') border-red-500 @enderror">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                            @error('kelas_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Data Orang Tua/Wali -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 mb-3 border-b pb-1">3. Data Wali Murid / Orang Tua</h4>
                        <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 mb-4">
                            <div class="form-group !mb-0">
                                <label class="form-label">Tautkan Akun Wali Murid Tertentu <span class="text-gray-400 font-normal">(Prioritas)</span></label>
                                <select name="wali_murid_id" class="form-select @error('wali_murid_id') border-red-500 @enderror">
                                    <option value="">-- Tidak ditautkan ke profil User --</option>
                                    @foreach($waliMurid as $w)
                                        <option value="{{ $w->id }}" {{ old('wali_murid_id') == $w->id ? 'selected' : '' }}>{{ $w->nama_lengkap }} ({{ $w->email }})</option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-gray-500 mt-1">Jika ditautkan, Nama dan No HP otomatis mengikuti profil akun ini.</p>
                                @error('wali_murid_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Nama Orang Tua/Wali Manual</label>
                                <input type="text" name="nama_orang_tua" value="{{ old('nama_orang_tua') }}" class="form-input @error('nama_orang_tua') border-red-500 @enderror" placeholder="Alternatif jika tanpa akun">
                                @error('nama_orang_tua') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">No. HP Orang Tua Manual</label>
                                <input type="text" name="no_hp_orang_tua" value="{{ old('no_hp_orang_tua') }}" class="form-input @error('no_hp_orang_tua') border-red-500 @enderror" placeholder="08xxxxxxxxxx">
                                @error('no_hp_orang_tua') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end bg-gray-50">
                    <button type="button" @click="showAdd = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-green">Simpan Siswa</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-2xl" @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <form :action="'{{ route('admin.siswa.index') }}/' + editData.id" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="font-black text-gray-900">Edit Data Siswa</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-lg hover:bg-gray-200 text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="px-6 py-5 max-h-[70vh] overflow-y-auto space-y-6">
                    <!-- Data Pokok Siswa -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 mb-3 border-b pb-1">1. Data Pokok Siswa</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap Siswa <span class="text-red-500">*</span></label>
                                <input type="text" name="nama" x-model="editData.nama" class="form-input @error('nama') border-red-500 @enderror">
                                @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">NISN / Kode</label>
                                <input type="text" name="kode" x-model="editData.kode" class="form-input @error('kode') border-red-500 @enderror">
                                @error('kode') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="jenis_kelamin" x-model="editData.jenis_kelamin" class="form-select @error('jenis_kelamin') border-red-500 @enderror">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                @error('jenis_kelamin') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_lahir" x-model="editData.tanggal_lahir" class="form-input @error('tanggal_lahir') border-red-500 @enderror">
                                @error('tanggal_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group md:col-span-2">
                                <label class="form-label">Alamat / Domisili</label>
                                <textarea name="alamat" x-model="editData.alamat" class="form-input h-20 @error('alamat') border-red-500 @enderror"></textarea>
                            </div>
                            <div class="form-group md:col-span-2">
                                <label class="form-label">Update Foto (Biarkan kosong jika tidak ingin mengubah)</label>
                                <input type="file" name="foto" class="form-input" accept="image/*">
                                @error('foto') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Akademik -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 mb-3 border-b pb-1">2. Akademik &amp; Administrasi</h4>
                        <div class="form-group">
                            <label class="form-label">Penempatan Kelas <span class="text-red-500">*</span></label>
                            <select name="kelas_id" x-model="editData.kelas_id" class="form-select @error('kelas_id') border-red-500 @enderror">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                            @error('kelas_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Data Orang Tua/Wali -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 mb-3 border-b pb-1">3. Data Wali Murid / Orang Tua</h4>
                        <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 mb-4">
                            <div class="form-group !mb-0">
                                <label class="form-label">Tautkan Akun Wali Murid Tertentu</label>
                                <select name="wali_murid_id" x-model="editData.wali_murid_id" class="form-select @error('wali_murid_id') border-red-500 @enderror">
                                    <option value="">-- Tidak ditautkan ke profil User --</option>
                                    @foreach($waliMurid as $w)
                                        <option value="{{ $w->id }}">{{ $w->nama_lengkap }} ({{ $w->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Nama Orang Tua/Wali Manual</label>
                                <input type="text" name="nama_orang_tua" x-model="editData.nama_orang_tua" class="form-input @error('nama_orang_tua') border-red-500 @enderror">
                            </div>
                            <div class="form-group">
                                <label class="form-label">No. HP Orang Tua Manual</label>
                                <input type="text" name="no_hp_orang_tua" x-model="editData.no_hp_orang_tua" class="form-input @error('no_hp_orang_tua') border-red-500 @enderror">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end bg-gray-50">
                    <button type="button" @click="showEdit = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-blue">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL HAPUS --}}
    <template x-teleport="body">
    <div x-show="showDelete" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="showDelete = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <form :action="'{{ route('admin.siswa.index') }}/' + deleteData.id" method="POST">
                @csrf
                @method('DELETE')
                <div class="px-6 py-5 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
                    <h3 class="font-black text-gray-900 text-base mb-2">Hapus Data Siswa?</h3>
                    <p class="text-sm font-bold text-gray-800 mb-1" x-text="deleteData.nama"></p>
                    <p class="text-xs text-gray-500 mb-4" x-text="'NISN: ' + (deleteData.nisn || '-')"></p>
                    <p class="text-xs text-red-600 bg-red-50 border border-red-100 rounded-lg p-2">⚠️ Semua data penilaian & rekam jejak akademik siswa ini mungkin juga ikut terhapus atau tak tergabung sempurna.</p>
                </div>
                <div class="px-6 pb-5 flex gap-3">
                    <button type="button" @click="showDelete = false" class="flex-1 btn btn-gray justify-center">Batal</button>
                    <button type="submit" class="flex-1 btn btn-red justify-center">Hapus Siswa</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
