<?php

namespace App\Jobs;

use App\Models\Test;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPodcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $time;
    public function __construct($time)
    {
        $this->time = $this;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Test::create(['param'=>date('Y-m-d H:i:s',$this->time).' and '.date('Y-m-d H:i:s')]);
    }
}
