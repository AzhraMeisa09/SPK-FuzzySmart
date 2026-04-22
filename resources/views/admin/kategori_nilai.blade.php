@extends('layouts.app')

@section('title', 'Kategori Nilai Fuzzy')
@section('page-title', 'Kategori Nilai Fuzzy')

@section('content')
<div x-data="{
    showAdd: {{ $errors->any() && !session('edit_id') ? 'true' : 'false' }},
    showEdit: {{ session('edit_id') ? 'true' : 'false' }},
    showDelete: false,
    editData: {
        id: '{{ old('id') }}',
        nama: '{{ old('nama') }}',
        nilai_l: '{{ old('nilai_l') }}',
        nilai_m: '{{ old('nilai_m') }}',
        nilai_u: '{{ old('nilai_u') }}',
        rentang_min: '{{ old('rentang_min') }}',
        rentang_max: '{{ old('rentang_max') }}'
    },
    deleteData: {},
    openEdit(k) {
        this.editData = {
            id: k.id,
            nama: k.nama,
            nilai_l: parseFloat(k.nilai_l).toFixed(2),
            nilai_m: parseFloat(k.nilai_m).toFixed(2),
            nilai_u: parseFloat(k.nilai_u).toFixed(2),
            rentang_min: parseFloat(k.rentang_min).toFixed(2),
            rentang_max: parseFloat(k.rentang_max).toFixed(2)
        };
        this.showEdit = true;
    },
    openDelete(k) {
        this.deleteData = k;
        this.showDelete = true;
    }
}" class="space-y-5">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-base font-black text-gray-900">Kategori Nilai Fuzzy</h1>
            <p class="text-xs text-gray-500 mt-0.5">Parameter linguistik untuk perhitungan Fuzzy SMART</p>
        </div>
        <button @click="showAdd = true" class="btn btn-green">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Kategori
        </button>
    </div>

    {{-- TABLE --}}
    <div class="card overflow-hidden">
        <table class="tbl">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Nilai L</th>
                    <th>Nilai M</th>
                    <th>Nilai U</th>
                    <th>Nilai Crisp</th>
                    <th>Rentang Nilai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategori as $i => $k)
                    <tr>
                        <td class="text-gray-400 text-xs">{{ $i + 1 }}</td>
                        <td>
                            @php
                                $badgeClass = 'badge-blue';
                                if($k->nama == 'MB') $badgeClass = 'bg-red-100 text-red-700';
                                elseif($k->nama == 'BSH') $badgeClass = 'bg-amber-100 text-amber-700';
                                elseif($k->nama == 'BSB') $badgeClass = 'bg-green-100 text-green-700';
                            @endphp
                            <span class="badge {{ $badgeClass }} font-bold">{{ $k->nama }}</span>
                        </td>
                        <td><span class="font-mono text-xs text-gray-600">{{ number_format($k->nilai_l, 2, ',', '.') }}</span></td>
                        <td><span class="font-mono text-xs text-gray-600">{{ number_format($k->nilai_m, 2, ',', '.') }}</span></td>
                        <td><span class="font-mono text-xs text-gray-600">{{ number_format($k->nilai_u, 2, ',', '.') }}</span></td>
                        <td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-black bg-blue-50 text-blue-700 border border-blue-100">
                                {{ number_format($k->nilai_crisp, 2, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            <span class="text-xs font-bold text-gray-700">{{ $k->rentang_min }}% – {{ $k->rentang_max }}%</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-1.5">
                                <button @click="openEdit({{ Js::from($k) }})" class="btn btn-xs btn-blue">Edit</button>
                                <button @click="openDelete({{ Js::from($k) }})" class="btn btn-xs btn-gray text-red-500">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">Belum ada data kategori nilai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-start gap-3">
            <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-[11px] text-gray-500 leading-relaxed font-medium">
                <span class="font-bold text-gray-700">Info:</span> Nilai Crisp dihitung otomatis dengan rumus (L+M+U)/3. Rentang nilai (%) digunakan untuk mengklasifikasikan hasil akhir evaluasi siswa ke dalam kategori linguistik (MB, BSH, atau BSB).
            </p>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-md" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.kategori-nilai.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-900">Tambah Kategori Nilai</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
                        <select name="nama" class="form-select @error('nama') border-red-500 @enderror">
                            <option value="MB" {{ old('nama') == 'MB' ? 'selected' : '' }}>MB (Mulai Berkembang)</option>
                            <option value="BSH" {{ old('nama') == 'BSH' ? 'selected' : '' }}>BSH (Sesuai Harapan)</option>
                            <option value="BSB" {{ old('nama') == 'BSB' ? 'selected' : '' }}>BSB (Sangat Baik)</option>
                        </select>
                        @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3">
                        <div class="form-group text-center">
                            <label class="form-label">Nilai L</label>
                            <input type="number" step="0.01" min="0" max="100" name="nilai_l" value="{{ old('nilai_l', '0.00') }}" class="form-input text-center font-mono">
                        </div>
                        <div class="form-group text-center">
                            <label class="form-label">Nilai M</label>
                            <input type="number" step="0.01" min="0" max="100" name="nilai_m" value="{{ old('nilai_m', '50.00') }}" class="form-input text-center font-mono">
                        </div>
                        <div class="form-group text-center">
                            <label class="form-label">Nilai U</label>
                            <input type="number" step="0.01" min="0" max="100" name="nilai_u" value="{{ old('nilai_u', '100.00') }}" class="form-input text-center font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div class="form-group">
                            <label class="form-label">Rentang Min (%) <span class="text-red-500">*</span></label>
                            <input type="number" name="rentang_min" value="{{ old('rentang_min') }}" class="form-input" placeholder="Contoh: 0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Rentang Max (%) <span class="text-red-500">*</span></label>
                            <input type="number" name="rentang_max" value="{{ old('rentang_max') }}" class="form-input" placeholder="Contoh: 50">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
                    <button type="button" @click="showAdd = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-green">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-md" @click.stop x-transition.scale.95>
            <form :action="'{{ url('admin/kategori-nilai') }}/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-900">Edit Kategori Nilai</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
                        <select name="nama" x-model="editData.nama" class="form-select @error('nama') border-red-500 @enderror">
                            <option value="MB">MB (Mulai Berkembang)</option>
                            <option value="BSH">BSH (Sesuai Harapan)</option>
                            <option value="BSB">BSB (Sangat Baik)</option>
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3">
                        <div class="form-group text-center">
                            <label class="form-label">Nilai L</label>
                            <input type="number" step="0.01" min="0" max="100" name="nilai_l" x-model="editData.nilai_l" class="form-input text-center font-mono">
                        </div>
                        <div class="form-group text-center">
                            <label class="form-label">Nilai M</label>
                            <input type="number" step="0.01" min="0" max="100" name="nilai_m" x-model="editData.nilai_m" class="form-input text-center font-mono">
                        </div>
                        <div class="form-group text-center">
                            <label class="form-label">Nilai U</label>
                            <input type="number" step="0.01" min="0" max="100" name="nilai_u" x-model="editData.nilai_u" class="form-input text-center font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div class="form-group">
                            <label class="form-label">Rentang Min (%) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="rentang_min" x-model="editData.rentang_min" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Rentang Max (%) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="rentang_max" x-model="editData.rentang_max" class="form-input">
                        </div>
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
    <div x-show="showDelete" x-transition.opacity @keydown.escape.window="showDelete = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition.scale.95>
            <form :action="'{{ url('admin/kategori-nilai') }}/' + deleteData.id" method="POST">
                @csrf
                @method('DELETE')
                <div class="px-6 py-6 text-center">
                    <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="font-black text-gray-900 text-lg mb-2">Hapus Kategori?</h3>
                    <p class="text-xs text-gray-500 mb-4 ml-4 mr-4 leading-relaxed">Menghapus kategori nilai dapat mempengaruhi perhitungan SPK yang sedang berjalan.</p>
                    <div class="py-2 px-3 bg-gray-50 rounded-lg mt-3">
                        <p class="text-sm font-bold text-gray-800" x-text="deleteData.nama"></p>
                    </div>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" @click="showDelete = false" class="flex-1 btn btn-gray justify-center font-bold">Batal</button>
                    <button type="submit" class="flex-1 btn btn-red justify-center font-bold shadow-sm shadow-red-200">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
