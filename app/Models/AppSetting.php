<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    // Menentukan nama tabel jika nama tabel bukan 'app_settings'
    protected $table = 'app_settings';

    // Kolom yang bisa diisi (mass assignable)
    protected $fillable = ['key', 'value'];

    // Mengubah JSON di database menjadi array PHP secara otomatis
    protected $casts = [
        'value' => 'array',
    ];
}