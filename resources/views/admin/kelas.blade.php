@extends('layouts.app')
@section('title', 'Manajemen Kelas')
@section('page-title', 'Manajemen Kelas')

@section('content')

<div x-data="{
    showAdd: {{ $errors->any() && !session('edit_id') ? 'true' : 'false' }},
    showEdit: {{ session('edit_id') ? 'true' : 'false' }},
    showDelete: false,
    editData: {
        id: '{{ old('id', session('edit_data.id')) }}',
        nama: '{{ old('nama_kelas', session('edit_data.nama_kelas')) }}',
        tahun_ajaran_id: '{{ old('tahun_ajaran_id', session('edit_data.tahun_ajaran_id')) }}',
        guru_ids: {{ json_encode(old('guru_ids', session('edit_data.guru_ids', []))) }}
    },
    deleteData: {},
    openEdit(k) { 
        this.editData = {
            id: k.id,
            nama: k.nama_kelas,
            tahun_ajaran_id: k.tahun_ajaran_id,
            guru_ids: k.guru.map(g => g.id.toString())
        }; 
        this.showEdit = true; 
    },
    openDelete(k) { this.deleteData = k; this.showDelete = true; }
}" class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-base font-black text-gray-900">Manajemen Kelas</h1>
            <p class="text-xs text-gray-500">{{ $kelas->total() }} kelas aktif</p>
        </div>
        <button @click="showAdd = true" class="btn btn-green">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Kelas
        </button>
    </div>

    <form action="{{ route('admin.kelas.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm mt-5 mb-4">
        <div class="relative flex-1 w-full">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full py-2.5" style="padding-left: 42px;" placeholder="Cari nama kelas...">
        </div>
        <div class="flex gap-2 w-full md:w-auto shrink-0 md:pl-2">
            <button type="submit" class="btn btn-blue py-2.5 px-6 shadow-sm">Cari</button>
        </div>
    </form>

    <div class="card overflow-hidden">
        <table class="tbl">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Tahun Ajaran</th>
                    <th>Guru Pengampu</th>
                    <th>Jml Siswa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelas as $i => $k)
                    <tr>
                        <td class="text-gray-400 text-xs">{{ $kelas->firstItem() + $i }}</td>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-green-100 text-green-700 font-black text-xs flex items-center justify-center">{{ substr($k->nama_kelas, 0, 1) }}</div>
                                <span class="font-semibold text-gray-800">{{ $k->nama_kelas }}</span>
                            </div>
                        </td>
                        <td><span class="text-gray-600">{{ $k->tahunAjaran->nama ?? '-' }}</span></td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @forelse($k->guru as $g)
                                    <span class="badge badge-blue">{{ $g->nama_lengkap }}</span>
                                @empty
                                    <span class="text-xs text-gray-400 italic">Belum ada guru</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-700">{{ $k->siswa->count() }}</span>
                                <span class="text-xs text-gray-400 font-medium">siswa</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex gap-1.5">
                                <button @click="openEdit({{ json_encode($k) }})" class="btn btn-xs btn-blue">Edit</button>
                                <button @click="openDelete({ id: {{ $k->id }}, nama: '{{ $k->nama_kelas }}' })" class="btn btn-xs btn-gray text-red-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-400">Belum ada data kelas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($kelas->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $kelas->links() }}
        </div>
        @endif
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-900">Tambah Kelas</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label">Nama Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kelas" value="{{ old('nama_kelas') }}" class="form-input @error('nama_kelas') border-red-500 @enderror" placeholder="Anggrek A">
                        @error('nama_kelas') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <select name="tahun_ajaran_id" class="form-select @error('tahun_ajaran_id') border-red-500 @enderror">
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($tahunAjaran as $ta)
                                <option value="{{ $ta->id }}" {{ old('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama }} {{ $ta->is_aktif ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Guru Pengampu</label>
                        <div x-data="{
                            open: false,
                            selected: {{ json_encode(old('guru_ids', [])) }},
                            options: [
                                @foreach($guru as $g)
                                    { id: '{{ $g->id }}', name: '{{ addslashes($g->nama_lengkap) }}' },
                                @endforeach
                            ],
                            get selectedNames() {
                                if (this.selected.length === 0) return 'Pilih Guru...';
                                return this.options.filter(o => this.selected.includes(o.id.toString())).map(o => o.name).join(', ');
                            }
                        }" class="relative">
                            <div @click="open = !open" class="form-input flex justify-between items-center cursor-pointer min-h-[38px] bg-white @error('guru_ids.*') border-red-500 @enderror">
                                <span x-text="selectedNames" class="truncate max-w-[90%] text-sm text-gray-700"></span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            
                            <div x-show="open" @click.away="open = false" class="absolute z-[60] w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto" x-transition x-cloak>
                                <template x-for="option in options" :key="option.id">
                                    <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-50 last:border-0 transform transition duration-150 ease-in-out">
                                        <input type="checkbox" :value="option.id" x-model="selected" name="guru_ids[]" class="text-green-600 rounded focus:ring-green-500 border-gray-300 w-4 h-4 cursor-pointer">
                                        <span x-text="option.name" class="text-sm font-medium text-gray-700 select-none"></span>
                                    </label>
                                </template>
                                <div x-show="options.length === 0" class="px-3 py-2 text-sm text-gray-400 italic">Belum ada data guru.</div>
                            </div>
                        </div>
                        @error('guru_ids.*') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
                    <button type="button" @click="showAdd = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-green">Tambah Kelas</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <form :action="'{{ route('admin.kelas.index') }}/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-900">Edit Kelas</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label">Nama Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kelas" x-model="editData.nama" class="form-input @error('nama_kelas') border-red-500 @enderror">
                        @error('nama_kelas') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <select name="tahun_ajaran_id" x-model="editData.tahun_ajaran_id" class="form-select @error('tahun_ajaran_id') border-red-500 @enderror">
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($tahunAjaran as $ta)
                                <option value="{{ $ta->id }}">
                                    {{ $ta->nama }} {{ $ta->is_aktif ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Guru Pengampu</label>
                        <div x-data="{
                            open: false,
                            options: [
                                @foreach($guru as $g)
                                    { id: '{{ $g->id }}', name: '{{ addslashes($g->nama_lengkap) }}' },
                                @endforeach
                            ],
                            get selectedNames() {
                                if (!editData.guru_ids || editData.guru_ids.length === 0) return 'Pilih Guru...';
                                return this.options.filter(o => editData.guru_ids.includes(o.id.toString())).map(o => o.name).join(', ');
                            }
                        }" class="relative">
                            <div @click="open = !open" class="form-input flex justify-between items-center cursor-pointer min-h-[38px] bg-white @error('guru_ids.*') border-red-500 @enderror">
                                <span x-text="selectedNames" class="truncate max-w-[90%] text-sm text-gray-700"></span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            
                            <div x-show="open" @click.away="open = false" class="absolute z-[60] w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto" x-transition x-cloak>
                                <template x-for="option in options" :key="option.id">
                                    <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-50 last:border-0 transform transition duration-150 ease-in-out">
                                        <input type="checkbox" :value="option.id" x-model="editData.guru_ids" name="guru_ids[]" class="text-blue-600 rounded focus:ring-blue-500 border-gray-300 w-4 h-4 cursor-pointer">
                                        <span x-text="option.name" class="text-sm font-medium text-gray-700 select-none"></span>
                                    </label>
                                </template>
                                <div x-show="options.length === 0" class="px-3 py-2 text-sm text-gray-400 italic">Belum ada data guru.</div>
                            </div>
                        </div>
                        @error('guru_ids.*') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
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
            <form :action="'{{ route('admin.kelas.index') }}/' + deleteData.id" method="POST">
                @csrf
                @method('DELETE')
                <div class="px-6 py-5 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
                    <h3 class="font-black text-gray-900 text-base mb-2">Hapus Kelas?</h3>
                    <p class="text-sm font-bold text-gray-800 mb-3" x-text="deleteData.nama"></p>
                    <p class="text-xs text-red-600 bg-red-50 border border-red-100 rounded-lg p-2">⚠️ Data siswa dalam kelas ini tidak dapat dihapus dan harus dipindahkan secara manual.</p>
                </div>
                <div class="px-6 pb-5 flex gap-3">
                    <button type="button" @click="showDelete = false" class="flex-1 btn btn-gray justify-center">Batal</button>
                    <button type="submit" class="flex-1 btn btn-red justify-center">Hapus Kelas</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
