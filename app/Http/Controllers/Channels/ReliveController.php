<?php

namespace App\Http\Controllers\Channels;

use App\Contracts\Workout;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReliveController extends Controller
{
    /**
     * @var Workout
     */
    private $workout;

    public function __construct(Workout $workout)
    {
        $this->workout = $workout;
    }

    public function sync()
    {
        
    }
}
