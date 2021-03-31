<?php

namespace App\Http\Controllers\Channels;

use App\Contracts\Workout;
use App\Http\Controllers\Controller;
use App\Jobs\Activity\SyncStravaJob;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StravaController extends Controller
{
    /**
     * @var Workout
     */
    private $workout;

    public function __construct(Workout $workout)
    {
        $this->workout = $workout;
    }

    public function sync(): RedirectResponse
    {
        try {
            $credential = Auth::user()
                ->athletes()
                ->where('source', $this->workout->getChannel())
                ->firstOrFail();

            dispatch(new SyncStravaJob($this->workout, $credential->athlete_id));
        } catch (ModelNotFoundException $e) {
            Log::debug('Strava account not connected.', ['USER.ID' => Auth::id()]);

            session()->flash('status', __('Please reconnect your Strava account.'));
        }

        return redirect()->route('activity');
    }
}
