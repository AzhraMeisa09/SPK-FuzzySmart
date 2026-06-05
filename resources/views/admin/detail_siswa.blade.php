@extends('layouts.app')
@section('title', 'Profil Murid: ' . $siswa->name)
@section('page-title', 'Profil Detail Murid')

@section('content')
<div class="space-y-6 pb-12">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-gray bg-white shadow-sm hover:shadow transition-all flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-var(--text-2)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Siswa
        </a>
        
        <div class="flex gap-3">
             <a href="{{ route('admin.siswa.edit', $siswa->id_siswa) }}" class="btn btn-blue shadow-md hover:shadow-lg transition-all flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Data Murid
            </a>
        </div>
    </div>

    <!-- Student Hero Section -->
    <div class="card overflow-hidden border-none shadow-xl">
        <div class="h-32 w-full" style="background: linear-gradient(135deg, #84934A 0%, #A3B18A 100%);"></div>
        <div class="px-8 pb-8">
            <div class="relative flex flex-col md:flex-row items-center md:items-end gap-6 -mt-12">
                <div class="relative">
                    @if($siswa->foto)
                        <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto Murid" class="w-32 h-32 rounded-3xl object-cover border-4 border-white shadow-2xl bg-white">
                    @else
                        <div class="w-32 h-32 rounded-3xl bg-white border-4 border-white shadow-2xl flex items-center justify-center text-4xl font-bold text-var(--accent)">
                            {{ strtoupper(substr($siswa->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="absolute -bottom-1 -right-1 w-8 h-8 rounded-2xl bg-var(--accent) border-4 border-white flex items-center justify-center shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                </div>
                
                <div class="flex-1 text-center md:text-left mb-2">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-1">
                        <h2 class="text-3xl font-bold text-var(--text-1) tracking-tight">{{ $siswa->name }}</h2>
                        <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-bold border border-blue-100">
                            {{ $siswa->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                        </span>
                    </div>
                    <p class="text-var(--text-3) font-medium text-sm flex items-center justify-center md:justify-start gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        NISN: {{ $siswa->kode ?? '—' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- INFO SIDEBAR -->
        <div class="lg:col-span-4 space-y-6">
            <!-- BIODATA SINGKAT -->
            <div class="card p-6">
                <h4 class="text-[11px] font-bold text-var(--text-3) mb-4">Informasi Kelahiran</h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-var(--text-2) font-medium">Jenis Kelamin</span>
                        <span class="text-xs font-bold text-var(--text-1)">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-var(--text-2) font-medium">Tanggal Lahir</span>
                        <span class="text-xs font-bold text-var(--text-1)">{{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-var(--text-2) font-medium">Usia Saat Ini</span>
                        <span class="text-xs font-bold text-var(--accent)">{{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->age }} Tahun</span>
                    </div>
                </div>
            </div>

            <!-- KODE REGISTRASI -->
            <div class="card p-6 border-none shadow-lg" style="background: linear-gradient(135deg, #78350f 0%, #b45309 100%);">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <h4 class="text-xs font-bold text-white mb-1">Kode Registrasi</h4>
                @if($siswa->kode_registrasi)
                    <div class="flex items-center gap-2 mt-2 mb-3">
                        <code class="text-lg font-black text-white tracking-[0.2em]">{{ $siswa->kode_registrasi }}</code>
                    </div>
                    <button
                        onclick="copyKodeDetail('{{ $siswa->kode_registrasi }}', this)"
                        class="mt-1 flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-[10px] font-bold transition-all border border-white/20">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Salin Kode
                    </button>
                @else
                    <p class="text-[11px] opacity-80 font-medium italic">Belum ada kode registrasi.</p>
                @endif

                <div class="mt-4 pt-4 border-t border-white/20">
                    <h4 class="text-[10px] font-bold text-white/70 mb-1.5">Status Registrasi</h4>
                    @if($siswa->wali->count() > 0 || $siswa->wali_murid_id)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-400/30 text-white border border-green-300/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-300"></span>
                            Sudah Terhubung
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/10 text-white/70 border border-white/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-white/50"></span>
                            Belum Terhubung
                        </span>
                    @endif
                </div>
            </div>

            <!-- WALi MURID INFO -->
            <div class="card p-6 border-none text-white shadow-lg" style="background: linear-gradient(135deg, #6A783D 0%, #84934A 100%);">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h4 class="text-xs font-bold mb-2">Akses Wali Murid</h4>
                @if($siswa->wali->count() > 0)
                    @foreach($siswa->wali as $w)
                        <p class="text-sm font-bold mb-1">{{ $w->nama_lengkap }}</p>
                        <p class="text-[11px] opacity-80 font-medium">{{ $w->email }}</p>
                    @endforeach
                @else
                    <p class="text-[11px] leading-relaxed opacity-80 font-medium italic">
                        Belum ada akun wali murid yang ditautkan ke profil siswa ini.
                    </p>
                @endif
            </div>
        </div>

        <!-- MAIN DATA -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- ALAMAT & ORTU -->
            <div class="card overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-var(--text-1) flex items-center gap-2">
                        <svg class="w-4 h-4 text-var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Data Tempat Tinggal & Orang Tua
                    </h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-bold text-var(--text-3) mb-1.5 block">Alamat Lengkap Siswa</label>
                            <div class="px-5 py-4 bg-var(--bg) rounded-2xl border border-var(--border) text-sm font-medium text-var(--text-1) leading-relaxed">
                                {{ $siswa->wali->count() > 0 ? $siswa->wali->first()->alamat : ($siswa->alamat ?? 'Data alamat belum dilengkapi.') }}
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-var(--text-3) mb-1.5 block">Nama Orang Tua</label>
                            <div class="px-4 py-3 bg-white rounded-xl border border-var(--border) text-sm font-bold text-var(--text-1)">
                                {{ $siswa->wali->count() > 0 ? $siswa->wali->first()->nama_lengkap : ($siswa->nama_orang_tua ?? '—') }}
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-var(--text-3) mb-1.5 block">Kontak Orang Tua</label>
                            <div class="px-4 py-3 bg-white rounded-xl border border-var(--border) text-sm font-bold text-var(--text-1)">
                                {{ $siswa->wali->count() > 0 ? $siswa->wali->first()->no_hp : ($siswa->no_hp_orang_tua ?? '—') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PENILAIAN SUMMARY (Optional but nice for consistency) -->
            <div class="card p-8 bg-var(--accent-lt) border-var(--accent)/20 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-var(--accent)/5 rounded-full"></div>
                <div class="relative">
                    <h4 class="text-sm font-bold text-var(--accent) mb-4">Status Akademik</h4>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm border border-var(--accent)/10">
                            <svg class="w-6 h-6 text-var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-var(--text-1) mb-1 tracking-tight">Terdaftar Aktif</p>
                            <p class="text-xs text-var(--text-2) leading-relaxed font-medium">Siswa ini terdaftar secara resmi di kelas <strong>{{ $siswa->kelas->nama_kelas ?? '—' }}</strong> untuk tahun ajaran aktif. Seluruh data penilaian mingguan dan bulanan dapat diakses melalui modul Guru.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function copyKodeDetail(kode, btn) {
    navigator.clipboard.writeText(kode).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Tersalin!';
        btn.classList.add('bg-white/40');
        setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('bg-white/40'); }, 1500);
    });
}
</script>
@endpush
@endsection
