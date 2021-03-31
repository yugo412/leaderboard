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
        // set token automatically is exists in session
        $sessionKey = sprintf(config('strava.token_key'), Auth::id());
        if (session()->has($sessionKey)) {
            $this->token = session($sessionKey);
        }

        if (env('APP_ENV') === 'local' && !empty(env('STRAVA_CLIENT_TOKEN')) && empty($this->token)) {
            $this->token = env('STRAVA_CLIENT_TOKEN');
        }

        $this->host = rtrim(config('services.strava.host'), '/');
    }

    public function setToken(?string $token): self
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

    public function activities($page = 1)
    {
        $response = Http::withToken($this->token)
            ->get($this->host.'/athlete/activities?per_page=50&page='.$page);

        return $response->json();
    }
}
