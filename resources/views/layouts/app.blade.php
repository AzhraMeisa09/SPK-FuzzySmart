<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SPK Fuzzy SMART — Sistem Pendukung Keputusan Penilaian Perkembangan Siswa">
    <title>@yield('title', 'Dashboard') — SPK TK Pembina</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logotutwuri.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <script>
        // Client-side protection: Redirect to login if no token found
        if (!localStorage.getItem('auth_token') && !['/', '/login'].includes(window.location.pathname)) {
            window.location.href = '/login';
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup { border-radius: 20px !important; font-family: 'Inter', sans-serif !important; }
        .swal2-styled.swal2-confirm { background-color: var(--accent) !important; border-radius: 12px !important; padding: 10px 24px !important; font-weight: 600 !important; }
        .swal2-styled.swal2-cancel { border-radius: 12px !important; padding: 10px 24px !important; font-weight: 600 !important; }
        .swal2-container { z-index: 20000 !important; }
    </style>
    
    <style>
        [x-cloak] { display: none !important; }

        /* ── DESIGN TOKENS ── */
        :root {
            --bg:           #F5F5F0;
            --surface:      #FFFFFF;
            --border:       #E2E8E0;
            --border-focus: #84934A;
            --text-1:       #1B211A;
            --text-2:       #5C6B58;
            --text-3:       #9BA89A;
            --accent:       #84934A;
            --accent-lt:    #F0F3E8;
            --danger:       #C0392B;
            --danger-lt:    #FEF0EE;
            font-size: 14px;
        }

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-1); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-3); }

        /* ── SIDEBAR ── */
        .sidebar-bg { background: #F8F9F5; border-right: 1px solid var(--border); }
        .nav-link, .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 10px;
            font-size: 13px; font-weight: 600; color: var(--text-2);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); text-decoration: none;
        }
        .nav-link:hover, .nav-item:hover { background: var(--bg); color: var(--text-1); }
        .nav-link.active, .nav-item.active { background: var(--accent); color: #FFFFFF; box-shadow: 0 8px 16px -4px rgba(132, 147, 74, 0.25); }
        .nav-section, .nav-section-label {
            font-size: 11px; font-weight: 700; color: var(--text-3);
            text-transform: uppercase; letter-spacing: 0.05em;
            padding: 20px 12px 6px;
        }

        /* ── CARDS ── */
        .card {
            background: var(--surface);
            border-radius: 10px;
            border: 1px solid var(--border);
        }
        .card-hover { transition: border-color 0.15s; }
        .card-hover:hover { border-color: #C8D0B8; }

        /* ── BADGES ── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 9px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }
        .badge-mb    { background: var(--danger-lt); color: var(--danger); }
        .badge-bsh   { background: #FDF8ED; color: #92700A; }
        .badge-bsb   { background: var(--accent-lt); color: var(--accent); }
        .badge-draft { background: #FDF8ED; color: #92700A; }
        .badge-final { background: var(--accent-lt); color: var(--accent); }
        .badge-aktif { background: var(--accent-lt); color: var(--accent); }
        .badge-nonaktif { background: #F5F5F0; color: var(--text-3); }
        .badge-blue  { background: #EEF4FF; color: #3B6FE0; }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 7px; font-size: 13px;
            font-weight: 600; cursor: pointer; border: none;
            line-height: 1.4; transition: background 0.12s, opacity 0.12s;
        }
        .btn:active { opacity: .85; }
        .btn-green  { background: var(--accent); color: #fff; }
        .btn-green:hover { background: #718040; }
        .btn-blue   { background: #3B6FE0; color: #fff; }
        .btn-blue:hover { background: #2E5AC4; }
        .btn-outline-blue { background: transparent; color: #3B6FE0; border: 1px solid #3B6FE0; }
        .btn-outline-blue:hover { background: #EEF4FF; }
        .btn-red    { background: var(--danger); color: #fff; }
        .btn-red:hover { background: #A93226; }
        .btn-gray   { background: var(--surface); color: var(--text-1); border: 1px solid var(--border); }
        .btn-gray:hover { background: var(--bg); }
        .btn-purple { background: #6D4FC2; color: #fff; }
        .btn-purple:hover { background: #5A3FA8; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn-xs { padding: 3px 8px; font-size: 11px; border-radius: 5px; }

        /* ── TABLE ── */
        .tbl { width: 100%; border-collapse: collapse; }
        .tbl thead tr { background: var(--bg); border-bottom: 1px solid var(--border); }
        .tbl thead th {
            padding: 10px 16px; text-align: left;
            font-size: 12px; font-weight: 500;
            color: var(--text-2); white-space: nowrap;
        }
        .tbl tbody td {
            padding: 11px 16px; font-size: 13px;
            color: var(--text-1); border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        .tbl tbody tr:hover td { background: var(--bg); }
        .tbl tbody tr:last-child td { border-bottom: none; }

        /* ── FORM ── */
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 7px; font-size: 13px;
            color: var(--text-1); background: var(--surface);
            transition: border-color .15s, box-shadow .15s;
            outline: none; font-family: 'Inter', sans-serif;
        }
        .form-select {
            padding-right: 32px; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%235C6B58' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--border-focus); box-shadow: 0 0 0 3px rgba(132,147,74,.12);
        }
        .form-label { display: block; font-size: 12px; font-weight: 500; color: var(--text-2); margin-bottom: 5px; }
        .form-group { margin-bottom: 14px; }
        .form-textarea { resize: vertical; min-height: 72px; }
        
        /* ── SEARCH BOX ── */
        .search-box { position: relative; display: flex; align-items: center; }
        .search-box input {
            padding-left: 40px !important; 
            padding-right: 12px;
            height: 42px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            color: var(--text-1);
            background: var(--bg);
            outline: none;
            width: 100%;
            transition: all 0.2s;
        }
        .search-box input:focus {
            border-color: var(--border-focus);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(132,147,74,0.12);
        }
        .search-box svg {
            position: absolute;
            left: 14px;
            color: var(--text-3);
            pointer-events: none;
        }

        /* ── RADIO PENILAIAN ── */
        .radio-card {
            display: flex; align-items: center; gap: 8px; padding: 8px 14px;
            border: 1px solid var(--border); border-radius: 7px; cursor: pointer;
            transition: all .12s; font-size: 13px; font-weight: 600;
            flex: 1; justify-content: center;
        }
        .radio-card input { display: none; }
        .radio-card:has(input:checked).mb  { border-color: var(--danger); background: var(--danger-lt); color: var(--danger); }
        .radio-card:has(input:checked).bsh { border-color: #92700A; background: #FDF8ED; color: #92700A; }
        .radio-card:has(input:checked).bsb { border-color: var(--accent); background: var(--accent-lt); color: var(--accent); }
        .radio-card:hover { border-color: #C8D0B8; background: var(--bg); }

        /* ── PROGRESS ── */
        .progress-track { height: 6px; background: var(--border); border-radius: 99px; overflow: hidden; }
        .progress-fill  { height: 100%; border-radius: 99px; transition: width .6s ease; }
        .progress-green  { background: var(--accent); }
        .progress-blue   { background: #3B6FE0; }
        .progress-yellow { background: #D4920A; }
        .progress-red    { background: var(--danger); }

        /* ── MODAL ── */
        .modal-overlay {
            position: fixed; inset: 0; 
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 10000; display: flex; align-items: center;
            justify-content: center; padding: 16px;
        }
        .modal-box {
            background: var(--surface); border-radius: 12px;
            border: 1px solid var(--border);
            width: 100%; max-height: 90vh; overflow-y: auto;
        }

        /* ── TIMELINE ── */
        .timeline-item { position: relative; padding-left: 28px; }
        .timeline-item::before { content: ''; position: absolute; left: 8px; top: 24px; bottom: 0; width: 1px; background: var(--border); }
        .timeline-item:last-child::before { display: none; }
        .timeline-dot { position: absolute; left: 0; top: 16px; width: 16px; height: 16px; border-radius: 50%; background: var(--accent); border: 2px solid var(--surface); box-shadow: 0 0 0 2px var(--accent); }

        /* ── STAT ICON ── */
        .stat-icon { width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }

        /* ── MISC ── */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .nav-icon { width: 16px; height: 16px; flex-shrink: 0; }
        .expand-row td { background: var(--bg); border-left: 2px solid var(--accent); }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn .25s ease forwards; }

        @media print {
            .no-print, aside, nav, header, [x-cloak] { display: none !important; }
            html, body { 
                height: auto !important; 
                overflow: visible !important; 
                background: white !important; 
                margin: 0 !important; 
                padding: 0 !important; 
                -webkit-print-color-adjust: exact;
            }
            
            /* Specific layout overrides */
            div.flex.h-screen { display: block !important; height: auto !important; overflow: visible !important; }
            div.flex.flex-col.flex-1 { display: block !important; height: auto !important; overflow: visible !important; }
            
            main { 
                display: block !important; 
                position: static !important; 
                overflow: visible !important; 
                width: 100% !important; 
                margin: 0 !important; 
                padding: 0 !important;
            }
            
            .max-w-7xl { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
            .card { box-shadow: none !important; border: 1px solid #eee !important; page-break-inside: avoid; }
            .fade-in { animation: none !important; transform: none !important; opacity: 1 !important; }
        }
    </style>
</head>
<body class="h-full antialiased overflow-hidden" style="background: var(--bg);">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
        @include('layouts.sidebar')

        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            @include('layouts.navbar')

            <main class="flex-1 overflow-y-auto" style="background: var(--bg);">
                <div class="max-w-7xl mx-auto p-4 md:p-5">
                    <div class="fade-in">
                        @yield('content')
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Success Alert
                            @if(session('success'))
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: "{{ session('success') }}",
                                    showConfirmButton: false,
                                    timer: 2500,
                                    timerProgressBar: true
                                });
                            @endif

                            // Error Alert
                            @if(session('error'))
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: "{{ session('error') }}",
                                    confirmButtonText: 'Tutup'
                                });
                            @endif

                            // Validation Errors
                            @if($errors->any())
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan Input',
                                    html: `<ul class="text-left text-sm space-y-1 ml-4 list-disc">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                           </ul>`,
                                    confirmButtonText: 'Perbaiki'
                                });
                            @endif
                        });
                    </script>
                </div>
            </main>
        </div>

        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden" x-cloak></div>
    </div>

    @stack('scripts')
</body>
</html>
