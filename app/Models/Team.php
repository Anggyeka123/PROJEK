<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'city',
        'country',
        'notes',
        'manager_token',
        'verification_status',
    ];

    public function tournamentTeams()
    {
        return $this->hasMany(TournamentTeam::class);
    }
}
