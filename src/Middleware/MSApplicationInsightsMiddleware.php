<?php

namespace Marchie\MSApplicationInsightsLaravel\Middleware;

use Closure;
use Marchie\MSApplicationInsightsLaravel\MSApplicationInsightsHelpers;

class MSApplicationInsightsMiddleware
{

    /**
     * @var MSApplicationInsightsHelpers
     */
    private $msApplicationInsightHelpers;


    /**
     * @param MSApplicationInsightsHelpers $msApplicationInsightsHelpers
     */
    public function __construct(MSApplicationInsightsHelpers $msApplicationInsightHelpers)
    {
        $this->msApplicationInsightHelpers = $msApplicationInsightHelpers;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->msApplicationInsightHelpers->trackPageViewDuration($request);

        $response = $next($request);

        $this->msApplicationInsightHelpers->flashPageInfo($request);

        return $response;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return void
     */
    public function terminate($request, $response)
    {
        $this->msApplicationInsightHelpers->trackRequest($request, $response);
    }
}
