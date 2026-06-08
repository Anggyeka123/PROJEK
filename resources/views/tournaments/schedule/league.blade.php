@extends('tournaments.schedule.partials.base', ['tournament' => $tournament])

@section('page-title', 'Kelola Jadwal & Skor Liga')
@section('page-subtitle', 'Atur jadwal pertandingan per grup dan masukkan skor untuk sistem kompetisi liga')

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
        ],
        'selectedTab' => $selectedTab,
        'emptyMessage' => 'Belum ada jadwal liga yang tersedia.',
        'showActions' => true,
    ])
@endsection
