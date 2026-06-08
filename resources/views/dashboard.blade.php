<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Dashboard Futsal League</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-xl font-semibold mb-4">Turnamen Aktif</h2>
            @forelse($tournaments as $t)
                <div class="p-4 border-b">{{ $t['name'] ?? 'Turnamen Tanpa Nama' }}</div>
            @empty
                <p class="text-gray-400 italic">Belum ada data turnamen.</p>
            @endforelse
        </div>

        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-xl font-semibold mb-4">Tim Terdaftar</h2>
            @forelse($teams as $team)
                <div class="p-4 border-b">{{ $team['name'] ?? 'Tim Tanpa Nama' }}</div>
            @empty
                <p class="text-gray-400 italic">Belum ada tim terdaftar.</p>
            @endforelse
        </div>
    </div>
</div>