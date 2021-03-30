<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class AthleteController extends Controller
{
    public function login(string $driver)
    {
        $drivers = ['strava'];
        throw_if(!in_array($driver, $drivers), new InvalidArgumentException(sprintf('Driver %s is not supported.', $driver)));

        return Socialite::driver($driver)
            ->scopes(config('services.strava.scopes'))
            ->redirect();
    }

    public function callback(Request $request, string $driver)
    {
        try {
            $athlete = Socialite::driver('strava')->user();
        } catch (InvalidStateException $e) {
            Log::debug($e);

            return redirect()->route('dashboard');
        } catch (ClientException $e) {
            Log::debug($e);

            return redirect()->route('dashboard');
        }

        $filters = [
            'athlete_id' => $athlete->id,
            'source' => $driver,
        ];

        $userAthlete = Athlete::where($filters)->first();

        // store token to session
        $tokenKey = sprintf(config('strava.token_key'), Auth::id());

        if (!empty($userAthlete) && !Auth::check()) {
            Auth::loginUsingId($userAthlete->user_id, true);

            // update new credentials
            $userAthlete = Athlete::firstOrNew($filters);
            $userAthlete->fill([
                'name' => $athlete->name,
                'avatar' => $athlete->avatar,
                'token' => $athlete->token,
                'refresh_token' => $athlete->refreshToken,
                'expired_at' => $athlete->expiresIn,
                'metadata' => $athlete->user ?? [],
            ]);
            $userAthlete->save();

            $request->session()->put($tokenKey, $userAthlete->token);
            
            return redirect()->route('dashboard');
        }
        
        if (Auth::check()) {
            // update new credentials
            $userAthlete = Athlete::firstOrNew($filters);
            $userAthlete->fill([
                'user_id' => Auth::id(),
                'name' => $athlete->name,
                'avatar' => $athlete->avatar,
                'token' => $athlete->token,
                'refresh_token' => $athlete->refreshToken,
                'expired_at' => $athlete->expiresIn,
                'metadata' => $athlete->user ?? [],
                'connected_at' => Carbon::now(),
            ]);

            $request->session()->put($tokenKey, $userAthlete->token);

            $userAthlete->save();
        }

        // user not registered yet
        $request->session()->put('athlete', $athlete);

        return redirect()->route('register')
            ->with('status', __('Successfully authenticated using ":driver" with ":name". Please login into system to continue.', [
                'driver' => ucfirst($driver),
                'name' => $athlete->name,
            ]));
    }
}
