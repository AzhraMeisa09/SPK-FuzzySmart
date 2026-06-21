@extends('layouts.app')

@section('title', 'Cetak Kode Registrasi – Kelas ' . $kelas->nama_kelas)
@section('page-title', 'Cetak Kode Registrasi')

@push('styles')
    <style>
        /* ── Action Bar (screen only) ── */
        .action-bar {
            display: flex;
            gap: 10px;
            margin: 12px 0 20px;
            padding: 0 4px;
        }
        .btn-print {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 8px 20px;
            background: #84934A; color: #fff;
            border: none; border-radius: 7px;
            font-size: 12px; font-weight: 700;
            cursor: pointer; font-family: inherit;
            box-shadow: 0 3px 10px rgba(132,147,74,0.35);
        }
        .btn-print:hover { background: #6d7c3c; }
        .btn-back {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 8px 16px;
            background: #fff; color: #555;
            border: 1.5px solid #d0d0d0; border-radius: 7px;
            font-size: 12px; font-weight: 600;
            text-decoration: none; font-family: inherit;
        }
        .btn-back:hover { background: #f5f5f5; }

        /* ── Kertas ── */
        .page {
            width: 800px;
            max-width: 100%;
            margin: 0 auto 40px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.13);
            overflow: hidden;
        }

        /* ── Kop Surat Formal ── */
        .page-header {
            padding: 24px 36px 16px;
        }
        .kop-top {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 10px;
        }
        .kop-logo {
            width: 80px; height: 80px;
            background: #fff;
            border: 2px solid #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }
        .kop-logo img { width: 76px; height: 76px; object-fit: contain; }
        .kop-center { flex: 1; text-align: center; }
        .kop-center .nama-sekolah {
            font-size: 17px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #2d3a10;
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.3;
        }
        .kop-center .tp {
            font-size: 12px;
            font-weight: 600;
            color: #444;
            margin-top: 2px;
        }
        .kop-center .alamat {
            font-size: 9.5px;
            color: #555;
            margin-top: 3px;
            line-height: 1.5;
        }
        .kop-judul {
            text-align: center;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid #ccd9a0;
        }
        .kop-judul h2 {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1a2a08;
            font-family: 'Times New Roman', Times, serif;
        }
        .kop-judul .kelas-info {
            font-size: 11px;
            color: #555;
            margin-top: 3px;
        }

        /* ── Body ── */
        .page-body { padding: 28px 36px 32px; }

        /* ── Meta info ── */
        .meta-row {
            display: grid;
            grid-template-columns: 130px 8px 1fr;
            gap: 3px 0;
            font-size: 11px;
            margin-bottom: 20px;
        }
        .meta-row .ml { color: #333; }
        .meta-row .mc { text-align: center; color: #333; }
        .meta-row .mv { font-weight: 600; color: #111; }

        /* ── Section title ── */
        .section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #84934A;
            margin-bottom: 14px;
        }

        /* ── Tabel ── */
        .page table { width: 100%; border-collapse: collapse; }

        .page thead th {
            background: #84934A;
            color: #fff;
            padding: 10px 14px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            text-align: left;
        }
        .page thead th:first-child { border-radius: 8px 0 0 0; width: 40px; text-align: center; }
        .page thead th:last-child  { border-radius: 0 8px 0 0; text-align: center; }
        .page thead th:nth-child(3) { text-align: center; width: 110px; }

        .page tbody tr { border-bottom: 1px solid #e0e0e0; }
        .page tbody tr:last-child  { border-bottom: none; }

        .page tbody td {
            padding: 9px 14px;
            font-size: 12px;
            vertical-align: middle;
            background: transparent;
        }
        .page tbody td:first-child { text-align: center; color: #aab078; font-size: 11px; font-weight: 700; }
        .page tbody td:nth-child(3) { text-align: center; }
        .page tbody td:last-child  { text-align: center; }

        .nama-text { font-weight: 700; font-size: 12.5px; color: #1e2910; }
        .nisn-text  { font-size: 9.5px; color: #94a355; margin-top: 1px; font-weight: 500; }

        .jk-chip {
            font-size: 11px;
            font-weight: 500;
            color: #333;
        }

        /* Kode — plain, mudah dibaca */
        .kode {
            font-family: 'Courier New', monospace;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 3px;
            background: #f4f4f4;
            border: 1.5px solid #ccc;
            border-radius: 5px;
            padding: 3px 10px;
            display: inline-block;
            color: #222;
        }
        .kode-empty { color: #ccc; font-style: italic; font-size: 11px; }

        /* ── Tanda tangan ── */
        .ttd-section {
            margin-top: 28px;
            display: block;
            width: 100%;
        }
        .ttd-box {
            float: right;
            text-align: center;
            font-size: 11px;
            min-width: 210px;
        }
        .ttd-box .ttd-city { margin-bottom: 4px; }
        .ttd-box .ttd-role { font-weight: 600; }
        .ttd-space { height: 58px; }
        .ttd-box .ttd-nama { font-weight: 700; padding-top: 4px; display: inline-block; min-width: 180px; }
        .ttd-box .ttd-nip  { font-size: 10px; color: #666; margin-top: 2px; }

        /* ── Footer strip ── */
        .page-footer {
            background: #f7f9f0;
            border-top: 1.5px solid #dde5c5;
            padding: 10px 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 9px;
            color: #7a8c48;
            font-weight: 500;
        }

        /* ── Print ── */
        @media print {
            .no-print, aside, nav, header, .action-bar, [x-cloak] { display: none !important; }
            
            html, body, 
            div.flex.h-screen, 
            div.flex.flex-col.flex-1,
            main, 
            .max-w-7xl, 
            .fade-in {
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow: visible !important;
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                position: static !important;
                background: white !important;
                box-shadow: none !important;
            }

            @page { size: A4; margin: 12mm 10mm; }
            .page { 
                box-shadow: none !important; 
                border: none !important;
                border-radius: 0 !important; 
                margin: 0 !important; 
                max-width: 100% !important; 
                width: 100% !important;
            }
            .kop-logo { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .meta-chip   { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            thead th     { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .jk-chip     { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tbody tr:nth-child(even) { background: #fff !important; }
            .page-footer { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
@endpush

@section('content')
    {{-- Tombol aksi --}}
    <div class="action-bar no-print">
        <a href="{{ route('admin.siswa.index') }}" class="btn-back">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <button class="btn-print" onclick="downloadPDF()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Download PDF
        </button>
    </div>

    <div class="page">

        {{-- Kop Surat Formal --}}
        <div class="page-header">
            <div class="kop-top">
                <div class="kop-logo">
                    <img src="{{ asset('images/logotutwuri.jpg') }}" alt="Logo Tutwuri">
                </div>
                <div class="kop-center">
                    <div class="nama-sekolah">Taman Kanak-Kanak Negeri Pembina</div>
                    <div class="alamat">
                        Jalan Rasuna Said No. 77, RT VIII, Kelurahan Kampung Manggis, Kecamatan Padang Panjang Barat<br>
                        Telp. (0752) 000-0000 &bull; Email: pembina@gmail.com
                    </div>
                </div>
                <div style="width: 80px; flex-shrink: 0;"></div>
            </div>
            <div class="kop-judul">
                <h2>Daftar Kode Registrasi Wali Murid</h2>
                <div class="kelas-info">Tahun Pelajaran {{ $tahunAjaran ? $tahunAjaran->nama : now()->year . '/' . (now()->year + 1) }}</div>
            </div>
        </div>

        {{-- Body --}}
        <div class="page-body">

            {{-- Meta info --}}
            <div class="meta-row">
                <span class="ml">Kelas</span>
                <span class="mc">:</span>
                <span class="mv">{{ $kelas->nama_kelas }}</span>

                <span class="ml">Jumlah Siswa</span>
                <span class="mc">:</span>
                <span class="mv">{{ $siswa->count() }} orang</span>

                <span class="ml">Tanggal Cetak</span>
                <span class="mc">:</span>
                <span class="mv">{{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</span>
            </div>

            {{-- Section title --}}
            <div class="section-title">Data Siswa &amp; Kode Registrasi</div>

            {{-- Tabel --}}
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Kelamin</th>
                        <th>Kode Registrasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswa as $i => $s)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <div class="nama-text">{{ $s->name }}</div>
                            <div class="nisn-text">NISN: {{ $s->id_siswa }}</div>
                        </td>
                        <td>
                            <span class="jk-chip {{ $s->jenis_kelamin === 'L' ? 'jk-L' : 'jk-P' }}">
                                {{ $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>
                        <td>
                            @if($s->kode_registrasi)
                                <span class="kode">{{ $s->kode_registrasi }}</span>
                            @else
                                <span class="kode-empty">— belum ada —</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:24px; color:#bbb; font-style:italic;">
                            Tidak ada siswa di kelas ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Tanda tangan --}}
            <div class="ttd-section">
                <div class="ttd-box">
                    <div class="ttd-city">……………, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                    <div class="ttd-role">Kepala Sekolah,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-nama">( _________________________ )</div>
                    <div class="ttd-nip">NIP. ……………………………………</div>
                </div>
                <div style="clear: both;"></div>
            </div>

        </div>



    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.querySelector('.page');
            
            // Simpan style asli
            const originalStyle = element.getAttribute('style') || '';
            
            // Hilangkan shadow & border-radius saja
            element.style.boxShadow = 'none';
            element.style.borderRadius = '0';
            
            const opt = {
                margin:       10,
                filename:     'Kode_Registrasi_Kelas_{{ $kelas->nama_kelas }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, logging: false },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            // Eksekusi download, lalu kembalikan style asli setelah selesai
            html2pdf().set(opt).from(element).save().then(() => {
                element.setAttribute('style', originalStyle);
            });
        }
    </script>
@endpush
