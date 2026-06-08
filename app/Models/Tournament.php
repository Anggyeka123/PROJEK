<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TournamentMatch;

class Tournament extends Model
{
    protected $fillable = [
        'name',
        'match_date',
        'division',
        'venue',
        'created_by',
    ];

    protected $casts = [
        'match_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function groupSetting()
    {
        return $this->hasOne(TournamentGroupSetting::class);
    }

    public function matches()
    {
        return $this->hasMany(TournamentMatch::class);
    }

    public function tournamentTeams()
    {
        return $this->hasMany(TournamentTeam::class);
    }
}
