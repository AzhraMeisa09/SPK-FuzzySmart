@extends('layouts.app')

@section('title', 'Jadwal Subkriteria')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/admin/minggu?role=admin" class="p-2 bg-white rounded-lg border border-slate-200 text-slate-400 hover:text-emerald-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-800 tracking-tight">M3: Tanaman Hias & Sayur</h1>
                <p class="text-xs text-slate-500 font-medium">Pengaturan subkriteria yang dinilai pada minggu ini</p>
            </div>
        </div>
        <button class="btn btn-success">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Subkriteria
        </button>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['Subkriteria', 'Kriteria Induk', 'Urutan', 'Wajib', 'Aksi']">
            @php
                $jadwals = [
                    ['S1.1: Mengenal Tuhan...', 'K1: Agama & Moral', '1', 'Ya'],
                    ['S2.1: Keseimbangan Tubuh', 'K2: Fisik Motorik', '2', 'Ya'],
                    ['S3.2: Mengenal Bentuk', 'K3: Kognitif', '3', 'Tidak'],
                    ['S5.1: Kerjasama Tim', 'K5: Sosoal Emosional', '4', 'Ya'],
                ];
            @endphp
            @foreach($jadwals as $j)
                <tr>
                    <td><span class="text-xs font-bold text-slate-700">{{ $j[0] }}</span></td>
                    <td><span class="text-[10px] font-black uppercase text-slate-400">{{ $j[1] }}</span></td>
                    <td><span class="text-xs font-bold text-slate-600">{{ $j[2] }}</span></td>
                    <td>
                        <x-badge :type="$j[3] == 'Ya' ? 'success' : 'default'">
                            {{ $j[3] }}
                        </x-badge>
                    </td>
                    <td>
                        <button class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Hapus dari Jadwal">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </td>
                </tr>
            @endforeach
        </x-table>
        <div class="p-5 bg-blue-50/50 flex items-center gap-3 border-t border-slate-100">
            <div class="text-blue-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-[10px] font-bold text-blue-700 uppercase tracking-tight">Subkriteria di atas akan muncul secara otomatis di form penilaian Guru pada minggu ke-3.</p>
        </div>
    </div>
</div>
@endsection
