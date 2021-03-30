<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Strava
{
    private $token;

    private $host;

    public function __construct()
    {
        $this->token = session('strava.token.'. Auth::id());

        if (env('APP_ENV') === 'local' && !empty(env('STRAVA_CLIENT_TOKEN')) && empty($this->token)) {
            $this->token = env('STRAVA_CLIENT_TOKEN');
        }

        if (empty($this->token)) {
            Log::error(sprintf('Token for user %s is not set.', Auth::id()));
        }

        $this->host = rtrim(config('services.strava.host'), '/');
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function profile()
    {
        $response = Http::withToken($this->token)->get($this->host.'/athlete');

        return $response->json();
    }

    public function stats(string $athleteId)
    {
        $response = Http::withToken($this->token)->get($this->host."/athletes/{$athleteId}/stats");

        return $response->json();
    }

    public function activities()
    {
        $response = Http::withToken($this->token)
            ->get($this->host.'/athlete/activities');

        return $response->json();
    }
}
