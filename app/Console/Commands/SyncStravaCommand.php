<?php

namespace App\Console\Commands;

use App\Models\ChannelActivity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncStravaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strava:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync local Strava activities with user\'s activities';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ChannelActivity::where('is_synced', false)
            ->chunk(100, function ($activities) {
                foreach ($activities as $channelActivity) {
                    $this->storeActiovity($channelActivity);
                }
            });

        return 0;
    }

    private function storeActiovity(ChannelActivity $channelActivity)
    {
        DB::transaction(function () use ($channelActivity) {
            $channelActivity->fill(['is_synced' => true]);
            $channelActivity->save();
        });
    }
}
