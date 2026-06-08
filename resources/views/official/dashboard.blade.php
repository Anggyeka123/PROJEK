@extends('official.layouts.app')

@section('title', 'Beranda')

@section('content')
    <div class="space-y-6">
        <section class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-6 shadow-2xl shadow-slate-950/40">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-20 w-20 rounded-3xl bg-slate-800 border border-slate-700 overflow-hidden flex items-center justify-center">
                        @if($team->logo)
                            <img src="{{ Storage::url($team->logo) }}" alt="Logo {{ $team->name }}" class="h-full w-full object-cover" />
                        @else
                            <span class="text-3xl text-violet-300">⚽</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Tim Resmi</p>
                        <h1 class="text-3xl font-semibold text-white">{{ $team->name }}</h1>
                        <p class="mt-1 text-sm text-slate-400">{{ $team->city ?? '-' }}, {{ $team->country ?? '-' }}</p>
                    </div>
                </div>
                <div class="rounded-3xl bg-slate-950/70 px-4 py-3 text-sm text-slate-300 border border-slate-800">
                    <p class="text-slate-400 text-xs uppercase tracking-[0.35em]">Status verifikasi</p>
                    <p class="mt-1 font-semibold {{ $team->verification_status === 'verified' ? 'text-emerald-300' : 'text-amber-300' }}">{{ $team->verification_status ? ucfirst($team->verification_status) : 'Belum diverifikasi' }}</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Pemain</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $totalPlayers }}</p>
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Goalkeeper</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $totalGoalkeepers }}</p>
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Official</p>
                    <p class="mt-3 text-3xl font-semibold text-white">{{ $totalOfficials }}</p>
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Kapten</p>
                    <p class="mt-3 text-2xl font-semibold text-white">{{ $captain?->player_name ?? 'Belum ditetapkan' }}</p>
                </div>
            </div>

            <div class="mt-6 rounded-[2rem] border border-slate-800 bg-slate-950/95 p-6 shadow-2xl shadow-slate-950/40">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Official Team</p>
                        <h2 class="mt-3 text-2xl font-semibold text-white">Ringkasan Official</h2>
                        @if($totalOfficials >= 7)
                            <span class="mt-3 inline-flex rounded-full bg-red-500/10 px-3 py-1 text-sm font-semibold text-red-300">Kuota Official Penuh</span>
                        @endif
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    <div class="rounded-3xl bg-slate-900 p-5">
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Total Official Terdaftar</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $totalOfficials }}</p>
                    </div>
                    <div class="rounded-3xl bg-slate-900 p-5">
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Sisa Slot Official</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ max(0, 7 - $totalOfficials) }}</p>
                    </div>
                    <div class="rounded-3xl bg-slate-900 p-5">
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Manager</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $officialManagerCount }}</p>
                    </div>
                    <div class="rounded-3xl bg-slate-900 p-5">
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Coach</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $officialCoachCount }}</p>
                    </div>
                    <div class="rounded-3xl bg-slate-900 p-5">
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Assistant Coach</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $officialAssistantCoachCount }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-6 shadow-2xl shadow-slate-950/40">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Informasi Turnamen</p>
                        <h2 class="mt-3 text-2xl font-semibold text-white">Turnamen Anda</h2>
                    </div>
                    <span class="rounded-full bg-violet-500/10 px-3 py-2 text-xs font-semibold text-violet-300">Aktif</span>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse($tournamentTeams as $tt)
                        <div class="rounded-3xl border border-slate-800 bg-slate-950 p-4">
                            <p class="font-semibold text-white">{{ $tt->tournament?->name ?? 'Turnamen tidak tersedia' }}</p>
                            <p class="text-sm text-slate-400">{{ optional($tt->tournament?->match_date)->format('d M Y') ?? 'Tanggal belum tersedia' }}</p>
                            <p class="mt-3 text-sm text-slate-300">Pendaftaran: <span class="font-semibold text-white">{{ $tt->registration_status ?? 'Belum dikonfirmasi' }}</span></p>
                        </div>
                    @empty
                        <div class="rounded-3xl border border-slate-800 bg-slate-950 p-6 text-center text-slate-400">
                            <p class="font-semibold text-white">Belum ada turnamen resmi</p>
                            <p class="mt-2 text-sm">Tim Anda belum terdaftar di turnamen manapun.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-6 shadow-2xl shadow-slate-950/40">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Pertandingan Berikutnya</p>
                    @if($nextMatch)
                        <div class="mt-5 space-y-4">
                            <p class="text-sm text-slate-400">{{ optional($nextMatch->tournament)->name ?? 'Turnamen' }}</p>
                            <div class="rounded-3xl bg-slate-950 p-4">
                                <p class="text-lg font-semibold text-white">{{ $nextMatch->homeTeam?->team?->name ?? $nextMatch->home_team_key ?? $nextMatch->source_home ?? 'TBD' }} vs {{ $nextMatch->awayTeam?->team?->name ?? $nextMatch->away_team_key ?? $nextMatch->source_away ?? 'TBD' }}</p>
                                <p class="mt-2 text-sm text-slate-400">{{ optional($nextMatch->match_date)->format('d M Y H:i') }}</p>
                                <p class="text-sm text-slate-400">{{ $nextMatch->venue ?? 'Lokasi belum ditetapkan' }}</p>
                                <span class="inline-flex rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">{{ ucfirst($nextMatch->status) }}</span>
                            </div>
                        </div>
                    @else
                        <div class="mt-5 rounded-3xl border border-slate-800 bg-slate-950 p-6 text-center text-slate-400">
                            <p class="font-semibold text-white">Tidak ada jadwal pertandingan terdekat.</p>
                            <p class="mt-2 text-sm">Tambahkan jadwal melalui panel turnamen admin jika sudah tersedia.</p>
                        </div>
                    @endif
                </div>

                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-6 shadow-2xl shadow-slate-950/40">
                    <h3 class="text-lg font-semibold text-white">Quick Action</h3>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                <a href="{{ route('official.players.index') }}" class="rounded-3xl bg-violet-500 px-4 py-4 text-center text-sm font-semibold text-white hover:bg-violet-400 transition">Kelola Pemain</a>
                        <a href="{{ route('official.officials.index') }}" class="rounded-3xl bg-slate-950 px-4 py-4 text-center text-sm font-semibold text-slate-200 hover:bg-slate-900 transition">Kelola Official</a>
                        <a href="{{ route('official.schedule') }}" class="rounded-3xl bg-violet-500 px-4 py-4 text-center text-sm font-semibold text-white hover:bg-violet-400 transition">Jadwal</a>
                        <button type="button" class="rounded-3xl border border-slate-800 bg-slate-950 px-4 py-4 text-sm font-semibold text-slate-500 opacity-70 cursor-not-allowed">Klasemen (Coming Soon)</button>
                        <button type="button" class="rounded-3xl border border-slate-800 bg-slate-950 px-4 py-4 text-sm font-semibold text-slate-500 opacity-70 cursor-not-allowed">Bracket (Coming Soon)</button>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
