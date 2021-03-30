<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class Athlete extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'athlete_id',
        'name',
        'avatar',
        'metadata',
        'token',
        'refresh_token',
        'expired_at',
        'connected_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'connected_at' => 'datetime',
        'expired_at' => 'datetime',
        'metadata' => 'json',
    ];

    public function setTokenAttribute(string $token)
    {
        $this->attributes['token'] = Crypt::encrypt($token);
    }

    public function setRefreshTokenAttribute(string $refreshToken)
    {
        $this->attributes['refresh_token'] = Crypt::encrypt($refreshToken);
    }

    public function setExpiredAtAttribute($expiresIn)
    {
        if (is_int($expiresIn) || !$expiresIn instanceof Carbon) {
            $this->attributes['expired_at'] = Carbon::now()->addMinutes($expiresIn);
        } else {
            $this->attributes['expired_at'] = $expiresIn;
        }
    }

    public function getTokenAttribute($token)
    {
        try {
            return Crypt::decrypt($token);
        } catch (DecryptException $e) {
            Log::error($e);
        }
    }

    public function getRefreshTokenAttribute($refreshToken)
    {
        try {
            return Crypt::decrypt($refreshToken);
        } catch (DecryptException $e) {
            Log::error($e);
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
