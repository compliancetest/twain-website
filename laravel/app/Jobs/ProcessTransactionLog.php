<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessTransactionLog extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $fileName;

    /**
     * ProcessTransactionLog constructor.
     * @param $fileName
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        error_log('tried to process queue');
    }
}
