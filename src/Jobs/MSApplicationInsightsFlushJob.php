<?php

namespace Marchie\MSApplicationInsightsLaravel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Marchie\MSApplicationInsightsLaravel\MSApplicationInsightsServer;

class MSApplicationInsightsFlushJob implements SelfHandling, ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * @var MSApplicationInsightsServer
     */
    private $msApplicationInsights;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MSApplicationInsightsServer $msApplicationInsights)
    {
        $this->msApplicationInsights = $msApplicationInsights;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->msApplicationInsights->telemetryClient->flush();
    }
}
