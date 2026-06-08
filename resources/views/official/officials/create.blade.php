@extends('official.layouts.app')

@section('title', 'Tambah Official')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-violet-300">Tambah Official</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Tambah Official Tim</h1>
                <p class="mt-2 text-sm text-slate-400">Isi data official untuk tim Anda dengan aturan role yang sesuai.</p>
            </div>
            <a href="{{ route('official.officials.index') }}" class="rounded-2xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-200 hover:border-violet-400 hover:text-white transition">
                Kembali ke Daftar Official
            </a>
        </div>

        <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-8 shadow-2xl shadow-slate-950/40">
            @if($tournamentTeams->isEmpty())
                <div class="rounded-[2rem] border border-slate-800 bg-slate-950 p-8 text-center shadow-2xl shadow-slate-950/40">
                    <p class="text-lg font-semibold text-white">Tim belum terdaftar pada turnamen mana pun.</p>
                    <p class="mt-2 text-sm text-slate-400">Pastikan tim sudah terdaftar di turnamen sebelum menambahkan official.</p>
                </div>
            @else
                <form action="{{ route('official.officials.store') }}" method="POST" class="space-y-6">
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
                            <select name="tournament_team_id" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none">
                                @foreach($tournamentTeams as $tt)
                                    <option value="{{ $tt->id }}" {{ old('tournament_team_id') == $tt->id ? 'selected' : '' }}>{{ $tt->tournament->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Nama Official</label>
                        <input name="official_name" value="{{ old('official_name') }}" placeholder="Budi Santoso" required maxlength="255" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Role</label>
                        <select id="roleSelection" name="role_selection" required class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none">
                            <option value="">-- Pilih Role --</option>
                            <option value="Manager" {{ old('role_selection') === 'Manager' ? 'selected' : '' }}>Manager</option>
                            <option value="Coach" {{ old('role_selection') === 'Coach' ? 'selected' : '' }}>Coach</option>
                            <option value="Assistant Coach" {{ old('role_selection') === 'Assistant Coach' ? 'selected' : '' }}>Assistant Coach</option>
                            <option value="Lainnya" {{ old('role_selection') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-800 bg-slate-950/70 p-4 text-sm text-slate-300">
                    <p class="text-sm font-semibold text-white">Batas Official</p>
                    <ul class="mt-3 space-y-2">
                        <li>Manager: maks 1</li>
                        <li>Coach: maks 1</li>
                        <li>Assistant Coach: maks 2</li>
                        <li>Total Official: maks 7</li>
                    </ul>
                </div>

                <div id="customRoleWrapper" class="hidden">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Nama Jabatan</label>
                    <input id="customRole" name="custom_role" value="{{ old('custom_role') }}" placeholder="Fisioterapis / Kitman / Dokter Tim" maxlength="255" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Nomor Telepon</label>
                        <input name="contact_phone" value="{{ old('contact_phone') }}" placeholder="081234567890" maxlength="50" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                        <input name="contact_email" type="email" value="{{ old('contact_email') }}" placeholder="official@domain.com" maxlength="255" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" />
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('official.officials.index') }}" class="rounded-2xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-200 hover:border-violet-400 hover:text-white transition text-center">
                        Batalkan
                    </a>
                    <button type="submit" class="rounded-2xl bg-violet-500 px-5 py-3 text-sm font-semibold text-white hover:bg-violet-400 transition">
                        Simpan Official
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>

    <script>
        const roleSelection = document.getElementById('roleSelection');
        const customRoleWrapper = document.getElementById('customRoleWrapper');
        const customRoleInput = document.getElementById('customRole');

        function updateCustomRoleVisibility() {
            if (!roleSelection) return;

            if (roleSelection.value === 'Lainnya') {
                customRoleWrapper.classList.remove('hidden');
                customRoleInput.required = true;
            } else {
                customRoleWrapper.classList.add('hidden');
                customRoleInput.required = false;
                customRoleInput.value = '';
            }
        }

        roleSelection?.addEventListener('change', updateCustomRoleVisibility);
        updateCustomRoleVisibility();
    </script>
@endsection
