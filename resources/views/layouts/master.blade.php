<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <<title>Sistem Arsip Surat Masuk Direksi</title>
        
    <link rel="icon" type="image/png" href="{{ asset('images/logo-ptpn4.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    
    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-gradient-to-b from-white to-blue-100 border-r shadow-lg flex flex-col items-center py-6 flex-shrink-0 z-20">
            <div class="mb-10 text-green-600 font-bold text-2xl flex flex-col items-center">
                <!-- KODE BARU -->
                <div class="flex justify-center items-center mb-8">
                    <img src="{{ asset('images/logo-ptpn4.png') }}" alt="Logo PTPN IV" class="w-24 h-auto drop-shadow-md transition hover:scale-105 duration-300">
                </div>
            </div>

            <div class="w-full px-4 space-y-3">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-gradient-to-b from-green-500 to-green-700 shadow-inner ring-2 ring-green-300' : 'bg-gradient-to-b from-green-400 to-green-600 hover:opacity-90 shadow-md' }} block w-full text-center border-2 border-green-700 text-white font-bold py-2 rounded-md transition">
                    Dashboard
                </a>

                <a href="{{ route('surat-masuk.index') }}" class="{{ request()->routeIs('surat-masuk.*') ? 'bg-gradient-to-b from-green-500 to-green-700 shadow-inner ring-2 ring-green-300' : 'bg-gradient-to-b from-green-400 to-green-600 hover:opacity-90 shadow-md' }} block w-full text-center border-2 border-green-700 text-white font-bold py-2 rounded-md transition">
                    Surat Masuk
                </a>

                @if(auth()->check() && auth()->user()->role === 'krani')
                    <a href="{{ route('disposisi.index') }}" class="{{ request()->routeIs('disposisi.*') ? 'bg-gradient-to-b from-green-500 to-green-700 shadow-inner ring-2 ring-green-300' : 'bg-gradient-to-b from-green-400 to-green-600 hover:opacity-90 shadow-md' }} block w-full text-center border-2 border-green-700 text-white font-bold py-2 rounded-md transition">
                        Disposisi
                    </a>
                @endif

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('riwayat-aksi.index') }}" class="{{ request()->routeIs('riwayat-aksi.*') ? 'bg-gradient-to-b from-green-500 to-green-700 shadow-inner ring-2 ring-green-300' : 'bg-gradient-to-b from-green-400 to-green-600 hover:opacity-90 shadow-md' }} block w-full text-center border-2 border-green-700 text-white font-bold py-2 rounded-md transition">
                        Riwayat Aksi
                    </a>
                @endif

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'bg-gradient-to-b from-green-500 to-green-700 shadow-inner ring-2 ring-green-300' : 'bg-gradient-to-b from-green-400 to-green-600 hover:opacity-90 shadow-md' }} block w-full text-center border-2 border-green-700 text-white font-bold py-2 rounded-md transition">
                        Manajemen Krani
                    </a>
                @endif
            </div>
        </aside>

        <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <header class="bg-white border-b px-6 py-3 flex justify-between items-center shadow-sm flex-shrink-0 z-10" x-data="realtimeClock()">
                <div class="text-sm font-bold text-gray-700 tracking-wide" x-text="currentTime">
                    Memuat waktu...
                </div>
                
                <div class="flex items-center space-x-4">
                    <button class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>
<div x-data="{ open: false }" class="relative">
    
    <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none hover:bg-gray-100 p-2 rounded-md transition">
        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-bold uppercase">
            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
        </div>
        <span class="font-semibold text-sm">{{ Auth::user()->name ?? 'User' }}</span>
        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" 
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl py-1 border border-gray-200 z-50"
         style="display: none;">
        
        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-medium transition">
            Profil Saya
        </a>
            <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 font-bold hover:bg-red-50 hover:text-red-700 transition duration-150 ease-in-out border-t border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </button>
            </form>
            </div>
        </div>
                </div>
            </header>

            @yield('content')
            
        </main>
    </div>

    @stack('modals')

    <script>
        function realtimeClock() {
            return {
                currentTime: '',
                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000); 
                },
                updateTime() {
                    const d = new Date();
                    // Mengubah nama hari ke Bahasa Indonesia
                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    // Mengubah nama bulan ke Bahasa Indonesia
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    
                    let dayName = days[d.getDay()];
                    let date = d.getDate().toString().padStart(2, '0');
                    let monthName = months[d.getMonth()];
                    let year = d.getFullYear();
                    
                    let h = d.getHours().toString().padStart(2, '0');
                    let m = d.getMinutes().toString().padStart(2, '0');
                    
                    // Format hasil: Jumat, 10 Juli 2026 | 00:26 WIB
                    this.currentTime = `${dayName}, ${date} ${monthName} ${year} | ${h}:${m} WIB`;
                }
            }
        }
    </script>
</body>
</html>