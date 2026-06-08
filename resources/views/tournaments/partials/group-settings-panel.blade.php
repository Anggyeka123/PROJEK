<div id="groupSettingsPanel" class="p-4 sm:p-6 max-w-4xl">
    @php
        $isLocked = $tournament->groupSetting->locked ?? false;
        $selectedCompetitionType = old('competition_type', $competitionType ?? 'tournament');
        $isTournamentSelected = $selectedCompetitionType === 'tournament';
        $isLeagueSelected = $selectedCompetitionType === 'league';
        $isLeaguePlayoffSelected = $selectedCompetitionType === 'league_playoff';
        $settingPlayoffOptions = isset($bracketSetting) ? ($bracketSetting->value['playoff_options'] ?? []) : [];
        if (is_array($settingPlayoffOptions)) {
            if (in_array('promotion', $settingPlayoffOptions) && in_array('relegation', $settingPlayoffOptions)) {
                $selectedPlayoffType = old('playoff_type', 'both');
            } elseif (in_array('promotion', $settingPlayoffOptions)) {
                $selectedPlayoffType = old('playoff_type', 'promotion');
            } elseif (in_array('relegation', $settingPlayoffOptions)) {
                $selectedPlayoffType = old('playoff_type', 'relegation');
            } else {
                $selectedPlayoffType = old('playoff_type', '');
            }
        } else {
            $selectedPlayoffType = old('playoff_type', '');
        }
    @endphp
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-900/20 border border-green-500/30 rounded-lg text-green-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-900/20 border border-red-500/30 rounded-lg text-red-400 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($isLocked)
        <div class="mb-6 p-4 bg-amber-900/20 border border-amber-500/30 rounded-lg text-amber-200 text-sm">
            Pengaturan grup sudah disimpan dan dikunci. Jika ingin mengubah, tekan tombol <strong>Reset</strong>.
        </div>
    @endif

    <form action="{{ route('tournaments.updateSettings', $tournament) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
            <label class="block text-sm font-semibold text-white mb-4">
                <span class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7-4a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Sistem Kompetisi
                </span>
            </label>
            <p class="text-slate-400 text-sm mb-4">Pilih sistem kompetisi yang akan digunakan untuk turnamen ini</p>

            <div class="space-y-3">
                <label class="flex items-center gap-3 p-4 rounded-lg cursor-pointer transition {{ $isTournamentSelected ? 'bg-slate-700 shadow-sm' : 'bg-slate-800 hover:bg-slate-750' }}">
                    <input type="radio" name="competition_type" value="tournament" class="w-4 h-4 text-purple-600" {{ $selectedCompetitionType === 'tournament' ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                    <span class="font-medium {{ $isTournamentSelected ? 'text-white' : 'text-slate-400' }}">Sistem Turnamen (Bracket)</span>
                    <span class="ml-auto text-xs {{ $isTournamentSelected ? 'text-slate-300' : 'text-slate-500' }}">Pertandingan gugur hingga ada juara</span>
                </label>

                <label class="flex items-center gap-3 p-4 rounded-lg cursor-pointer transition {{ $isLeagueSelected ? 'bg-slate-700 shadow-sm' : 'bg-slate-800 hover:bg-slate-750' }}">
                    <input type="radio" name="competition_type" value="league" class="w-4 h-4 text-purple-600" {{ $selectedCompetitionType === 'league' ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                    <span class="font-medium {{ $isLeagueSelected ? 'text-white' : 'text-slate-400' }}">Sistem Liga</span>
                    <span class="ml-auto text-xs {{ $isLeagueSelected ? 'text-slate-300' : 'text-slate-500' }}">Setiap tim bermain dengan semua tim lainnya</span>
                </label>

                <label class="flex items-center gap-3 p-4 rounded-lg cursor-pointer transition {{ $isLeaguePlayoffSelected ? 'bg-slate-700 shadow-sm' : 'bg-slate-800 hover:bg-slate-750' }}">
                    <input type="radio" name="competition_type" value="league_playoff" class="w-4 h-4 text-purple-600" {{ $selectedCompetitionType === 'league_playoff' ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                    <span class="font-medium {{ $isLeaguePlayoffSelected ? 'text-white' : 'text-slate-400' }}>Sistem Liga - Play Off</span>
                    <span class="ml-auto text-xs {{ $isLeaguePlayoffSelected ? 'text-slate-300' : 'text-slate-500' }}>Liga reguler dengan babak play off tambahan</span>
                </label>
            </div>
        </div>

        <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <label class="block text-sm font-semibold text-white mb-4">
                        <span class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 19H9a6 6 0 016-6h.01M15 19h4a2 2 0 002-2v-5a6 6 0 00-6-6h-4a6 6 0 00-6 6v5a2 2 0 002 2h4m-12 0a2 2 0 012-2h8a2 2 0 012 2"></path>
                            </svg>
                            Jumlah Tim Per Grup
                        </span>
                    </label>
                    <p class="text-slate-400 text-sm mb-4">Tentukan berapa banyak tim yang berada dalam satu grup pertandingan (minimum 2)</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 w-full lg:w-auto">
                    <div class="flex flex-col">
                        <label class="text-xs text-slate-400 mb-2">Tim per grup</label>
                        <input type="number" name="teams_per_group" id="teamsPerGroup"
                            value="{{ old('teams_per_group', $tournament->groupSetting->teams_per_group) }}"
                            id="teamsPerGroup" data-min-tournament="2" data-min-league="3"
                            min="2" max="20"
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-400 focus:border-indigo-500 focus:outline-none transition"
                            onchange="updateQualifiedTeams()"
                            {{ $isLocked ? 'disabled' : '' }}>
                        <p class="text-xs text-slate-500 mt-1" id="teamsPerGroupHint">Minimum: 2 tim</p>
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs text-slate-400 mb-2">Jumlah Grup</label>
                        <input type="number" name="group_count" id="groupCount"
                            value="{{ old('group_count', $tournament->groupSetting->group_count ?? 4) }}"
                            min="1" max="32"
                            data-locked="{{ $isLocked ? '1' : '0' }}"
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-400 focus:border-indigo-500 focus:outline-none transition"
                            {{ $isLocked ? 'disabled' : '' }}
                            @if($competitionType === 'league' && ! $isLocked) readonly @endif>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <button type="button" onclick="updateQualifiedTeams()" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition" {{ $isLocked ? 'disabled' : '' }}>
                    Update
                </button>
                <div id="teamCountDisplay" class="text-sm text-slate-400"></div>
            </div>
        </div>

        <div id="playoffTypeCard" class="bg-slate-900 rounded-xl border border-slate-800 p-6 mt-4 transition duration-200 {{ $isLeaguePlayoffSelected ? '' : 'hidden' }}">
            <label class="block text-sm font-semibold text-white mb-4">
                <span class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7M12 19a7 7 0 100-14 7 7 0 000 14z"></path>
                    </svg>
                    Tipe Playoff
                </span>
            </label>
            <p class="text-slate-400 text-sm mb-4">Pilih tipe playoff yang akan digunakan ketika menggunakan Sistem Liga - Play Off. Jika kedua opsi dipilih, artinya ada Playoff Promosi & Degradasi.</p>

            <div class="grid gap-3 sm:grid-cols-2">
                <label class="flex items-center gap-3 p-3 bg-slate-800 rounded-lg cursor-pointer hover:bg-slate-700 transition">
                    <input type="radio" name="playoff_type" value="promotion" class="w-4 h-4 text-emerald-400" {{ $selectedPlayoffType === 'promotion' ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                    <span class="text-slate-200 font-medium">Play Off Promosi</span>
                </label>

                <label class="flex items-center gap-3 p-3 bg-slate-800 rounded-lg cursor-pointer hover:bg-slate-700 transition">
                    <input type="radio" name="playoff_type" value="relegation" class="w-4 h-4 text-red-400" {{ $selectedPlayoffType === 'relegation' ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                    <span class="text-slate-200 font-medium">Play Off Degradasi</span>
                </label>

                <label class="flex items-center gap-3 p-3 bg-slate-800 rounded-lg cursor-pointer hover:bg-slate-700 transition col-span-full">
                    <input type="radio" name="playoff_type" value="both" class="w-4 h-4 text-blue-400" {{ $selectedPlayoffType === 'both' ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                    <span class="text-slate-200 font-medium">Play Off Promosi & Degradasi</span>
                </label>
            </div>
        </div>

        <div id="qualifiedCard" class="bg-slate-900 rounded-xl border border-slate-800 p-6">
            <label class="block text-sm font-semibold text-white mb-4">
                <span class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span id="qualifiedCardTitle">Tim yang Lolos dari Grup</span>
                </span>
            </label>
            <p class="text-slate-400 text-sm mb-4" id="qualifiedCardDesc">Pilih ranking tim mana saja yang akan lolos dari grup (dapat dipilih lebih dari satu)</p>

            <div id="qualifiedTeamsContainer" class="space-y-2"></div>

            <div id="selectedInfoQualified" class="mt-4 p-3 bg-indigo-600/20 border border-indigo-500/30 rounded-lg">
                <p class="text-sm text-indigo-200">
                    <strong id="selectedInfoLabelQualified">Tim Terpilih:</strong> <span id="selectedTeamsQualified">Belum ada pilihan</span>
                </p>
            </div>
        </div>

        <div id="relegatedCard" class="hidden bg-slate-900 rounded-xl border border-slate-800 p-6">
            <label class="block text-sm font-semibold text-white mb-4">
                <span class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span id="relegatedCardTitle">Tim Degradasi</span>
                </span>
            </label>
            <p class="text-slate-400 text-sm mb-4" id="relegatedCardDesc">Pilih ranking tim mana saja yang akan degradasi saat menggunakan sistem liga.</p>

            <div id="relegatedTeamsContainer" class="space-y-2"></div>

            <div id="selectedInfoRelegated" class="mt-4 p-3 bg-indigo-600/20 border border-indigo-500/30 rounded-lg">
                <p class="text-sm text-indigo-200">
                    <strong id="selectedInfoLabelRelegated">Tim Degradasi:</strong> <span id="selectedTeamsRelegated">Belum ada pilihan</span>
                </p>
            </div>
        </div>

        <div class="bg-indigo-900/20 border border-indigo-500/30 rounded-lg p-4">
            <p class="text-sm text-indigo-200">
                <strong>💡 Info:</strong> Pilih jumlah tim per grup dengan bebas (minimal 2, maksimal 20), kemudian pilih ranking tim mana saja yang akan lolos. Klik tombol "Simpan Perubahan" untuk menyimpan ke database.
            </p>
        </div>

        @if($tournament->groupSetting)
            <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
                <h3 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Pengaturan Saat Ini
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div class="p-3 bg-slate-800 rounded-lg">
                        <p class="text-xs text-slate-400">Tim Per Grup</p>
                        <p class="text-lg font-bold text-indigo-400">{{ $tournament->groupSetting->teams_per_group }} Tim</p>
                    </div>
                    <div class="p-3 bg-slate-800 rounded-lg">
                        <p class="text-xs text-slate-400">Jumlah Grup</p>
                        <p class="text-lg font-bold text-indigo-400">{{ $tournament->groupSetting->group_count ?? 0 }} Grup</p>
                    </div>
                    <div class="p-3 bg-slate-800 rounded-lg">
                        <p class="text-xs text-slate-400">Total Peserta</p>
                        <p class="text-lg font-bold text-indigo-400">{{ ($tournament->groupSetting->teams_per_group ?? 0) * ($tournament->groupSetting->group_count ?? 0) }} Tim</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div class="p-3 bg-slate-800 rounded-lg">
                        <p class="text-xs text-slate-400">Tim Yang Lolos per Grup</p>
                        <p class="text-lg font-bold text-green-400">{{ count($tournament->groupSetting->qualified_teams) }} Tim</p>
                    </div>
                    <div class="p-3 bg-slate-800 rounded-lg">
                        <p class="text-xs text-slate-400">Total Tim Lolos</p>
                        <p class="text-lg font-bold text-green-400">{{ ($tournament->groupSetting->group_count ?? 0) * count($tournament->groupSetting->qualified_teams) }} Tim</p>
                    </div>
                    <div class="p-3 bg-slate-800 rounded-lg">
                        <p class="text-xs text-slate-400">Ranking yang Lolos</p>
                        <p class="text-lg font-bold text-indigo-400">{{ implode(', ', array_map(fn($r) => 'Ranking ' . $r, $tournament->groupSetting->qualified_teams)) }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 pt-4">
            <a href="{{ route('tournaments.manage', $tournament) }}" class="flex-1 text-center py-3 px-6 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-lg transition">
                Batal
            </a>

            @if($tournament->groupSetting)
                <button type="button" onclick="submitResetForm()" class="flex-1 py-3 px-6 bg-red-600/20 hover:bg-red-600/40 text-red-400 font-semibold rounded-lg transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Reset
                </button>
            @endif

            <button type="submit" class="flex-1 py-3 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition flex items-center justify-center gap-2" {{ $isLocked ? 'disabled' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>

    @if($tournament->groupSetting)
        <form id="resetGroupSettings" action="{{ route('tournaments.resetSettings', $tournament) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>

<script>
    window.addEventListener('DOMContentLoaded', function() {
        updateQualifiedTeams();
        updateSelectedInfo();
        updateCompetitionTypeInfo();
        updatePlayoffTypeCardVisibility();
        updateQualifiedTeamsCardLabel();
    });

    const currentQualified = @json($tournament->groupSetting->qualified_teams ?? []);
    const currentRelegated = @json($tournament->groupSetting->relegated_teams ?? []);
    const submittedQualified = @json(old('qualified_teams', []));
    const submittedRelegated = @json(old('relegated_teams', []));
    const isLocked = {{ $isLocked ? 'true' : 'false' }};

    function updateQualifiedTeams() {
        const selectedType = document.querySelector('input[name="competition_type"]:checked').value;
        const isLeague = selectedType === 'league';
        const isLeaguePlayoff = selectedType === 'league_playoff';
        const teamsPerGroupInput = document.getElementById('teamsPerGroup');
        const minValue = (isLeague || isLeaguePlayoff) ? 3 : 2;
        
        // Update min attribute dan hint text
        teamsPerGroupInput.min = minValue;
        document.getElementById('teamsPerGroupHint').textContent = `Minimum: ${minValue} tim`;
        
        let teamsPerGroup = parseInt(teamsPerGroupInput.value) || minValue;

        // Validasi minimum berdasarkan tipe kompetisi
        if ((isLeague || isLeaguePlayoff) && teamsPerGroup < 3) {
            teamsPerGroup = 3;
            teamsPerGroupInput.value = 3;
        } else if (!isLeague && !isLeaguePlayoff && teamsPerGroup < 2) {
            teamsPerGroup = 2;
            teamsPerGroupInput.value = 2;
        }

        if (teamsPerGroup > 20) {
            alert('Jumlah tim maksimal 20');
            teamsPerGroupInput.value = 20;
            return;
        }

        // For league_playoff, determine which container to use based on playoff_type
        const playoffType = isLeaguePlayoff ? (document.querySelector('input[name="playoff_type"]:checked')?.value || '') : '';
        let useQualified = !isLeague && !isLeaguePlayoff; // Tournament uses qualified
        let useRelegated = isLeague; // League uses relegated

        if (isLeaguePlayoff) {
            useQualified = playoffType === 'promotion' || playoffType === 'both';
            useRelegated = playoffType === 'relegation' || playoffType === 'both';
        }

        // Update qualified teams container (if needed)
        if (useQualified) {
            const container = document.getElementById('qualifiedTeamsContainer');
            const teams = [];
            // Qualified order (ascending)
            for (let i = 1; i <= teamsPerGroup; i++) {
                teams.push(i);
            }

            const currentSelected = currentQualified;
            const submittedSelected = submittedQualified;
            const selectedValues = submittedSelected.length > 0 ? submittedSelected : currentSelected;

            container.innerHTML = '';

            teams.forEach(i => {
                let isChecked = (selectedValues.includes(i) || selectedValues.includes(String(i)));
                let badge = '';

                if (!isLeague && !isLeaguePlayoff) {
                    if (i === 1) badge = '<span class="ml-auto text-xs bg-yellow-600/30 text-yellow-300 px-2 py-1 rounded">Juara</span>';
                    else if (i === 2) badge = '<span class="ml-auto text-xs bg-gray-600/30 text-gray-300 px-2 py-1 rounded">Runner-up</span>';
                    else if (i === 3) badge = '<span class="ml-auto text-xs bg-orange-600/30 text-orange-300 px-2 py-1 rounded">Ketiga</span>';
                }

                const html = `
                    <label class="flex items-center gap-3 p-3 bg-slate-800 rounded-lg cursor-pointer hover:bg-slate-700 transition">
                        <input type="checkbox" name="qualified_teams[]" value="${i}" ${isChecked ? 'checked' : ''}
                            class="w-4 h-4 text-indigo-600 rounded" onchange="handleTeamSelection(this, 'qualified')" ${isLocked ? 'disabled' : ''}>
                        <span class="text-white font-medium">Ranking ${i}</span>
                        ${badge}
                    </label>
                `;

                container.innerHTML += html;
            });

            document.querySelectorAll('input[name="qualified_teams[]"]').forEach(checkbox => {
                if (parseInt(checkbox.value) > teamsPerGroup) {
                    checkbox.checked = false;
                }
            });
        }

        // Update relegated teams container (if needed)
        if (useRelegated) {
            const container = document.getElementById('relegatedTeamsContainer');
            const teams = [];
            // Relegated order (descending)
            for (let i = teamsPerGroup; i >= 1; i--) {
                teams.push(i);
            }

            const currentSelected = currentRelegated;
            const submittedSelected = submittedRelegated;
            const selectedValues = submittedSelected.length > 0 ? submittedSelected : currentSelected;
            const isRelegationOnlyPlayoff = isLeaguePlayoff && useRelegated && !useQualified;

            container.innerHTML = '';

            teams.forEach(i => {
                let isChecked = (selectedValues.includes(i) || selectedValues.includes(String(i)));
                let badge = '';
                let isDisabledRanking = (isLeague && i === 1) || (isRelegationOnlyPlayoff && i === 1);

                if (isDisabledRanking) {
                    isChecked = false;
                }

                if (isLeague) {
                    // Badge untuk liga ketika ranking 1 disabled
                    if (i === 1) badge = '<span class="ml-auto text-xs bg-slate-600/30 text-slate-400 px-2 py-1 rounded">Tidak bisa dipilih</span>';
                } else if (isRelegationOnlyPlayoff && i === 1) {
                    badge = '<span class="ml-auto text-xs bg-red-600/30 text-red-200 px-2 py-1 rounded">Tidak bisa dipilih</span>';
                }

                const html = `
                    <label class="flex items-center gap-3 p-3 ${isDisabledRanking ? 'bg-slate-700/50 opacity-60' : 'bg-slate-800'} rounded-lg ${!isDisabledRanking ? 'cursor-pointer hover:bg-slate-700' : ''} transition">
                        <input type="checkbox" name="relegated_teams[]" value="${i}" ${isChecked ? 'checked' : ''}
                            class="w-4 h-4 text-indigo-600 rounded" onchange="handleTeamSelection(this, 'relegated')" ${isLocked || isDisabledRanking ? 'disabled' : ''}>
                        <span class="text-white font-medium">Ranking ${i}</span>
                        ${badge}
                    </label>
                `;

                container.innerHTML += html;
            });

            document.querySelectorAll('input[name="relegated_teams[]"]').forEach(checkbox => {
                if (parseInt(checkbox.value) > teamsPerGroup) {
                    checkbox.checked = false;
                }
            });
        }

        updateSelectedInfo();
    }

    function updateSelectedInfo() {
        const selectedType = document.querySelector('input[name="competition_type"]:checked').value;
        const isLeaguePlayoff = selectedType === 'league_playoff';
        const playoffType = isLeaguePlayoff ? (document.querySelector('input[name="playoff_type"]:checked')?.value || '') : '';
        
        // Determine which field to check
        let useRelegated = selectedType === 'league';
        if (isLeaguePlayoff) {
            useRelegated = playoffType === 'relegation' || playoffType === 'both';
        }
        
        const name = useRelegated ? 'relegated_teams[]' : 'qualified_teams[]';
        const checkboxes = document.querySelectorAll(`input[name="${name}"]:checked`);
        const selected = Array.from(checkboxes)
            .map(cb => 'Ranking ' + cb.value)
            .sort((a, b) => parseInt(a.split(' ')[1]) - parseInt(b.split(' ')[1]))
            .join(', ');

        const teamsPerGroup = parseInt(document.getElementById('teamsPerGroup').value) || 2;
        document.getElementById('teamCountDisplay').textContent = 
            `Tim per grup: ${teamsPerGroup} tim`;

        if (useRelegated) {
            document.getElementById('selectedTeamsRelegated').textContent = selected || 'Belum ada pilihan';
            const selectedInfo = document.getElementById('selectedInfoRelegated');
            if (checkboxes.length === 0) {
                selectedInfo.classList.add('border-red-500/30', 'bg-red-600/20');
                selectedInfo.classList.remove('border-indigo-500/30', 'bg-indigo-600/20');
                selectedInfo.querySelector('p').classList.add('text-red-200');
            } else {
                selectedInfo.classList.remove('border-red-500/30', 'bg-red-600/20');
                selectedInfo.classList.add('border-indigo-500/30', 'bg-indigo-600/20');
                selectedInfo.querySelector('p').classList.remove('text-red-200');
            }
        } else {
            document.getElementById('selectedTeamsQualified').textContent = selected || 'Belum ada pilihan';
            const selectedInfo = document.getElementById('selectedInfoQualified');
            if (checkboxes.length === 0) {
                selectedInfo.classList.add('border-red-500/30', 'bg-red-600/20');
                selectedInfo.classList.remove('border-indigo-500/30', 'bg-indigo-600/20');
                selectedInfo.querySelector('p').classList.add('text-red-200');
            } else {
                selectedInfo.classList.remove('border-red-500/30', 'bg-red-600/20');
                selectedInfo.classList.add('border-indigo-500/30', 'bg-indigo-600/20');
                selectedInfo.querySelector('p').classList.remove('text-red-200');
            }
        }
    }

    document.querySelector('form')?.addEventListener('submit', function(e) {
        const selectedType = document.querySelector('input[name="competition_type"]:checked').value;
        const isLeaguePlayoff = selectedType === 'league_playoff';
        const playoffType = isLeaguePlayoff ? (document.querySelector('input[name="playoff_type"]:checked')?.value || '') : '';
        
        let needQualified = selectedType === 'tournament' || (isLeaguePlayoff && (playoffType === 'promotion' || playoffType === 'both'));
        let needRelegated = selectedType === 'league' || (isLeaguePlayoff && (playoffType === 'relegation' || playoffType === 'both'));
        
        if (needQualified) {
            const qualifiedBoxes = document.querySelectorAll('input[name="qualified_teams[]"]:checked');
            if (qualifiedBoxes.length === 0) {
                e.preventDefault();
                const message = selectedType === 'league_playoff' 
                    ? '❌ Pilih minimal 1 ranking untuk Play Off Promosi'
                    : '❌ Pilih minimal 1 ranking tim yang akan lolos dari grup';
                alert(message);
                document.getElementById('selectedInfoQualified').classList.add('border-red-500/30', 'bg-red-600/20');
                document.getElementById('selectedInfoQualified').classList.remove('border-indigo-500/30', 'bg-indigo-600/20');
                return;
            }
        }
        
        if (needRelegated) {
            const relegatedBoxes = document.querySelectorAll('input[name="relegated_teams[]"]:checked');
            if (relegatedBoxes.length === 0) {
                e.preventDefault();
                const message = selectedType === 'league_playoff'
                    ? '❌ Pilih minimal 1 ranking untuk Play Off Degradasi'
                    : '❌ Pilih minimal 1 ranking tim yang akan degradasi dari grup';
                alert(message);
                document.getElementById('selectedInfoRelegated').classList.add('border-red-500/30', 'bg-red-600/20');
                document.getElementById('selectedInfoRelegated').classList.remove('border-indigo-500/30', 'bg-indigo-600/20');
                return;
            }
        }
    });

    function submitResetForm() {
        if (confirm('Reset pengaturan ke default? Tindakan ini tidak bisa dibatalkan.')) {
            document.getElementById('resetGroupSettings').submit();
        }
    }

    // Fungsi untuk menangani seleksi tim agar tidak bisa dipilih di kedua card sekaligus
    function handleTeamSelection(checkbox, type) {
        const value = checkbox.value;
        const isChecked = checkbox.checked;

        if (isChecked) {
            if (type === 'qualified') {
                // Jika checkbox Promosi dicentang, uncheck ranking yang sama di Degradasi
                const relegatedCheckbox = document.querySelector(`input[name="relegated_teams[]"][value="${value}"]`);
                if (relegatedCheckbox) {
                    relegatedCheckbox.checked = false;
                }
            } else if (type === 'relegated') {
                // Jika checkbox Degradasi dicentang, uncheck ranking yang sama di Promosi
                const qualifiedCheckbox = document.querySelector(`input[name="qualified_teams[]"][value="${value}"]`);
                if (qualifiedCheckbox) {
                    qualifiedCheckbox.checked = false;
                }
            }
        }

        updateSelectedInfo();
    }

    // Event listener untuk perubahan teams_per_group (real-time saat input)
    const teamsPerGroupInput = document.getElementById('teamsPerGroup');
    if (teamsPerGroupInput) {
        teamsPerGroupInput.addEventListener('input', function() {
            updateQualifiedTeams();
        });
        teamsPerGroupInput.addEventListener('change', function() {
            updateQualifiedTeams();
        });
    }

    // Event listener untuk perubahan competition_type
    document.querySelectorAll('input[name="competition_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateCompetitionTypeInfo();
            updateQualifiedTeams();
            updatePlayoffTypeCardVisibility();
        });
    });

    // Event listener untuk perubahan playoff_type
    document.querySelectorAll('input[name="playoff_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateQualifiedTeamsCardLabel();
            updateQualifiedTeams();
            updateCompetitionTypeInfo();
        });
    });

    function updatePlayoffTypeCardVisibility() {
        const selectedType = document.querySelector('input[name="competition_type"]:checked').value;
        const block = document.getElementById('playoffTypeCard');
        if (!block) return;

        if (selectedType === 'league_playoff') {
            block.classList.remove('hidden');
        } else {
            block.classList.add('hidden');
        }
        updateQualifiedTeamsCardLabel();
    }

    function updateQualifiedTeamsCardLabel() {
        const selectedType = document.querySelector('input[name="competition_type"]:checked').value;
        const playoffType = document.querySelector('input[name="playoff_type"]:checked')?.value || '';
        const qualifiedCard = document.getElementById('qualifiedCard');
        const relegatedCard = document.getElementById('relegatedCard');
        const titleEl = document.getElementById('qualifiedCardTitle');
        const descEl = document.getElementById('qualifiedCardDesc');
        const relegTitleEl = document.getElementById('relegatedCardTitle');
        const relegDescEl = document.getElementById('relegatedCardDesc');

        if (!qualifiedCard || !relegatedCard) return;

        // Qualified card: tournament OR (league_playoff + promotion/both)
        const showQualified = selectedType === 'tournament' || (selectedType === 'league_playoff' && (playoffType === 'promotion' || playoffType === 'both'));
        // Relegated card: league OR (league_playoff + relegation/both)
        const showRelegated = selectedType === 'league' || (selectedType === 'league_playoff' && (playoffType === 'relegation' || playoffType === 'both'));

        if (showQualified) {
            qualifiedCard.classList.remove('hidden');
            if (selectedType === 'league_playoff') {
                titleEl.textContent = 'Tim Play Off Promosi';
                descEl.textContent = 'Pilih ranking tim mana saja yang akan mengikuti Play Off Promosi';
            } else {
                titleEl.textContent = 'Tim yang Lolos dari Grup';
                descEl.textContent = 'Pilih ranking tim mana saja yang akan lolos dari grup (dapat dipilih lebih dari satu)';
            }
        } else {
            qualifiedCard.classList.add('hidden');
        }

        if (showRelegated) {
            relegatedCard.classList.remove('hidden');
            if (selectedType === 'league_playoff') {
                relegTitleEl.textContent = 'Tim Play Off Degradasi';
                relegDescEl.textContent = 'Pilih ranking tim mana saja yang akan mengikuti Play Off Degradasi';
            } else {
                relegTitleEl.textContent = 'Tim Degradasi';
                relegDescEl.textContent = 'Pilih ranking tim mana saja yang akan degradasi saat menggunakan sistem liga.';
            }
        } else {
            relegatedCard.classList.add('hidden');
        }
    }

    function updateCompetitionTypeInfo() {
        const selectedType = document.querySelector('input[name="competition_type"]:checked').value;
        let infoBox = document.getElementById('competitionTypeInfo');
        const groupCount = document.getElementById('groupCount');
        const isGloballyLocked = groupCount.dataset.locked === '1';
        
        if (!infoBox) {
            infoBox = document.createElement('div');
            infoBox.id = 'competitionTypeInfo';
            infoBox.className = 'mt-4 p-4 rounded-lg border text-sm flex items-start gap-3';
            document.querySelector('input[name="competition_type"]').closest('.space-y-3').parentElement.appendChild(infoBox);
        }

        const isLeagueSelected = selectedType === 'league';
        const isLeaguePlayoffSelected = selectedType === 'league_playoff';
        const playoffType = isLeaguePlayoffSelected ? (document.querySelector('input[name="playoff_type"]:checked')?.value || '') : '';
        const bracketActiveForLeaguePlayoff = isLeaguePlayoffSelected && (playoffType === 'promotion' || playoffType === 'relegation' || playoffType === 'both');

        if (isLeagueSelected || isLeaguePlayoffSelected) {
            groupCount.value = '1';
            if (!isGloballyLocked) {
                groupCount.readOnly = true;
                groupCount.classList.add('cursor-not-allowed', 'opacity-70');
            }
        } else if (!isGloballyLocked) {
            groupCount.readOnly = false;
            groupCount.classList.remove('cursor-not-allowed', 'opacity-70');
        }

        if (selectedType === 'tournament' || bracketActiveForLeaguePlayoff) {
            const systemLabel = selectedType === 'tournament' ? 'turnamen' : 'liga-playoff';
            infoBox.className = 'mt-4 p-4 bg-violet-900/20 border border-violet-500/30 text-violet-200 rounded-lg text-sm flex items-start gap-3';
            infoBox.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 7a1 1 0 000 2h6a1 1 0 000-2H8zm0 4a1 1 0 000 2h3a1 1 0 000-2H8z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <strong class="block mb-1">✓ Bracket Gugur Aktif</strong>
                    <p>Dengan sistem ${systemLabel}, fitur bracket gugur akan tersedia dan dapat diakses di halaman manajemen turnamen.</p>
                </div>
            `;
        } else if (isLeaguePlayoffSelected) {
            infoBox.className = 'mt-4 p-4 bg-amber-900/20 border border-amber-500/30 text-amber-200 rounded-lg text-sm flex items-start gap-3';
            infoBox.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <strong class="block mb-1">✓ Bracket Gugur Aktif</strong>
                    <p>Untuk sistem liga-playoff, bracket gugur tersedia untuk Play Off Promosi, Play Off Degradasi, dan Play Off Promosi & Degradasi.</p>
                </div>
            `;
        } else {
            infoBox.className = 'mt-4 p-4 bg-amber-900/20 border border-amber-500/30 text-amber-200 rounded-lg text-sm flex items-start gap-3';
            infoBox.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <strong class="block mb-1">⊘ Bracket Gugur Tidak Tersedia</strong>
                    <p>Sistem liga biasa tidak menggunakan bracket gugur. Jumlah grup otomatis diset ke 1 dan tidak dapat diubah.</p>
                </div>
            `;
        }
    }

    // Initialize competition type info on page load
    window.addEventListener('DOMContentLoaded', function() {
        updateCompetitionTypeInfo();
    });
</script>
