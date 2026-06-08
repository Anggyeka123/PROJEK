<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentTeamPlayer extends Model
{
    protected $fillable = [
        'tournament_team_id',
        'player_name',
        'shirt_number',
        'positions',
        'dominant_position',
        'phone',
        'birth_place',
        'birth_date',
        'photo',
        'is_captain',
        'status',
        'registered_at',
    ];

    protected $casts = [
        'positions' => 'array',
    ];

    public function tournamentTeam()
    {
        return $this->belongsTo(TournamentTeam::class);
    }
}
