<?php

namespace Marchie\MSApplicationInsightsLaravel\Jobs;

use ApplicationInsights\Telemetry_Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class MSApplicationInsightsFlushJob implements SelfHandling, ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * @var
     */
    private $telemetryClient;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Telemetry_Client $telemetryClient)
    {
        $this->telemetryClient = $telemetryClient;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->telemetryClient->flush();
    }
}
