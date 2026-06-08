<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentTeamOfficial extends Model
{
    protected $fillable = [
        'tournament_team_id',
        'official_name',
        'role',
        'contact_phone',
        'contact_email',
        'notes',
    ];

    public function tournamentTeam()
    {
        return $this->belongsTo(TournamentTeam::class);
    }
}
