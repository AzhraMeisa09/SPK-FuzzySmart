@php $role = request('role', 'admin'); @endphp

<header class="h-14 bg-white border-b border-gray-100 shadow-sm flex items-center justify-between px-4 z-30 sticky top-0">
    <!-- Left -->
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = !sidebarOpen"
                class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors lg:hidden">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Breadcrumb -->
        <div class="hidden md:flex items-center gap-2 text-sm">
            <span class="font-semibold text-gray-800">@yield('page-title', 'Dashboard')</span>
        </div>
    </div>

    <!-- Right -->
    <div class="flex items-center gap-2">
        <div class="hidden sm:block">
             <div class="text-xs font-medium text-gray-400">Semester Ganjil 2024/2025</div>
        </div>

        <div class="w-px h-5 bg-gray-200 mx-1"></div>

        <!-- Notification -->
        <button class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        <!-- Avatar + Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="text-right hidden sm:block">
                    <p id="navUserName" class="text-xs font-semibold text-gray-800 leading-tight">User</p>
                    <p id="navUserRole" class="text-[10px] text-gray-400 uppercase tracking-tighter">Role</p>
                </div>
                <div id="navUserInitial" class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center text-white text-sm font-bold">
                    U
                </div>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Script to sync user data -->
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const userData = JSON.parse(localStorage.getItem('user_data') || '{}');
                    if (userData.nama_lengkap) {
                        document.getElementById('navUserName').textContent = userData.nama_lengkap;
                        document.getElementById('navUserRole').textContent = userData.role.replace('_', ' ');
                        document.getElementById('navUserInitial').textContent = userData.nama_lengkap.charAt(0).toUpperCase();
                    }
                });

                async function handleLogout() {
                    const token = localStorage.getItem('auth_token');
                    if (!token) {
                        window.location.href = '/';
                        return;
                    }

                    try {
                        await fetch('/api/logout', {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        });
                    } catch (e) {
                        console.error('Logout error:', e);
                    } finally {
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user_data');
                        window.location.href = '/';
                    }
                }
            </script>

            <div x-show="open" @click.away="open = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden" x-cloak>
                <div class="px-4 py-2.5 border-b border-gray-50">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Akun</p>
                </div>
                <div class="py-1">
                    <a href="#" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil Saya
                    </a>
                </div>
                <div class="border-t border-gray-100 py-1">
                    <button onclick="handleLogout()" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>
