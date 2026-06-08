<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pemain | Official Tim</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-violet-300">Tambah Pemain</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Tambah Pemain Tim</h1>
                <p class="mt-2 text-sm text-slate-400">Isi data pemain sesuai dengan peraturan yang berlaku.</p>
            </div>
            <a href="{{ route('official.players.index') }}" class="rounded-2xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-200 hover:border-violet-400 hover:text-white transition">
                Kembali ke Daftar Pemain
            </a>
        </div>

        <div class="mt-8 rounded-[2rem] border border-slate-800 bg-slate-900/95 p-8 shadow-2xl shadow-slate-950/40">
            <form action="{{ route('official.players.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                @if($errors->any())
                    <div class="rounded-3xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-200">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Turnamen</label>
                    <input type="text" disabled class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-400 cursor-not-allowed" value="{{ $tournamentTeams->first()?->tournament?->name ?? 'Tidak ada turnamen' }}" />
                    <input type="hidden" name="tournament_team_id" value="{{ $tournamentTeams->first()?->id }}" />
                    @if($tournamentTeams->count() > 1)
                        <div class="mt-3 text-xs text-slate-500">
                            <p class="font-semibold">Tim Anda mengikuti {{ $tournamentTeams->count() }} turnamen. Pilih salah satu:</p>
                            <select name="tournament_team_id" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none mt-2">
                                @foreach($tournamentTeams as $tt)
                                    <option value="{{ $tt->id }}">{{ $tt->tournament->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
    <label class="block text-sm font-medium text-slate-300 mb-2">Nama Pemain *</label>
    <input name="player_name" value="{{ old('player_name') }}" placeholder="Adi Pratama" required maxlength="15" 
        class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none uppercase" 
        pattern="[A-Za-z ]+" title="Hanya huruf dan spasi (3-15 karakter)" 
        oninput="this.value = this.value.replace(/[^A-Za-z ]/g, '')" />
    <p class="mt-1 text-xs text-slate-500">Huruf dan spasi saja, 3-15 karakter</p>
</div>
                    <div>
    <label class="block text-sm font-medium text-slate-300 mb-2">Nomor Punggung *</label>
    <input name="shirt_number" type="number" value="{{ old('shirt_number') }}" required min="1" max="99" 
        class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" 
        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
        oninput="if(this.value > 99) this.value = 99; if(this.value < 0) this.value = 1;" />
    <p class="mt-1 text-xs text-slate-500">1-99 (unik dalam tim yang sama)</p>
</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-3">Posisi Bermain *</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="positions[]" value="GK" {{ in_array('GK', old('positions', [])) ? 'checked' : '' }} class="h-5 w-5 rounded text-violet-500">
                            <span class="text-sm text-slate-300">Goalkeeper (GK)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="positions[]" value="Anchor" {{ in_array('Anchor', old('positions', [])) ? 'checked' : '' }} class="h-5 w-5 rounded text-violet-500">
                            <span class="text-sm text-slate-300">Anchor</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="positions[]" value="Flank" {{ in_array('Flank', old('positions', [])) ? 'checked' : '' }} class="h-5 w-5 rounded text-violet-500">
                            <span class="text-sm text-slate-300">Flank</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="positions[]" value="Pivot" {{ in_array('Pivot', old('positions', [])) ? 'checked' : '' }} class="h-5 w-5 rounded text-violet-500">
                            <span class="text-sm text-slate-300">Pivot</span>
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Pilih minimal satu posisi</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Posisi Utama *</label>
                        <select name="dominant_position" required class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none">
                            <option value="">-- Pilih Posisi Utama --</option>
                            <option value="GK" {{ old('dominant_position') === 'GK' ? 'selected' : '' }}>Goalkeeper (GK)</option>
                            <option value="Anchor" {{ old('dominant_position') === 'Anchor' ? 'selected' : '' }}>Anchor</option>
                            <option value="Flank" {{ old('dominant_position') === 'Flank' ? 'selected' : '' }}>Flank</option>
                            <option value="Pivot" {{ old('dominant_position') === 'Pivot' ? 'selected' : '' }}>Pivot</option>
                        </select>
                    </div>
                   <div>
    <label class="block text-sm font-medium text-slate-300 mb-2">Nomor HP *</label>
    <input name="phone" type="tel" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" maxlength="15" required
        class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" 
        oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
    <p class="mt-1 text-xs text-slate-500">10-15 digit</p>
</div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
    <label class="block text-sm font-medium text-slate-300 mb-2">Tempat Lahir *</label>
    <input name="birth_place" type="text" value="{{ old('birth_place', '') }}" placeholder="Kota / Tempat Lahir" required
        class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" 
        oninput="this.value = this.value.replace(/[^A-Za-z ]/g, '')" />
</div>
    <div>
        <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal Lahir *</label>
        <input name="birth_date" type="date" value="{{ old('birth_date') }}" required
            class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" />
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-300 mb-2">Foto Pemain *</label>
    <input name="photo" type="file" accept="image/jpeg,image/png,image/webp" required
        class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" />
    <p class="mt-1 text-xs text-slate-500">JPG, PNG, WebP (Max 8 MB)</p>
</div>
                <div class="flex items-center gap-3 rounded-2xl border border-slate-700 bg-slate-950 px-4 py-4">
                    <input id="is_captain" name="is_captain" type="checkbox" value="1" class="h-5 w-5 rounded text-violet-500 focus:ring-violet-400" {{ old('is_captain') ? 'checked' : '' }}>
                    <label for="is_captain" class="text-sm text-slate-300">👑 Jadikan Kapten Tim</label>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('official.players.index') }}" class="rounded-2xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-200 hover:border-violet-400 hover:text-white transition text-center">
                        Batalkan
                    </a>
                    <button type="submit" class="rounded-2xl bg-violet-500 px-5 py-3 text-sm font-semibold text-white hover:bg-violet-400 transition">
                        Simpan Pemain
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
