@extends('official.layouts.app')

@section('title', 'Klasemen')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-violet-300">Klasemen Resmi</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Klasemen Tim Official</h1>
                <p class="mt-2 text-sm text-slate-400">Lihat posisi tim Anda dalam klasemen turnamen yang Anda ikuti.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('official.dashboard') }}" class="rounded-2xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-200 hover:border-violet-400 hover:text-white transition">
                    Kembali ke Beranda
                </a>
            </div>
        </div>

        @if($standings->isEmpty())
            <div class="rounded-[2rem] border border-slate-800 bg-slate-950/95 p-10 text-center text-slate-400">
                <p class="text-xl font-semibold text-white">Belum ada data klasemen.</p>
                <p class="mt-2 text-sm">Klasemen akan muncul setelah pertandingan memiliki hasil yang valid.</p>
            </div>
        @else
            <div class="grid gap-6">
                @foreach($standings as $data)
                    <section class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-6 shadow-2xl shadow-slate-950/40">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Turnamen</p>
                                <h2 class="mt-3 text-2xl font-semibold text-white">{{ $data['tournament']->name }}</h2>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-violet-500/10 px-4 py-2 text-xs font-semibold text-violet-300">{{ count($data['groups']) }} Grup</span>
                        </div>

                        @foreach($data['groups'] as $group)
                            <div class="mt-6 rounded-[2rem] border border-slate-800 bg-slate-950/90 p-4">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Grup</p>
                                        <h3 class="text-lg font-semibold text-white">{{ $group['label'] }}</h3>
                                    </div>
                                    <p class="text-sm text-slate-400">Urutkan berdasarkan Poin, Selisih Gol, dan Gol Masuk.</p>
                                </div>

                                <div class="mt-4 overflow-x-auto">
                                    <table class="w-full min-w-[760px] text-left text-sm">
                                        <thead>
                                            <tr class="border-b border-slate-800 text-slate-400">
                                                <th class="px-4 py-3 w-12">#</th>
                                                <th class="px-4 py-3">Tim</th>
                                                <th class="px-3 py-3 text-center">M</th>
                                                <th class="px-3 py-3 text-center">W</th>
                                                <th class="px-3 py-3 text-center">D</th>
                                                <th class="px-3 py-3 text-center">L</th>
                                                <th class="px-3 py-3 text-center">GM</th>
                                                <th class="px-3 py-3 text-center">GK</th>
                                                <th class="px-3 py-3 text-center">SG</th>
                                                <th class="px-4 py-3 text-right">PTS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($group['rows'] as $row)
                                                <tr class="border-b border-slate-800 {{ $row['is_current'] ? 'bg-violet-500/10 text-white' : 'text-slate-200' }}">
                                                    <td class="px-4 py-4 font-semibold">{{ $row['position'] }}</td>
                                                    <td class="px-4 py-4">
                                                        <div class="flex items-center gap-3">
                                                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-800 text-slate-400 overflow-hidden">
                                                                @if($row['logo'])
                                                                    <img src="{{ Storage::url($row['logo']) }}" alt="Logo {{ $row['name'] }}" class="h-10 w-10 object-cover" />
                                                                @else
                                                                    <span class="text-xs uppercase">{{ strtoupper(substr($row['name'], 0, 2)) }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="min-w-0">
                                                                <p class="truncate text-sm font-semibold">{{ $row['name'] }}</p>
                                                                @if($row['is_current'])
                                                                    <p class="text-xs text-violet-300">Tim Anda</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-4 text-center">{{ $row['played'] }}</td>
                                                    <td class="px-3 py-4 text-center">{{ $row['wins'] }}</td>
                                                    <td class="px-3 py-4 text-center">{{ $row['draws'] }}</td>
                                                    <td class="px-3 py-4 text-center">{{ $row['losses'] }}</td>
                                                    <td class="px-3 py-4 text-center">{{ $row['goals_for'] }}</td>
                                                    <td class="px-3 py-4 text-center">{{ $row['goals_against'] }}</td>
                                                    <td class="px-3 py-4 text-center">{{ $row['goal_difference'] }}</td>
                                                    <td class="px-4 py-4 text-right font-semibold">{{ $row['points'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </section>
                @endforeach
            </div>
        @endif
    </div>
@endsection
