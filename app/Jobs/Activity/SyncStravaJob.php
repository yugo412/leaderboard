<?php

namespace App\Jobs\Activity;

use App\Contracts\Workout;
use App\Models\Athlete;
use App\Models\ChannelActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncStravaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Workout
     */
    private $workout;

    /**
     * @var string
     */
    private $athleteId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Workout $workout, string $athleteId)
    {
        $this->workout = $workout;
        $this->athleteId = $athleteId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $credential = Athlete::where('source', $this->workout->getChannel())
                ->where('athlete_id', $this->athleteId)
                ->firstOrFail();

            $finished = false;
            $page = 1;

            do {
                $activities = $this->workout
                    ->setToken($credential->token)
                    ->activities();

                if (!empty($activities['message'])) {
                    Log::error($activities['message'], [
                        'USER.ID' => $credential->user_id,
                        'channel' => $this->workout->getChannel(),
                    ]);
                    
                    break;
                }
                
                if (count($activities) <= 0) {
                    $finished = true;
                } else {
                    ++$page;

                    foreach ($activities as $activity) {
                        $channelActivity = ChannelActivity::firstOrNew([
                            'channel' => $this->workout->getChannel(),
                            'athlete_id' => data_get($activity, 'athlete.id'),
                            'activity_id' => data_get($activity, 'id'),
                        ]);

                        $channelActivity->fill([
                            'activity' => $activity,
                            'is_synced' => false,
                        ]);

                        $channelActivity->save();
                    }
                }
            } while ($finished === false);
        } catch (ModelNotFoundException $e) {
            Log::error(sprintf('Athlete for ID %s not found.', $this->athleteId));
            unset($e);
        }
    }
}
