<?php

namespace App\Contracts;

interface Workout
{
    public function getChannel(): string;

    public function setToken(string $token): self;

    public function profile();

    public function activities();
}
