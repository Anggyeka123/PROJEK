<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\MatchGenerator;

class TournamentParticipantController extends Controller
{
    public function index(Tournament $tournament)
    {
        $participants = $tournament->tournamentTeams()->with('team')->get();

        return view('tournaments.participants.index', compact('tournament', 'participants'));
    }

    public function create(Tournament $tournament)
    {
        return view('tournaments.participants.create', compact('tournament'));
    }

    public function store(Request $request, Tournament $tournament)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ];

        // Add conditional validation based on country
        if ($request->input('country') === 'Indonesia') {
            $rules['province'] = 'required|string|max:255';
        } else {
            $rules['state'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('team-logos', 'public');
        }

        $team = Team::create([
            'name' => $validated['name'],
            'slug' => $this->generateTeamSlug($validated['name']),
            'logo' => $logoPath,
            'city' => $validated['city'],
            'country' => $validated['country'],
        ]);

        TournamentTeam::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
        ]);

        // Regenerate tournament schedule/bracket after participant added
        app(MatchGenerator::class)->generateForTournament($tournament);

        return redirect()->route('tournaments.participants.index', $tournament)
            ->with('success', 'Peserta berhasil ditambahkan ke turnamen.');
    }

    public function edit(Tournament $tournament, TournamentTeam $participant)
    {
        if ($participant->tournament_id !== $tournament->id) {
            abort(404);
        }

        $participant->load('team');

        return view('tournaments.participants.edit', compact('tournament', 'participant'));
    }

    public function update(Request $request, Tournament $tournament, TournamentTeam $participant)
    {
        if ($participant->tournament_id !== $tournament->id) {
            abort(404);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ];

        // Add conditional validation based on country
        if ($request->input('country') === 'Indonesia') {
            $rules['province'] = 'required|string|max:255';
        } else {
            $rules['state'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        // Handle logo upload and deletion of old file
        $team = $participant->team;
        if ($request->hasFile('logo')) {
            $newPath = $request->file('logo')->store('team-logos', 'public');
            // delete old
            if ($team->logo) {
                Storage::disk('public')->delete($team->logo);
            }
            $team->logo = $newPath;
        }

        $team->name = $validated['name'];
        $team->city = $validated['city'];
        $team->country = $validated['country'];
        $team->save();

        // Regenerate schedule/bracket when team details updated (names affect seeds/keys)
        app(MatchGenerator::class)->generateForTournament($tournament);

        return redirect()->route('tournaments.participants.index', $tournament)
            ->with('success', 'Detail peserta berhasil diperbarui.');
    }

    public function destroy(Tournament $tournament, TournamentTeam $participant)
    {
        if ($participant->tournament_id !== $tournament->id) {
            abort(404);
        }

        $participant->delete();

        // Regenerate schedule/bracket after removal
        app(MatchGenerator::class)->generateForTournament($tournament);

        return redirect()->route('tournaments.participants.index', $tournament)
            ->with('success', 'Peserta berhasil dihapus dari turnamen.');
    }

    private function generateTeamSlug(string $name): string
    {
        $slug = Str::slug($name) ?: Str::random(10);
        $originalSlug = $slug;
        $count = 1;

        while (Team::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    
}
