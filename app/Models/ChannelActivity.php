<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'channel',
        'activity',
        'is_synced',
    ];

    protected $casts = [
        'activity' => 'json',
        'is_synced' => 'boolean',
    ];
}
