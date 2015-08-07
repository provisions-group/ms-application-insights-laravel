<?php
namespace Marchie\MSApplicationInsightsLaravel\Middleware;

use Closure;
use Marchie\MSApplicationInsightsLaravel\MSApplicationInsightsServer;

class MSApplicationInsightsMiddleware
{
    /**
     * @var
     */
    private $msApplicationInsights;


    /**
     * @param MSApplicationInsightsServer $msApplicationInsights
     */
    public function __construct(MSApplicationInsightsServer $msApplicationInsights)
    {
        $this->msApplicationInsights = $msApplicationInsights;
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
        $this->trackPageView($request);

        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return void
     */
    public function terminate($request, $response)
    {
        $this->flashPageInfo($request);

        $this->trackRequest($request, $response);
    }


    /**
     * Track a page view
     *
     * @param $request
     * @return void
     */
    private function trackPageView($request)
    {
        if ($request->session()->has('ms_application_insights_page_info'))
        {
            $pageInfo = $request->session()->get('ms_application_insights_page_info', null);

            if (isset($pageInfo))
            {
                $this->msApplicationInsights->telemetryClient->trackPageView(
                    'application',
                    $pageInfo['url'],
                    $this->getPageViewDuration($pageInfo['load_time']),
                    $pageInfo['properties']
                );
            }
        }
    }


    /**
     * Track application performance
     *
     * @param $request
     * @param $response
     */
    private function trackRequest($request, $response)
    {
        if ($this->msApplicationInsights->telemetryClient)
        {
            $this->msApplicationInsights->telemetryClient->trackRequest(
                'application',
                $request->fullUrl(),
                $_SERVER['REQUEST_TIME_FLOAT'],
                $this->getRequestDuration(),
                $response->status(),
                $this->isSuccessful($response),
                $this->getProperties($request),
                $this->getMeasurements($request, $response)
            );
        }
    }


    /**
     * Flash page info for use in following page request
     *
     * @param $request
     */
    private function flashPageInfo($request)
    {
        $request->session()->flash('ms_application_insights_page_info', [
            'url' => $request->fullUrl(),
            'load_time' => microtime(true),
            'properties' => $this->getProperties($request)
        ]);
    }


    /**
     * Get properties from the Laravel request
     *
     * @param $request
     *
     * @return array|null
     */
    private function getProperties($request)
    {
        $properties = [
            'ajax' => $request->ajax(),
            'ip' => $request->ip(),
            'pjax' => $request->pjax(),
            'secure' => $request->secure(),
        ];

        if ($request->route())
        {
            $properties['route'] = $request->route();
        }

        if ($request->user())
        {
            $properties['user'] = $request->user()->id;
        }

        return ( ! empty($properties)) ? $properties : null;
    }


    /**
     * Doesn't do a lot right now!
     *
     * @param $request
     * @param $response
     *
     * @return array|null
     */
    private function getMeasurements($request, $response)
    {
        $measurements = [];

        return ( ! empty($measurements)) ? $measurements : null;
    }


    /**
     * Estimate the time spent viewing the previous page
     *
     * @param $loadTime
     *
     * @return mixed
     */
    private function getPageViewDuration($loadTime)
    {
        return $_SERVER['REQUEST_TIME_FLOAT'] - $loadTime;
    }


    /**
     * Calculate the time spent processing the request
     *
     * @return mixed
     */
    private function getRequestDuration()
    {
        return microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
    }


    /**
     * Determine if the request was successful
     *
     * @param $response
     *
     * @return bool
     */
    private function isSuccessful($response)
    {
        return ($response->status() < 400);
    }

}