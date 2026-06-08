@extends('official.layouts.app')

@section('title', 'Official Tim')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-violet-300">Official Tim</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Daftar Official {{ $team->name }}</h1>
                <p class="mt-2 text-sm text-slate-400">Kelola official tim Anda dengan mudah dalam tampilan card yang ramah mobile.</p>
            </div>
            <a href="{{ route('official.officials.create') }}" class="rounded-2xl bg-violet-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-violet-400">
                Tambah Official
            </a>
        </div>

        @php
            $totalOfficials = $officials->count();
            $managerCount = $officials->where('role', 'Manager')->count();
            $coachCount = $officials->where('role', 'Coach')->count();
            $assistantCoachCount = $officials->where('role', 'Assistant Coach')->count();
            $slotsRemaining = max(0, 7 - $totalOfficials);
            $limitReached = $totalOfficials >= 7;
        @endphp

        <div class="grid gap-4 sm:grid-cols-3 xl:grid-cols-6 mb-6">
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40 sm:col-span-2">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Ringkasan Official</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $totalOfficials }} / 7 Official Terdaftar</p>
                <p class="mt-2 text-sm text-slate-400">Kelola official tim Anda dengan batas maksimal 7 official.</p>
            </div>
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Maksimum Official</p>
                <p class="mt-3 text-3xl font-semibold text-white">7</p>
            </div>
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Sisa Slot</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $slotsRemaining }}</p>
            </div>
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Manager</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $managerCount }} / 1</p>
            </div>
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Coach</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $coachCount }} / 1</p>
            </div>
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Assistant Coach</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $assistantCoachCount }} / 2</p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[2fr_1fr] mb-6">
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-xs uppercase tracking-[0.35em] text-slate-500">Cari Official</span>
                        <input id="officialSearch" type="text" placeholder="Cari nama atau jabatan" class="mt-2 w-full rounded-3xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" />
                    </label>
                    <label class="block">
                        <span class="text-xs uppercase tracking-[0.35em] text-slate-500">Filter Jabatan</span>
                        <select id="roleFilter" class="mt-2 w-full rounded-3xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none">
                            <option value="">Semua Jabatan</option>
                            <option value="manager">Manager</option>
                            <option value="coach">Coach</option>
                            <option value="assistant coach">Assistant Coach</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </label>
                </div>
            </div>
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Progress Kuota Official</p>
                <div class="mt-4 rounded-3xl bg-slate-950 p-4">
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm text-slate-400">{{ $totalOfficials }} / 7 Official Terdaftar</p>
                        <p class="text-sm font-semibold text-white">{{ $slotsRemaining }} slot tersisa</p>
                    </div>
                    <div class="mt-4 h-3 overflow-hidden rounded-full bg-slate-800">
                        <div class="h-3 rounded-full bg-violet-500" style="width: {{ $totalOfficials / 7 * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        @if($limitReached)
            <div class="rounded-[2rem] border border-amber-500/30 bg-amber-500/10 p-5 text-sm text-amber-200 shadow-2xl shadow-slate-950/40 mb-6">
                <p class="font-semibold">Kuota Official Penuh</p>
                <p class="mt-2 text-slate-300">Sudah mencapai batas official untuk tim ini. Hapus official yang ada sebelum menambahkan official baru.</p>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($officials as $official)
                @php
                    $badgeClasses = 'bg-slate-700/10 text-slate-300';
                    if ($official->role === 'Manager') {
                        $badgeClasses = 'bg-blue-500/10 text-blue-300';
                    } elseif ($official->role === 'Coach') {
                        $badgeClasses = 'bg-emerald-500/10 text-emerald-300';
                    } elseif ($official->role === 'Assistant Coach') {
                        $badgeClasses = 'bg-amber-500/10 text-amber-300';
                    }
                @endphp
                <article class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-5 shadow-2xl shadow-slate-950/40" data-name="{{ strtolower($official->official_name) }}" data-role="{{ strtolower($official->role) }}">
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <div>
                                <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Jabatan</p>
                                <p class="mt-2 text-lg font-semibold text-white">{{ $official->role }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses }}">{{ $official->role }}</span>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Nama Official</p>
                            <p class="mt-2 text-xl font-semibold text-white">{{ $official->official_name }}</p>
                        </div>
                        <div class="space-y-2 text-sm text-slate-300">
                            <p><span class="font-semibold text-slate-400">Telepon:</span> {{ $official->contact_phone ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-400">Email:</span> {{ $official->contact_email ?? '-' }}</p>
                        </div>
                        @if($official->tournamentTeam?->tournament)
                            <div class="rounded-3xl bg-slate-950 p-3 text-sm text-slate-400">
                                Turnamen: <span class="font-semibold text-white">{{ $official->tournamentTeam->tournament->name }}</span>
                            </div>
                        @endif
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('official.officials.edit', $official) }}" class="rounded-3xl bg-indigo-600 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-indigo-500 transition">
                                Edit
                            </a>
                            <form action="{{ route('official.officials.destroy', $official) }}" method="POST" onsubmit="return confirm('Hapus official ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full rounded-3xl bg-red-600 px-4 py-3 text-sm font-semibold text-white hover:bg-red-500 transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-12 text-center shadow-2xl shadow-slate-950/40 col-span-full">
                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-800 text-4xl text-slate-500">🤝</div>
                    <p class="text-lg font-semibold text-white">Belum ada official terdaftar.</p>
                    <p class="mt-2 text-sm text-slate-400">Tambahkan official pertama untuk mulai melengkapi tim Anda.</p>
                    <a href="{{ route('official.officials.create') }}" class="mt-6 inline-flex rounded-3xl bg-violet-500 px-6 py-3 text-sm font-semibold text-white hover:bg-violet-400 transition">
                        Tambah Official Pertama
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        const officialSearch = document.getElementById('officialSearch');
        const roleFilter = document.getElementById('roleFilter');
        const officialCards = Array.from(document.querySelectorAll('[data-name][data-role]'));
        const noResults = document.createElement('div');
        noResults.className = 'rounded-[2rem] border border-slate-800 bg-slate-900/95 p-12 text-center text-slate-400 shadow-2xl shadow-slate-950/40 col-span-full';
        noResults.innerHTML = '<p class="text-lg font-semibold text-white">Tidak ada official yang cocok.</p><p class="mt-2 text-sm">Ubah kata kunci pencarian atau filter jabatan Anda.</p>';

        function updateOfficialVisibility() {
            const searchTerm = officialSearch.value.trim().toLowerCase();
            const selectedRole = roleFilter.value.toLowerCase();
            let visibleCount = 0;

            officialCards.forEach(card => {
                const name = card.dataset.name || '';
                const role = card.dataset.role || '';
                const matchesSearch = !searchTerm || name.includes(searchTerm) || role.includes(searchTerm);
                const matchesRole = !selectedRole || role === selectedRole;

                if (matchesSearch && matchesRole) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            const listContainer = document.querySelector('.grid.gap-4.sm\:grid-cols-2.xl\:grid-cols-3');
            const existingNoResults = listContainer.querySelector('.no-results');

            if (visibleCount === 0) {
                if (!existingNoResults) {
                    noResults.classList.add('no-results');
                    listContainer.appendChild(noResults);
                }
            } else if (existingNoResults) {
                existingNoResults.remove();
            }
        }

        if (officialSearch && roleFilter) {
            officialSearch.addEventListener('input', updateOfficialVisibility);
            roleFilter.addEventListener('change', updateOfficialVisibility);
        }
    </script>
@endsection
