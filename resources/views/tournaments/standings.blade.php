<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grup & Bagan Klasemen - {{ $tournament->name }}</title>
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
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <p class="text-xs sm:text-sm text-indigo-400 font-semibold mb-2">GRUP & BAGAN KLASEMEN</p>
                            <h1 class="text-2xl sm:text-3xl font-bold">Bagan Klasemen Grup</h1>
                            @if(count($groups) > 0)
                                <p class="text-slate-400 text-sm mt-2">
                                    Tim yang lolos: 
                                    <span class="text-indigo-400 font-semibold">{{ $setting->getQualifiedTeamsLabel() }}</span>
                                </p>
                            @else
                                <p class="text-slate-400 text-sm mt-2">
                                    Belum ada data klasemen karena belum ada pertandingan selesai dengan skor final.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-4 sm:p-6">
                <div class="grid gap-4 mb-6 sm:grid-cols-3">
                    <div class="bg-slate-900 rounded-xl border border-slate-800 p-4">
                        <p class="text-xs text-slate-400">Jumlah Grup</p>
                        <p class="text-2xl font-bold text-white">{{ $setting->group_count ?? 0 }}</p>
                    </div>
                    <div class="bg-slate-900 rounded-xl border border-slate-800 p-4">
                        <p class="text-xs text-slate-400">Tim Per Grup</p>
                        <p class="text-2xl font-bold text-white">{{ $setting->teams_per_group ?? 0 }}</p>
                    </div>
                    <div class="bg-slate-900 rounded-xl border border-slate-800 p-4">
                        <p class="text-xs text-slate-400">Tim Lolos Per Grup</p>
                        <p class="text-2xl font-bold text-green-400">{{ count($setting->qualified_teams) }}</p>
                    </div>
                </div>

                <!-- Groups Grid -->
                @if(count($groups) > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach($groups as $groupName => $teams)
                            <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
                                <!-- Group Header -->
                                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                                    <h2 class="text-2xl font-bold">Grup {{ $groupName }}</h2>
                                </div>

                                <!-- Group Table -->
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                    <thead>
                                        <tr class="bg-slate-800 border-b border-slate-700">
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">#</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">TIM</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-300">M</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-300">W</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-300">D</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-300">L</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-300">GM</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-300">GK</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-300">SG</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-300">PTS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($teams as $team)
                                            @php
                                                $isQualified = $setting->isQualified($team['ranking']);
                                                $isRelegated = in_array($team['ranking'], $setting->relegated_teams ?? []);
                                                $isLeaguePlayoffPromotion = $competitionType === 'league_playoff' && in_array($playoffType, ['promotion', 'both'], true);
                                                $isLeaguePlayoffRelegation = $competitionType === 'league_playoff' && in_array($playoffType, ['relegation', 'both'], true);
                                                $bgClass = '';
                                                $badgeClass = '';
                                                $badgeText = '';
                                                
                                                if ($competitionType === 'league') {
                                                    if ($team['ranking'] === 1) {
                                                        $bgClass = 'bg-yellow-900/30 border-l-4 border-yellow-500';
                                                        $badgeClass = 'bg-yellow-600/30 text-yellow-300';
                                                        $badgeText = '👑 Champions';
                                                    } elseif ($isRelegated) {
                                                        $bgClass = 'bg-red-900/20 border-l-4 border-red-500';
                                                        $badgeClass = 'bg-red-600/30 text-red-300';
                                                        $badgeText = '↓ Relegation';
                                                    } else {
                                                        $bgClass = 'hover:bg-slate-800/50';
                                                    }
                                                } elseif ($isLeaguePlayoffPromotion && $isQualified) {
                                                    $bgClass = 'bg-sky-900/20 border-l-4 border-sky-500';
                                                    $badgeClass = 'bg-sky-600/30 text-sky-300';
                                                    $badgeText = 'Play Off Promosi';
                                                } elseif ($isLeaguePlayoffRelegation && $isRelegated) {
                                                    $bgClass = 'bg-red-900/20 border-l-4 border-red-500';
                                                    $badgeClass = 'bg-red-600/30 text-red-300';
                                                    $badgeText = 'Play Off Degradasi';
                                                } else {
                                                    $bgClass = $isQualified ? 'bg-green-900/20 border-l-4 border-green-500' : 'hover:bg-slate-800/50';
                                                }
                                            @endphp
                                            <tr class="border-b border-slate-700 transition {{ $bgClass }}">
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-bold text-slate-300">{{ $team['ranking'] }}</span>
                                                        @if($competitionType === 'league' && $badgeText)
                                                            <span class="inline-flex items-center gap-1 px-2 py-1 {{ $badgeClass }} text-xs font-semibold rounded">
                                                                {{ $badgeText }}
                                                            </span>
                                                        @elseif($competitionType === 'league_playoff' && $isLeaguePlayoffPromotion && $isQualified)
                                                            <span class="inline-flex items-center gap-1 px-2 py-1 {{ $badgeClass }} text-xs font-semibold rounded">
                                                                {{ $badgeText }}
                                                            </span>
                                                        @elseif($competitionType !== 'league' && !($competitionType === 'league_playoff' && $isLeaguePlayoffPromotion) && $isQualified)
                                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-600/30 text-green-300 text-xs font-semibold rounded">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Lolos
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <p class="text-sm font-medium text-white">{{ $team['name'] }}</p>
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm text-slate-300">
                                                    {{ $team['wins'] + $team['draws'] + $team['losses'] }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm font-semibold text-white">
                                                    {{ $team['wins'] }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm font-semibold text-white">
                                                    {{ $team['draws'] }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm font-semibold text-white">
                                                    {{ $team['losses'] }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm text-slate-300">
                                                    {{ $team['goals_scored'] }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm text-slate-300">
                                                    {{ $team['goals_conceded'] }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm font-semibold text-white">
                                                    {{ $team['goal_difference'] >= 0 ? '+' . $team['goal_difference'] : $team['goal_difference'] }}
                                                </td>
                                                <td class="px-4 py-3 text-right text-sm font-bold text-indigo-400">
                                                    {{ $team['points'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Legend -->
                            <div class="bg-slate-800/50 px-6 py-4 border-t border-slate-700">
                                <p class="text-xs text-slate-400">
                                    M = PERTANDINGAN | W = MENANG | D = SERI | L = KALAH | GM = GOL MASUK | GK = GOL KEMASUKAN | SG = SELISIH GOL | PTS = POIN
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                    <div class="rounded-[2rem] border border-slate-800 bg-slate-950/95 p-12 text-center">
                        <p class="text-2xl font-semibold text-white">Belum ada data klasemen.</p>
                        <p class="mt-3 text-slate-400">Standings hanya akan muncul setelah pertandingan selesai dan skor final diinput.</p>
                    </div>
                @endif

                <!-- Tournament Mode: Qualified Teams Summary -->
                @if($competitionType === 'tournament')
                    <div class="mt-8 bg-slate-900 rounded-xl border border-slate-800 p-6">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Tim yang Lolos ke Babak Berikutnya
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach($groups as $groupName => $teamsList)
                                @foreach($teamsList as $team)
                                    @if($setting->isQualified($team['ranking']))
                                        <div class="bg-green-900/20 border border-green-500/30 rounded-lg p-3">
                                            <p class="text-sm font-semibold text-green-300">{{ $team['name'] }}</p>
                                            <p class="text-xs text-green-200">Grup {{ $groupName }} - Ranking {{ $team['ranking'] }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- League Mode: Categories Summary -->
                    <div class="mt-8 space-y-6">
                        <!-- Champions -->
                        <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
                            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                <span class="text-2xl">👑</span>
                                <span>Champions</span>
                                <span class="ml-auto inline-flex items-center gap-1 px-3 py-1 bg-yellow-600/30 text-yellow-300 text-xs font-semibold rounded">EMAS</span>
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                @php $found = false; @endphp
                                @foreach($groups as $groupName => $teamsList)
                                    @foreach($teamsList as $team)
                                        @if($team['ranking'] === 1)
                                            <div class="bg-yellow-900/30 border border-yellow-500/50 rounded-lg p-3">
                                                <p class="text-sm font-semibold text-yellow-300">{{ $team['name'] }}</p>
                                                <p class="text-xs text-yellow-200">Grup {{ $groupName }}</p>
                                            </div>
                                            @php $found = true; @endphp
                                        @endif
                                    @endforeach
                                @endforeach
                                @if(!$found)
                                    <p class="col-span-full text-sm text-slate-400 py-4">Tidak ada data</p>
                                @endif
                            </div>
                        </div>

                        <!-- Runner-up -->
                        <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
                            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                <span class="text-2xl">🥈</span>
                                <span>Runner-up</span>
                                <span class="ml-auto inline-flex items-center gap-1 px-3 py-1 bg-gray-600/30 text-gray-300 text-xs font-semibold rounded">SILVER</span>
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                @php $found = false; @endphp
                                @foreach($groups as $groupName => $teamsList)
                                    @foreach($teamsList as $team)
                                        @if($team['ranking'] === 2)
                                            <div class="bg-gray-700/30 border border-gray-400/50 rounded-lg p-3">
                                                <p class="text-sm font-semibold text-gray-300">{{ $team['name'] }}</p>
                                                <p class="text-xs text-gray-200">Grup {{ $groupName }}</p>
                                            </div>
                                            @php $found = true; @endphp
                                        @endif
                                    @endforeach
                                @endforeach
                                @if(!$found)
                                    <p class="col-span-full text-sm text-slate-400 py-4">Tidak ada data</p>
                                @endif
                            </div>
                        </div>

                        <!-- Third Place -->
                        <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
                            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                <span class="text-2xl">🥉</span>
                                <span>Third Place</span>
                                <span class="ml-auto inline-flex items-center gap-1 px-3 py-1 bg-orange-600/30 text-orange-300 text-xs font-semibold rounded">COKLAT</span>
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                @php $found = false; @endphp
                                @foreach($groups as $groupName => $teamsList)
                                    @foreach($teamsList as $team)
                                        @if($team['ranking'] === 3)
                                            <div class="bg-orange-900/30 border border-orange-600/50 rounded-lg p-3">
                                                <p class="text-sm font-semibold text-orange-300">{{ $team['name'] }}</p>
                                                <p class="text-xs text-orange-200">Grup {{ $groupName }}</p>
                                            </div>
                                            @php $found = true; @endphp
                                        @endif
                                    @endforeach
                                @endforeach
                                @if(!$found)
                                    <p class="col-span-full text-sm text-slate-400 py-4">Tidak ada data</p>
                                @endif
                            </div>
                        </div>

                        <!-- Relegation -->
                        @if(!empty($setting->relegated_teams))
                            <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                    <span class="text-2xl">↓</span>
                                    <span>Relegation</span>
                                    <span class="ml-auto inline-flex items-center gap-1 px-3 py-1 bg-red-600/30 text-red-300 text-xs font-semibold rounded">MERAH</span>
                                </h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                    @foreach($groups as $groupName => $teamsList)
                                        @foreach($teamsList as $team)
                                            @if(in_array($team['ranking'], $setting->relegated_teams))
                                                <div class="bg-red-900/20 border border-red-500/50 rounded-lg p-3">
                                                    <p class="text-sm font-semibold text-red-300">{{ $team['name'] }}</p>
                                                    <p class="text-xs text-red-200">Grup {{ $groupName }} - Ranking {{ $team['ranking'] }}</p>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- League Playoff: Promotion Bracket -->
                @if($hasPlayoffPromotion && !empty($playoffPromotionTeams))
                    <div class="mt-8 bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
                            <h2 class="text-2xl font-bold">Bracket Gugur - Play Off Promosi</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach($playoffPromotionTeams as $slot => $team)
                                    <div class="bg-sky-900/20 border border-sky-500/50 rounded-lg p-3">
                                        <p class="text-sm font-semibold text-sky-300">{{ $team }}</p>
                                        <p class="text-xs text-sky-200">{{ $slot }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- League Playoff: Relegation Bracket -->
                @if($hasPlayoffRelegation && !empty($playoffRelegationTeams))
                    <div class="mt-8 bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-600 to-rose-600 px-6 py-4">
                            <h2 class="text-2xl font-bold">Bracket Gugur - Play Off Degradasi</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach($playoffRelegationTeams as $slot => $team)
                                    <div class="bg-red-900/20 border border-red-500/50 rounded-lg p-3">
                                        <p class="text-sm font-semibold text-red-300">{{ $team }}</p>
                                        <p class="text-xs text-red-200">{{ $slot }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Info Card -->
                <div class="mt-6 bg-indigo-900/20 border border-indigo-500/30 rounded-lg p-4">
                    <p class="text-sm text-indigo-200">
                        <strong>💡 Info:</strong>
                        @if($competitionType === 'league')
                            Sistem Liga menampilkan kategori Champions (Peringkat 1) dan Relegation.
                        @else
                            Pengaturan kelolosan grup dapat diubah di menu <span class="font-semibold">Pengaturan Turnamen</span>. Setiap perubahan akan langsung mempengaruhi tampilan klasemen ini.
                        @endif
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
