<?php

namespace App\Services;

use App\Contracts\Workout;

class Relive implements Workout
{
    public function getChannel(): string
    {
        return 'relive';
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function profile()
    {
    }

    public function activities()
    {
    }
}
