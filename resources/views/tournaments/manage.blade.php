<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tournament->name }} - Ikhtisar | Futsal League</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-white">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:w-64 bg-slate-900 border-r border-slate-800 flex-col sticky top-0 h-screen">
                <div class="p-6 border-b border-slate-800">
                    <h2 class="text-lg font-bold">{{ $tournament->name }}</h2>
                    <p class="text-xs text-slate-400 mt-1">{{ $tournament->division }}</p>
                </div>

                <nav class="flex-1 overflow-y-auto p-4 space-y-2">
                    <a href="#" class="w-full text-left px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ikhtisar Sistem
                    </a>
                    <a href="#" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Verifikasi Berkas
                    </a>
                    <a href="{{ route('tournaments.settings', $tournament) }}" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan Turnamen
                    </a>
                    
                        <a href="{{ route('tournaments.manageSchedule', $tournament) }}" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Kelola Jadwal & Skor
                        </a>
                    <a href="{{ route('tournaments.standings', $tournament) }}" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Bagan Klasemen
                    </a>
                    <a href="{{ route('tournaments.bracketAdmin', $tournament) }}" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"></path>
                        </svg>
                        Bracket Gugur
                    </a>
                    <a href="{{ route('tournaments.participants.index', $tournament) }}" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 19H9a6 6 0 016-6h.01M15 19h4a2 2 0 002-2v-5a6 6 0 00-6-6h-4a6 6 0 00-6 6v5a2 2 0 002 2h4m-12 0a2 2 0 012-2h8a2 2 0 012 2"></path>
                        </svg>
                        Manajemen Peserta
                    </a>
                    <!-- Akses Manager link removed (token management moved to participants) -->
                    
                </nav>

                <div class="p-4 border-t border-slate-800 space-y-2">
                    <a href="{{ route('tournaments.index') }}" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Daftar
                    </a>
                </div>
            </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="border-b border-slate-800 bg-slate-900 bg-opacity-50 backdrop-blur sticky top-0 z-40">
                <div class="px-4 sm:px-6 py-6">
                    <p class="text-xs sm:text-sm text-indigo-400 font-semibold mb-2">IKHTISAR TURNAMEN</p>
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold">{{ $tournament->name }}</h1>
                            <p class="text-slate-400 text-sm mt-2">Status, kuota pendaftaran, dan data statistik live saat ini.</p>
                        </div>
                        <button onclick="toggleMobileMenu()" class="md:hidden text-slate-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="p-4 sm:p-6">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <!-- Total Pendaftar -->
                    <div class="bg-slate-900 rounded-xl border border-slate-800 p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs sm:text-sm text-slate-400 font-medium">TOTAL PENDAFTAR</p>
                            <div class="p-2 bg-indigo-600/20 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl sm:text-3xl font-bold text-white">{{ $statistics['total_pendaftar'] }}</p>
                    </div>

                    <!-- Terverifikasi -->
                    <div class="bg-slate-900 rounded-xl border border-slate-800 p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs sm:text-sm text-slate-400 font-medium">TERVERIFIKASI</p>
                            <div class="p-2 bg-green-600/20 rounded-lg">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl sm:text-3xl font-bold text-white">{{ $statistics['terverifikasi'] }}</p>
                    </div>

                    <!-- Butuh Verifikasi -->
                    <div class="bg-slate-900 rounded-xl border border-slate-800 p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs sm:text-sm text-slate-400 font-medium">BUTUH VERIFIKASI</p>
                            <div class="p-2 bg-yellow-600/20 rounded-lg">
                                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl sm:text-3xl font-bold text-white">{{ $statistics['butuh_verifikasi'] }}</p>
                    </div>

                    <!-- Ditolak / Draft -->
                    <div class="bg-slate-900 rounded-xl border border-slate-800 p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs sm:text-sm text-slate-400 font-medium">DITOLAK / DRAFT</p>
                            <div class="p-2 bg-red-600/20 rounded-lg">
                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl sm:text-3xl font-bold text-white">{{ $statistics['ditolak_draft'] }}</p>
                    </div>
                </div>

                <!-- Team Readiness Section -->
                <div class="bg-slate-900 rounded-xl border border-slate-800 p-4 sm:p-6 mb-8">
                    <h2 class="text-lg sm:text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Persentase Kesiapan Slot Tim
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-slate-300">ASA</p>
                                <p class="text-sm font-semibold text-indigo-400">0% Kesiapan Slot</p>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Verification Section -->
                <div class="bg-slate-900 rounded-xl border border-slate-800 p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-white mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Pusat Verifikasi Cepat
                            </h2>
                            <p class="text-slate-400 text-sm">Beberapa tim sekolah baru telah mengisi roster pendaftaran mandiri dan menunggu verifikasi berkas dari panitia pelaksana.</p>
                        </div>
                        <button class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition whitespace-nowrap flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Buka Halaman Verifikasi
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden fixed inset-0 bg-slate-950/95 z-30 p-4 top-0 left-0 right-0 bottom-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold">Menu</h2>
            <button onclick="toggleMobileMenu()" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <nav class="space-y-2">
            <a href="#" class="w-full text-left px-4 py-3 bg-indigo-600 text-white rounded-lg font-semibold flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Ikhtisar Sistem
            </a>
            <a href="#" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Verifikasi Berkas
            </a>
            <a href="#" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Kelola Jadwal & Skor
            </a>
            <a href="#" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Grup & Bagan Klasemen
            </a>
            @if($bracketAdminAvailable)
            <a href="{{ route('tournaments.bracketAdmin', $tournament) }}" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"></path>
                </svg>
                Bracket Gugur
            </a>
            @endif
            <a href="{{ route('tournaments.participants.index', $tournament) }}" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 19H9a6 6 0 016-6h.01M15 19h4a2 2 0 002-2v-5a6 6 0 00-6-6h-4a6 6 0 00-6 6v5a2 2 0 002 2h4m-12 0a2 2 0 012-2h8a2 2 0 012 2"></path>
                </svg>
                Manajemen Peserta
            </a>
            <a href="#" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Akses Manager
            </a>
            <a href="#" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Pengaturan Turnamen
            </a>
            <a href="{{ route('tournaments.index') }}" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg mt-6 border-t border-slate-800 pt-4 flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar
            </a>
        </nav>
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
