<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SPK Fuzzy SMART — Sistem Pendukung Keputusan Penilaian Perkembangan Siswa">
    <title>@yield('title', 'Dashboard') — SPK TK Pembina</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Client-side protection: Redirect to login if no token found
        if (!localStorage.getItem('auth_token') && window.location.pathname !== '/') {
            window.location.href = '/';
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        :root { font-size: 14px; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        /* Sidebar */
        .sidebar-gradient { background: linear-gradient(180deg, #15803d 0%, #16a34a 100%); }
        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; border-radius: 8px;
            font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.8);
            transition: all 0.15s ease; text-decoration: none;
        }
        .nav-link:hover { background: rgba(255,255,255,0.15); color: #fff; }
        .nav-link.active { background: rgba(255,255,255,0.2); color: #fff; font-weight: 700; }
        .nav-section { font-size: 10px; font-weight: 700; text-transform: uppercase; 
                       letter-spacing: 0.1em; color: rgba(255,255,255,0.4); 
                       padding: 16px 12px 4px; }

        /* Cards */
        .card { background: #fff; border-radius: 12px; border: 1px solid #f3f4f6; 
                box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .card-hover { transition: box-shadow 0.2s, transform 0.2s; }
        .card-hover:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.1); transform: translateY(-1px); }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 4px;
                 padding: 2px 8px; border-radius: 20px;
                 font-size: 11px; font-weight: 700; }
        .badge-mb  { background: #fef2f2; color: #dc2626; }
        .badge-bsh { background: #fefce8; color: #ca8a04; }
        .badge-bsb { background: #f0fdf4; color: #16a34a; }
        .badge-draft  { background: #fefce8; color: #b45309; }
        .badge-final  { background: #f0fdf4; color: #15803d; }
        .badge-aktif  { background: #f0fdf4; color: #15803d; }
        .badge-nonaktif { background: #f9fafb; color: #6b7280; }
        .badge-blue { background: #eff6ff; color: #2563eb; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px;
               padding: 7px 14px; border-radius: 8px; font-size: 13px;
               font-weight: 600; transition: all 0.15s; cursor: pointer;
               border: none; line-height: 1.4; }
        .btn-green { background: #16a34a; color: #fff; }
        .btn-green:hover { background: #15803d; box-shadow: 0 2px 8px rgba(22,163,74,.3); }
        .btn-blue { background: #2563eb; color: #fff; }
        .btn-blue:hover { background: #1d4ed8; }
        .btn-outline-blue { background: transparent; color: #2563eb; border: 1.5px solid #2563eb; }
        .btn-outline-blue:hover { background: #eff6ff; }
        .btn-red { background: #dc2626; color: #fff; }
        .btn-red:hover { background: #b91c1c; }
        .btn-gray { background: #f9fafb; color: #374151; border: 1px solid #e5e7eb; }
        .btn-gray:hover { background: #f3f4f6; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn-xs { padding: 3px 8px; font-size: 11px; border-radius: 6px; }

        /* Table */
        .tbl { width: 100%; border-collapse: collapse; }
        .tbl thead tr { background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
        .tbl thead th { padding: 10px 16px; text-align: left; font-size: 11px; 
                        font-weight: 700; text-transform: uppercase; 
                        letter-spacing: 0.05em; color: #6b7280; white-space: nowrap; }
        .tbl tbody td { padding: 11px 16px; font-size: 13px; color: #374151; 
                        border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        .tbl tbody tr:hover td { background: #f9fafb; }
        .tbl tbody tr:last-child td { border-bottom: none; }

        /* Form */
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 8px 12px; border: 1.5px solid #e5e7eb;
            border-radius: 8px; font-size: 13px; color: #111827; background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s; outline: none;
            font-family: 'Inter', sans-serif;
        }
        .form-select { padding-right: 32px; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 8px center; background-size: 16px; }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.1); }
        .form-label { display: block; font-size: 12px; font-weight: 600; 
                      color: #374151; margin-bottom: 5px; }
        .form-group { margin-bottom: 14px; }
        .form-textarea { resize: vertical; min-height: 72px; }

        /* Radio penilaian */
        .radio-card { display: flex; align-items: center; gap: 8px; padding: 8px 14px;
                      border: 1.5px solid #e5e7eb; border-radius: 8px; cursor: pointer;
                      transition: all 0.15s; font-size: 13px; font-weight: 600; flex: 1; justify-content: center; }
        .radio-card input { display: none; }
        .radio-card:has(input:checked).mb  { border-color: #dc2626; background: #fef2f2; color: #dc2626; }
        .radio-card:has(input:checked).bsh { border-color: #ca8a04; background: #fefce8; color: #ca8a04; }
        .radio-card:has(input:checked).bsb { border-color: #16a34a; background: #f0fdf4; color: #16a34a; }
        .radio-card:hover { border-color: #d1d5db; background: #f9fafb; }

        /* Progress bar */
        .progress-track { height: 8px; background: #e5e7eb; border-radius: 99px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 99px; transition: width 0.8s ease; }
        .progress-green  { background: linear-gradient(90deg, #16a34a, #4ade80); }
        .progress-blue   { background: linear-gradient(90deg, #2563eb, #60a5fa); }
        .progress-yellow { background: linear-gradient(90deg, #d97706, #fbbf24); }
        .progress-red    { background: linear-gradient(90deg, #dc2626, #f87171); }

        /* Modal */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.5); 
                         z-index: 100; display: flex; align-items: center; 
                         justify-content: center; padding: 16px; }
        .modal-box { background: #fff; border-radius: 16px; 
                     box-shadow: 0 20px 60px rgba(0,0,0,.2);
                     width: 100%; max-height: 90vh; overflow-y: auto; }

        /* Timeline */
        .timeline-item { position: relative; padding-left: 28px; }
        .timeline-item::before { content: ''; position: absolute; left: 8px; top: 24px; 
                                  bottom: 0; width: 2px; background: #e5e7eb; }
        .timeline-item:last-child::before { display: none; }
        .timeline-dot { position: absolute; left: 0; top: 16px; width: 16px; height: 16px; 
                        border-radius: 50%; background: #16a34a; border: 2px solid #fff; 
                        box-shadow: 0 0 0 2px #16a34a; }

        /* Stats icon */
        .stat-icon { width: 40px; height: 40px; border-radius: 10px; 
                     display: flex; align-items: center; justify-content: center; }

        /* Sidebar scrollbar hide */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        /* Nav icon size */
        .nav-icon { width: 16px; height: 16px; flex-shrink: 0; }

        /* Animations */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeInUp 0.35s ease forwards; }

        /* Expand table */
        .expand-row td { background: #f9fafb; border-left: 3px solid #16a34a; }

        @media print {
            aside, header { display: none !important; }
            body { overflow: visible !important; }
            main { overflow: visible !important; }
        }
    </style>
</head>
<body class="h-full bg-gray-50 antialiased overflow-hidden">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
        @include('layouts.sidebar')

        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            @include('layouts.navbar')

            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="max-w-7xl mx-auto p-4 md:p-5">
                    <div class="fade-in">
                        @if(session('success'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                                </div>
                                <button @click="show = false"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                                </div>
                                <button @click="show = false"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
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
