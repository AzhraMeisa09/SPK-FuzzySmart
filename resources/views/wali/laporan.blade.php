@extends('layouts.app')

@section('title', 'Laporan Semester')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight">Rapor Digital</h1>
            <p class="text-xs text-slate-500 font-medium">Unduh laporan hasil belajar siswa</p>
        </div>
    </div>

    <div class="card overflow-hidden grid grid-cols-1 lg:grid-cols-2">
        <!-- Preview Section (Static Image/Placeholder) -->
        <div class="bg-slate-100 p-8 flex items-center justify-center relative group">
             <div class="w-full max-w-sm aspect-[1/1.414] bg-white shadow-2xl rounded border border-slate-200 p-8 flex flex-col space-y-4 relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div class="w-12 h-1 bg-slate-200 mt-2"></div>
                    <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        @include('components.icons.document-text')
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="h-4 w-3/4 bg-slate-50 rounded"></div>
                    <div class="h-3 w-1/2 bg-slate-50 rounded opacity-60"></div>
                </div>
                <div class="flex-1 border-y border-slate-50 py-4 flex flex-col gap-2">
                    @for($i=0;$i<6;$i++)
                        <div class="flex justify-between">
                            <div class="h-2 w-1/2 bg-slate-50 rounded opacity-40"></div>
                            <div class="h-2 w-8 bg-slate-50 rounded opacity-40"></div>
                        </div>
                    @endfor
                </div>
                <div class="flex justify-end pt-4">
                     <div class="w-16 h-8 bg-slate-50 border border-slate-100 rounded opacity-30"></div>
                </div>
                
                <!-- Ribbon Overlay -->
                <div class="absolute top-4 right-[-35px] bg-emerald-600 text-white text-[8px] font-black py-1 px-10 transform rotate-45 shadow-sm uppercase">Original</div>
             </div>
             
             <!-- Hover Overlay -->
             <div class="absolute inset-0 bg-emerald-950/20 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
                 <button class="bg-white text-emerald-900 px-6 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest shadow-2xl">Pratinjau</button>
             </div>
        </div>

        <!-- Download Info -->
        <div class="p-8 flex flex-col justify-center space-y-6">
            <div>
                 <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Terbit: 20 Juni 2024</p>
                 <h2 class="text-2xl font-black text-slate-800 tracking-tight leading-tight">Laporan Hasil Belajar Semester Ganjil</h2>
                 <p class="text-sm text-slate-500 font-medium leading-relaxed mt-4">Dokumen ini merupakan laporan resmi perkembangan siswa yang telah diverifikasi oleh Wali Kelas dan Kepala Sekolah.</p>
            </div>

            <div class="space-y-3">
                 <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                      <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded flex items-center justify-center">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                      </div>
                      <div class="flex-1">
                          <p class="text-[10px] font-black text-slate-700 uppercase leading-none">Format File</p>
                          <p class="text-[10px] text-slate-400 font-medium mt-1">Adobe PDF (2.4 MB)</p>
                      </div>
                 </div>
                 <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                      <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded flex items-center justify-center">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                      </div>
                      <div class="flex-1">
                          <p class="text-[10px] font-black text-slate-700 uppercase leading-none">Keamanan</p>
                          <p class="text-[10px] text-slate-400 font-medium mt-1">Digital Signature Verified</p>
                      </div>
                 </div>
            </div>

            <div class="pt-4">
                <button class="w-full btn btn-success py-4 shadow-xl shadow-emerald-500/20 text-[11px] font-black uppercase tracking-[0.2em]">
                    @include('components.icons.arrow-down-tray')
                    Unduh Dokumen PDF
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
