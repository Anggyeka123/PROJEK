<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bagan Bracket - {{ $tournament->name }}</title>
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
                <a href="{{ route('tournaments.manage', $tournament) }}" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition flex items-center gap-3">
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
                <a href="#" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Kelola Jadwal & Skor
                </a>
                <a href="{{ route('tournaments.standings', $tournament) }}" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Bagan Klasemen
                </a>
                <a href="#" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 19H9a6 6 0 016-6h.01M15 19h4a2 2 0 002-2v-5a6 6 0 00-6-6h-4a6 6 0 00-6 6v5a2 2 0 002 2h4m-12 0a2 2 0 012-2h8a2 2 0 012 2"></path>
                    </svg>
                    Manajemen Peserta
                </a>
                <a href="#" class="w-full text-left px-4 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Akses Manager
                </a>
                <a href="{{ route('tournaments.settings', $tournament) }}" class="w-full text-left px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Pengaturan Turnamen
                </a>
            </nav>

            <div class="p-4 border-t border-slate-800">
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
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <p class="text-xs sm:text-sm text-emerald-400 font-semibold mb-2">BAGAN BRACKET</p>
                            <h1 class="text-2xl sm:text-3xl font-bold">Atur Babak Knockout</h1>
                            <p class="text-slate-400 text-sm mt-2">Sesuaikan struktur babak knockout setelah fase grup selesai.</p>
                        </div>
                    </div>
                </div>
            </header>

            @if($competitionType === 'league')
                <div class="p-4 sm:p-6 max-w-full">
                    <div class="mb-6 p-4 bg-amber-900/20 border border-amber-500/30 rounded-lg text-amber-200 text-sm flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <strong class="block mb-1">⊘ Bracket Gugur Tidak Tersedia</strong>
                            <p>Sistem liga biasa tidak menggunakan bracket gugur.</p>
                        </div>
                    </div>
                </div>
            @else
                @include('tournaments.partials.bracket-settings-panel')
            @endif
        </main>
    </div>
</body>
</html>
