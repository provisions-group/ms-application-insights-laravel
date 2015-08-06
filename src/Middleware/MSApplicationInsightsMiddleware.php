<?php
namespace Marchie\MSApplicationInsightsLaravel\Middleware;

use Closure;

class MSApplicationInsightsMiddleware
{
    private $aiData;

    /**
     * @var
     */
    private $msApplicationInsights;


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
        $this->setRequestData($request);

        return $next($request);
    }

    public function terminate($request, $response)
    {
        $this->setResponseData($response);

        $this->trackRequest();
    }

    private function trackRequest()
    {
        if ($this->msApplicationInsights->telemetryClient)
        {
            $this->msApplicationInsights->telemetryClient->trackRequest('application', $this->aiData['url'], $this->aiData['start_time'], $this->aiData['duration'], $this->aiData['status_code'], $this->aiData['success'], $this->aiData['properties'], $this->aiData['measurements']);
        }
    }

    private function setRequestData($request)
    {
        $this->aiData = [
            'start_time' => $_SERVER['REQUEST_TIME_FLOAT'],
            'url' => $request->fullUrl(),
            'properties' => $this->setProperties($request),
            'measurements' => $this->setMeasurements($request),
        ];
    }

    private function setResponseData($response)
    {
        $this->aiData['status_code'] = $response->status();
        $this->aiData['duration'] = (microtime(true) - $this->aiData['start_time']) * 1000;
        $this->aiData['success'] = ($response->status() < 400);
    }

    private function setProperties($request)
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


    private function setMeasurements($request)
    {
        $measurements = [];

        return ( ! empty($measurements)) ? $measurements : null;
    }
}