<?php
namespace Marchie\MSApplicationInsightsLaravel;

use Throwable;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function trackPageViewDuration($request)
    {
        if ($this->telemetryEnabled()) {
            if ($request->session()->has('ms_application_insights_page_info')) {
                $this->msApplicationInsights->telemetryClient->trackMessage('browse_duration',
                    $this->getPageViewProperties($request));
                try {
                    $this->msApplicationInsights->telemetryClient->flush();
                } catch (RequestException $e) {}
            }
        }
    }


    /**
     * Track application performance
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     */
    public function trackRequest($request, $response)
    {
        if ($this->telemetryEnabled())
        {
            if ($this->msApplicationInsights->telemetryClient)
            {
                $this->msApplicationInsights->telemetryClient->trackRequest(
                    'application',
                    $request->fullUrl(),
                    $_SERVER['REQUEST_TIME_FLOAT'],
                    $this->getRequestDuration(),
                    $this->getResponseCode($response),
                    $this->isSuccessful($response),
                    $this->getRequestProperties($request),
                    $this->getRequestMeasurements($request, $response)
                );
                try {
                    $this->msApplicationInsights->telemetryClient->flush();
                } catch (RequestException $e) {}
            }
        }
    }

    /**
     * Track application exceptions
     *
     * @param Exception $e
     */
    public function trackException(Throwable $e)
    {
        if ($this->telemetryEnabled()) {
            $this->msApplicationInsights->telemetryClient->trackException($e,
                $this->getRequestPropertiesFromException($e));
            try {
                $this->msApplicationInsights->telemetryClient->flush();
            } catch (RequestException $e) {}
        }
    }


    /**
     * Get request properties from the exception trace, if available
     *
     * @param Exception $e
     *
     * @return array|null
     */
    private function getRequestPropertiesFromException(Throwable $e)
    {
        foreach ($e->getTrace() as $item)
        {
            if (isset($item['args']))
            {
                foreach ($item['args'] as $arg)
                {
                    if ($arg instanceof Request)
                    {
                        return $this->getRequestProperties($arg);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Flash page info for use in following page request
     *
     * @param \Illuminate\Http\Request $request
     */
    public function flashPageInfo($request)
    {
        if ($this->telemetryEnabled())
        {
            $request->session()->flash('ms_application_insights_page_info', [
                'url' => $request->fullUrl(),
                'load_time' => microtime(true),
                'properties' => $this->getRequestProperties($request)
            ]);
        }

    }

    /**
     * Determines whether the Telemetry Client is enabled
     *
     * @return bool
     */
    private function telemetryEnabled()
    {
        return isset($this->msApplicationInsights->telemetryClient);
    }


    /**
     * Get properties from the Laravel request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|null
     */
    private function getRequestProperties($request)
    {
        $properties = [
            'ajax' => $request->ajax(),
            'fullUrl' => $request->fullUrl(),
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

        if ($request->server('HTTP_REFERER'))
        {
            $properties['referer'] = $request->server('HTTP_REFERER');
        }

        return $properties;
    }


    /**
     * Doesn't do a lot right now!
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
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
     * @param \Illuminate\Http\Response $response
     *
     * @return bool
     */
    private function isSuccessful($response)
    {
        return ($this->getResponseCode($response) < 400);
    }


    /**
     * Get additional properties for page view at the end of the request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
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
     * Formats time strings into a human-readable format
     *
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

    /**
     * If you use stream() or streamDownload() then the response object isn't a standard one. Here we check different
     * places for the status code depending on the object that Laravel sends us.
     *
     * @param StreamedResponse|Response $response The response object
     *
     * @return int The HTTP status code
     */
    private function getResponseCode($response)
    {
        return $response instanceof StreamedResponse ? $response->getStatusCode() : $response->status();
    }
}
