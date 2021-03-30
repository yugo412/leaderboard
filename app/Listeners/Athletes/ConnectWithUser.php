<?php

namespace App\Listeners\Athletes;

use App\Models\Athlete;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class ConnectWithUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $athlete = session('athlete');

        if (!empty($athlete)) {
            $userAthlete = Athlete::create([
                'user_id' => Auth::id(),
                'athlete_id' => $athlete->id,
                'name' => $athlete->name,
                'avatar' => $athlete->avatar,
                'token' => $athlete->token,
                'refresh_token' => $athlete->refreshToken,
                'expired_at' => $athlete->expiresIn,
                'connected_at' => Carbon::now(),
                'metadata' => $athlete->user ?? [],
            ]);

            $key = sprintf(config('strava.token_key'), Auth::id());
            session()->put($key, $userAthlete->token);
        }

        session()->forget('athlete');
    }
}
