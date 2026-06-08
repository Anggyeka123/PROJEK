@extends('official.layouts.app')

@section('title', 'Pemain')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-violet-300">Roster Tim</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Pemain Official {{ $team->name }}</h1>
                <p class="mt-2 text-sm text-slate-400">Daftar pemain tim dengan tampilan card yang lebih ramah mobile.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('official.players.create') }}" class="rounded-2xl bg-violet-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-violet-400">
                    Tambah Pemain
                </a>
                <a href="{{ route('official.dashboard') }}" class="rounded-2xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-200 hover:border-violet-400 hover:text-white transition">
                    Beranda
                </a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Total Pemain</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $totalPlayers }}</p>
            </div>
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Goalkeeper</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $totalGoalkeepers }}</p>
            </div>
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Filter & Cari</p>
                <p class="mt-3 text-sm text-slate-400">Cari berdasarkan nama, nomor, atau posisi.</p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[2fr_1fr]">
            <div class="space-y-4">
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-xs uppercase tracking-[0.35em] text-slate-500">Cari Pemain</span>
                            <input id="playerSearch" type="text" placeholder="Cari nama atau nomor" class="mt-2 w-full rounded-3xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" />
                        </label>
                        <label class="block">
                            <span class="text-xs uppercase tracking-[0.35em] text-slate-500">Filter Posisi</span>
                            <select id="positionFilter" class="mt-2 w-full rounded-3xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none">
                                <option value="">Semua Posisi</option>
                                <option value="GK">GK</option>
                                <option value="Anchor">Anchor</option>
                                <option value="Flank">Flank</option>
                                <option value="Pivot">Pivot</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div id="playersList" class="space-y-4">
                    @forelse($players as $player)
                        <article class="player-card rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40" data-name="{{ strtolower($player->player_name) }}" data-number="{{ $player->shirt_number }}" data-position="{{ strtolower($player->dominant_position) }}">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="h-20 w-20 overflow-hidden rounded-3xl bg-slate-800 border border-slate-700">
                                        @if($player->photo)
                                            <img src="{{ Storage::url($player->photo) }}" alt="Foto {{ $player->player_name }}" class="h-full w-full object-cover" />
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-slate-500">👤</div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-xl font-semibold text-white">{{ $player->player_name }}</p>
                                        <p class="mt-1 text-sm text-slate-400">#{{ $player->shirt_number ?? '-' }} • {{ $player->dominant_position ?? '-' }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $player->positions ? implode(', ', $player->positions) : 'Posisi belum ditentukan' }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-start gap-3 sm:items-end">
                                    @if($player->is_captain)
                                        <span class="inline-flex rounded-full bg-yellow-500/10 px-4 py-2 text-xs font-semibold text-yellow-300">Kapten</span>
                                    @endif
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('official.players.edit', $player) }}" class="rounded-2xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-500 transition">Ubah</a>
                                        <form action="{{ route('official.players.destroy', $player) }}" method="POST" onsubmit="return confirm('Hapus pemain ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-2xl bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-500 transition">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-8 text-center text-slate-400 shadow-2xl shadow-slate-950/40">
                            <p class="text-lg font-semibold text-white">Belum ada pemain terdaftar.</p>
                            <p class="mt-2 text-sm">Tambahkan pemain baru untuk mulai membangun skuad tim Anda.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <aside class="space-y-4">
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Ringkasan Tim</p>
                    <div class="mt-5 space-y-4">
                        <div class="rounded-3xl bg-slate-950 p-4">
                            <p class="text-sm text-slate-400">Total pemain</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ $totalPlayers }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-950 p-4">
                            <p class="text-sm text-slate-400">Total GK</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ $totalGoalkeepers }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-950 p-4">
                            <p class="text-sm text-slate-400">Tim Anda</p>
                            <p class="mt-2 text-base font-semibold text-white">{{ $team->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Navigasi Cepat</p>
                    <div class="mt-4 grid gap-3">
                        <a href="{{ route('official.dashboard') }}" class="rounded-3xl bg-slate-950 px-4 py-4 text-center text-sm font-semibold text-slate-200 hover:bg-slate-900 transition">Beranda</a>
                        <button type="button" class="rounded-3xl bg-slate-950 px-4 py-4 text-sm font-semibold text-slate-500 opacity-70 cursor-not-allowed">Jadwal (Coming Soon)</button>
                        <button type="button" class="rounded-3xl bg-slate-950 px-4 py-4 text-sm font-semibold text-slate-500 opacity-70 cursor-not-allowed">Klasemen (Coming Soon)</button>
                        <button type="button" class="rounded-3xl bg-slate-950 px-4 py-4 text-sm font-semibold text-slate-500 opacity-70 cursor-not-allowed">Bracket (Coming Soon)</button>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('playerSearch');
        const positionFilter = document.getElementById('positionFilter');
        const playerCards = Array.from(document.querySelectorAll('.player-card'));
        const noResults = document.createElement('div');
        noResults.className = 'rounded-[2rem] border border-slate-800 bg-slate-900/95 p-8 text-center text-slate-400 shadow-2xl shadow-slate-950/40';
        noResults.innerHTML = '<p class="text-lg font-semibold text-white">Tidak ada pemain yang cocok.</p><p class="mt-2 text-sm">Ubah kata kunci pencarian atau filter posisi Anda.</p>';

        function updatePlayerVisibility() {
            const searchTerm = searchInput.value.trim().toLowerCase();
            const selectedPosition = positionFilter.value;
            let visibleCount = 0;

            playerCards.forEach(card => {
                const name = card.dataset.name || '';
                const number = card.dataset.number || '';
                const position = card.dataset.position || '';

                const matchesSearch = !searchTerm || name.includes(searchTerm) || number.includes(searchTerm);
                const matchesPosition = !selectedPosition || position === selectedPosition.toLowerCase();

                if (matchesSearch && matchesPosition) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            const playersList = document.getElementById('playersList');
            const existingNoResults = playersList.querySelector('.no-results');

            if (visibleCount === 0) {
                if (!existingNoResults) {
                    noResults.classList.add('no-results');
                    playersList.appendChild(noResults);
                }
            } else if (existingNoResults) {
                existingNoResults.remove();
            }
        }

        if (searchInput && positionFilter) {
            searchInput.addEventListener('input', updatePlayerVisibility);
            positionFilter.addEventListener('change', updatePlayerVisibility);
        }
    </script>
@endsection
