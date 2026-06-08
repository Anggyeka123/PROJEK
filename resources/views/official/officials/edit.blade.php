@extends('official.layouts.app')

@section('title', 'Edit Official')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-violet-300">Edit Official</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Ubah Data Official</h1>
                <p class="mt-2 text-sm text-slate-400">Perbarui data official tim Anda dengan aturan role yang berlaku.</p>
            </div>
            <a href="{{ route('official.officials.index') }}" class="rounded-2xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-200 hover:border-violet-400 hover:text-white transition">
                Kembali ke Daftar Official
            </a>
        </div>

        <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-8 shadow-2xl shadow-slate-950/40">
            <form action="{{ route('official.officials.update', $official) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

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
                    <input type="text" disabled class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-400 cursor-not-allowed" value="{{ $tournamentTeam?->tournament?->name ?? 'Tidak ada turnamen' }}" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Nama Official</label>
                        <input name="official_name" value="{{ old('official_name', $official->official_name) }}" placeholder="Budi Santoso" required maxlength="255" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Role</label>
                        <select id="roleSelection" name="role_selection" required class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none">
                            @php
                                $currentRole = old('role_selection', in_array($official->role, ['Manager', 'Coach', 'Assistant Coach']) ? $official->role : 'Lainnya');
                            @endphp
                            <option value="">-- Pilih Role --</option>
                            <option value="Manager" {{ $currentRole === 'Manager' ? 'selected' : '' }}>Manager</option>
                            <option value="Coach" {{ $currentRole === 'Coach' ? 'selected' : '' }}>Coach</option>
                            <option value="Assistant Coach" {{ $currentRole === 'Assistant Coach' ? 'selected' : '' }}>Assistant Coach</option>
                            <option value="Lainnya" {{ $currentRole === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
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

                <div id="customRoleWrapper" class="{{ $currentRole === 'Lainnya' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Nama Jabatan</label>
                    <input id="customRole" name="custom_role" value="{{ old('custom_role', $currentRole === 'Lainnya' ? $official->role : '') }}" placeholder="Fisioterapis / Kitman / Dokter Tim" maxlength="255" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Nomor Telepon</label>
                        <input name="contact_phone" value="{{ old('contact_phone', $official->contact_phone) }}" placeholder="081234567890" maxlength="50" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                        <input name="contact_email" type="email" value="{{ old('contact_email', $official->contact_email) }}" placeholder="official@domain.com" maxlength="255" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:border-violet-400 focus:outline-none" />
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('official.officials.index') }}" class="rounded-2xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-200 hover:border-violet-400 hover:text-white transition text-center">
                        Batalkan
                    </a>
                    <button type="submit" class="rounded-2xl bg-violet-500 px-5 py-3 text-sm font-semibold text-white hover:bg-violet-400 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
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
