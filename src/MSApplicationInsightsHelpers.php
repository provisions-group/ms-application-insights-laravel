<?php
namespace Marchie\MSApplicationInsightsLaravel;

class MSApplicationInsightsHelpers
{

    /**
     * @var MSApplicationInsightsServer
     */
    private $msApplicationInsights;


    public function __construct(MSApplicationInsightsServer $msApplicationInsights)
    {
        $this->msApplicationInsights = $msApplicationInsights;
    }

    /**
     * Track a page view
     *
     * @param $request
     * @return void
     */
    public function trackPageViewDuration($request)
    {
        if ($request->session()->has('ms_application_insights_page_info'))
        {
            $this->msApplicationInsights->telemetryClient->trackMessage(
                'browse_duration',
                $this->getPageViewProperties($request)
            );
        }
    }


    /**
     * Track application performance
     *
     * @param $request
     * @param $response
     */
    public function trackRequest($request, $response)
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
                $this->getRequestProperties($request),
                $this->getRequestMeasurements($request, $response)
            );
        }
    }


    /**
     * Flash page info for use in following page request
     *
     * @param $request
     */
    public function flashPageInfo($request)
    {
        $request->session()->flash('ms_application_insights_page_info', [
            'url' => $request->fullUrl(),
            'load_time' => microtime(true),
            'properties' => $this->getRequestProperties($request)
        ]);
    }


    /**
     * Get properties from the Laravel request
     *
     * @param $request
     *
     * @return array|null
     */
    private function getRequestProperties($request)
    {
        $properties = [
            'ajax' => $request->ajax(),
            'ip' => $request->ip(),
            'pjax' => $request->pjax(),
            'secure' => $request->secure(),
        ];

        if ($request->route()
            && $request->route()->getName())
        {
            $properties['route'] = $request->route()->getName();
        }

        if ($request->user())
        {
            $properties['user'] = $request->user()->id;
        }

        return $properties;
    }


    /**
     * Doesn't do a lot right now!
     *
     * @param $request
     * @param $response
     *
     * @return array|null
     */
    private function getRequestMeasurements($request, $response)
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
        return round(($_SERVER['REQUEST_TIME_FLOAT'] - $loadTime), 2);
    }

    /**
     * Calculate the time spent processing the request
     *
     * @return mixed
     */
    private function getRequestDuration()
    {
        return (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000;
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


    private function getPageViewProperties($request)
    {
        $pageInfo = $request->session()->get('ms_application_insights_page_info');

        $properties = $pageInfo['properties'];

        $properties['url'] = $pageInfo['url'];
        $properties['duration'] = $this->getPageViewDuration($pageInfo['load_time']);
        $properties['duration_formatted'] = $this->formatTime($properties['duration']);

        return $properties;
    }


    /**
     * @param $duration
     *
     * @return string
     */
    private function formatTime($duration)
    {
        $milliseconds = str_pad((round($duration - floor($duration), 2) * 100), 2, '0', STR_PAD_LEFT);

        if ($duration < 1) {
            return "0.{$milliseconds} seconds";
        }

        $seconds = floor($duration % 60);

        if ($duration < 60) {
            return "{$seconds}.{$milliseconds} seconds";
        }

        $string = str_pad($seconds, 2, '0', STR_PAD_LEFT) . '.' . $milliseconds;

        $minutes = floor(($duration % 3600) / 60);

        if ($duration < 3600) {
            return "{$minutes}:{$string}";
        }

        $string = str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . $string;

        $hours = floor(($duration % 86400) / 3600);

        if ($duration < 86400) {
            return "{$hours}:{$string}";
        }

        $string = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . $string;

        $days = floor($duration / 86400);

        return $days . ':' . $string;
    }
}