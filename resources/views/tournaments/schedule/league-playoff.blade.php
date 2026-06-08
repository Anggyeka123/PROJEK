@extends('tournaments.schedule.partials.base', ['tournament' => $tournament])

@section('page-title', 'Kelola Jadwal & Skor Liga Play Off')
@section('page-subtitle', 'Atur jadwal pertandingan fase liga dan fase playoff (promosi/degradasi)')

@section('content')
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-900/20 border border-green-500/30 rounded-lg text-green-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @include('tournaments.schedule.partials.match-table', [
        'matches' => $matches,
        'tabs' => [
            ['key' => 'all', 'label' => 'Semua Laga'],
            ['key' => 'group', 'label' => 'Penyisihan Grup'],
            ['key' => 'playoff', 'label' => 'Fase Gugur'],
        ],
        'selectedTab' => $selectedTab,
        'emptyMessage' => 'Tidak ada jadwal Liga Play Off yang tersedia.',
        'showActions' => true,
    ])
@endsection
