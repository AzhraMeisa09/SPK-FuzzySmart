@extends('layouts.app')
@section('title', 'Kategori Nilai Fuzzy')
@section('page-title', 'Konfigurasi Parameter Fuzzy')

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
    deleteData: { id: '', nama: '' },
    openEdit(k) {
        this.editData = {
            id: k.id_kategori,
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
        this.deleteData = { id: k.id_kategori, nama: k.nama };
        this.showDelete = true;
    }
}" class="space-y-5">

    {{-- HEADER --}}
    <div class="card p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Parameter Linguistik Fuzzy</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Konfigurasi variabel TFN (Triangular Fuzzy Number) untuk kalkulasi SPK.</p>
            </div>
            <button @click="showAdd = true" class="btn btn-green shadow-lg shadow-green-100 px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Tambah Kategori
            </button>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card overflow-hidden">
        <table class="tbl">
            <thead>
                <tr>
                    <th class="w-16">No</th>
                    <th>Nama Kategori</th>
                    <th class="text-center">TFN Parameter (L, M, U)</th>
                    <th class="text-center">Skor Crisp</th>
                    <th class="text-center">Ambang Batas (%)</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kategori as $i => $k)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="text-gray-400 text-xs">{{ $i + 1 }}</td>
                        <td>
                            @php
                                $badgeClass = match($k->nama) {
                                    'MB' => 'bg-red-50 text-red-700 border-red-100',
                                    'BSH' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'BSB' => 'bg-green-50 text-green-700 border-green-100',
                                    default => 'bg-blue-50 text-blue-700 border-blue-100'
                                };
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl border flex items-center justify-center text-xs font-bold {{ $badgeClass }} shadow-sm">
                                    {{ $k->nama }}
                                </div>
                                <span class="text-[11px] font-medium text-gray-500">{{ $k->nama == 'MB' ? 'Mulai Berkembang' : ($k->nama == 'BSH' ? 'Sesuai Harapan' : 'Sangat Baik') }}</span>
                            </div>
                        </td>
                        <td class="py-4">
                            <div class="flex items-center justify-center gap-1.5">
                                <div class="flex flex-col items-center">
                                    <span class="text-[8px] font-bold text-gray-300 mb-1">Low</span>
                                    <span class="w-10 h-7 bg-gray-50 border border-gray-100 flex items-center justify-center text-[10px] font-mono text-gray-400 rounded-lg">{{ number_format($k->nilai_l, 1) }}</span>
                                </div>
                                <div class="h-4 w-px bg-gray-200 mt-3"></div>
                                <div class="flex flex-col items-center px-1">
                                    <span class="text-[8px] font-bold text-var(--accent) mb-1">Mid</span>
                                    <span class="w-12 h-8 bg-var(--accent-lt) border border-var(--accent)/20 flex items-center justify-center text-[10px] font-bold text-var(--accent) rounded-lg shadow-sm">{{ number_format($k->nilai_m, 1) }}</span>
                                </div>
                                <div class="h-4 w-px bg-gray-200 mt-3"></div>
                                <div class="flex flex-col items-center">
                                    <span class="text-[8px] font-bold text-gray-300 mb-1">Up</span>
                                    <span class="w-10 h-7 bg-gray-50 border border-gray-100 flex items-center justify-center text-[10px] font-mono text-gray-400 rounded-lg">{{ number_format($k->nilai_u, 1) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                {{ number_format($k->nilai_crisp, 3) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-3">
                                <span class="px-2 py-1 rounded-lg bg-gray-50 text-[11px] font-bold text-gray-600 border border-gray-100">{{ $k->rentang_min }}%</span>
                                <div class="w-8 h-[2px] bg-gray-200 rounded-full relative">
                                    <div class="absolute inset-y-0 left-0 bg-var(--accent) rounded-full" style="width: 100%"></div>
                                </div>
                                <span class="px-2 py-1 rounded-lg bg-var(--accent-lt) text-[11px] font-bold text-var(--accent) border border-var(--accent)/10">{{ $k->rentang_max }}%</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openEdit({{ Js::from($k) }})" class="p-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="openDelete({{ Js::from($k) }})" class="p-2 rounded-xl bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-20 text-gray-400 italic text-sm">Parameter fuzzy belum dikonfigurasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-5 bg-gray-50/50 border-t border-gray-100 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 border border-blue-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-800 mb-1">Metodologi Kalkulasi</p>
                <p class="text-[11px] text-gray-500 leading-relaxed">
                    Nilai Crisp dihitung otomatis dengan formula **(L + M + U) / 3**. Ambang batas (%) menentukan klasifikasi akhir siswa berdasarkan perolehan nilai utilitas global dalam rentang 0 hingga 1.
                </p>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-md" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.kategori-nilai.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold" style="color: var(--text-1);">Tambah Parameter</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label text-xs font-bold text-gray-500 mb-1 block">Label Kategori <span class="text-red-500">*</span></label>
                        <select name="nama" class="form-select rounded-xl">
                            <option value="MB">MB (Mulai Berkembang)</option>
                            <option value="BSH">BSH (Berkembang Sesuai Harapan)</option>
                            <option value="BSB">BSB (Berkembang Sangat Baik)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="form-label text-xs font-bold text-gray-500 mb-2 block">Variabel TFN (L, M, U)</label>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="form-group">
                                <label class="text-[10px] font-bold text-gray-400 mb-1 block text-center">Low</label>
                                <input type="number" step="0.01" name="nilai_l" value="{{ old('nilai_l', '0.00') }}" class="form-input text-center font-mono">
                            </div>
                            <div class="form-group">
                                <label class="text-[10px] font-bold text-var(--accent) mb-1 block text-center">Mid</label>
                                <input type="number" step="0.01" name="nilai_m" value="{{ old('nilai_m', '0.50') }}" class="form-input text-center font-mono">
                            </div>
                            <div class="form-group">
                                <label class="text-[10px] font-bold text-gray-400 mb-1 block text-center">Up</label>
                                <input type="number" step="0.01" name="nilai_u" value="{{ old('nilai_u', '1.00') }}" class="form-input text-center font-mono">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label text-xs font-bold text-gray-500 mb-1 block">Min (%)</label>
                            <input type="number" name="rentang_min" class="form-input font-bold" placeholder="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-xs font-bold text-gray-500 mb-1 block">Max (%)</label>
                            <input type="number" name="rentang_max" class="form-input font-bold" placeholder="50">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/30">
                    <button type="button" @click="showAdd = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-green">Simpan</button>
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
                <input type="hidden" name="id" :value="editData.id">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold" style="color: var(--text-1);">Edit Parameter</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label text-xs font-bold text-gray-500 mb-1 block">Label Kategori</label>
                        <select name="nama" x-model="editData.nama" class="form-select rounded-xl">
                            <option value="MB">MB (Mulai Berkembang)</option>
                            <option value="BSH">BSH (Berkembang Sesuai Harapan)</option>
                            <option value="BSB">BSB (Berkembang Sangat Baik)</option>
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3">
                        <div class="form-group">
                            <label class="text-[10px] font-bold text-gray-400 mb-1 block text-center">Low</label>
                            <input type="number" step="0.01" name="nilai_l" x-model="editData.nilai_l" class="form-input text-center font-mono">
                        </div>
                        <div class="form-group">
                            <label class="text-[10px] font-bold text-var(--accent) mb-1 block text-center">Mid</label>
                            <input type="number" step="0.01" name="nilai_m" x-model="editData.nilai_m" class="form-input text-center font-mono">
                        </div>
                        <div class="form-group">
                            <label class="text-[10px] font-bold text-gray-400 mb-1 block text-center">Up</label>
                            <input type="number" step="0.01" name="nilai_u" x-model="editData.nilai_u" class="form-input text-center font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label text-xs font-bold text-gray-500 mb-1 block">Min (%)</label>
                            <input type="number" step="0.01" name="rentang_min" x-model="editData.rentang_min" class="form-input font-bold">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-xs font-bold text-gray-500 mb-1 block">Max (%)</label>
                            <input type="number" step="0.01" name="rentang_max" x-model="editData.rentang_max" class="form-input font-bold">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/30">
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
                <div class="px-6 py-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-4 border border-red-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-1">Hapus Kategori?</h3>
                    <p class="text-sm text-gray-500 mb-6">Aksi ini akan merusak logika perhitungan SPK jika dihapus sembarangan.</p>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" @click="showDelete = false" class="flex-1 btn btn-gray">Batal</button>
                    <button type="submit" class="flex-1 btn btn-red">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
