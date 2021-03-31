<?php

namespace App\Http\Controllers;

use App\Jobs\Activity\SyncStravaJob;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        return view('activities.index');
    }

    public function sync(?string $channel): RedirectResponse
    {
        try {
            $credential = Auth::user()
                ->athletes()
                ->where('source', $channel)
                ->firstOrFail();

            dispatch(new SyncStravaJob($credential->athlete_id));
        } catch (ModelNotFoundException $e) {
            Log::debug('Strava account not connected.', ['USER.ID' => Auth::id()]);

            session()->flash('status', __('Please reconnect your Strava account.'));
        }

        return redirect()->route('activity');
    }
}
