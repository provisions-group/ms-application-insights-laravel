<?php
namespace Marchie\MSApplicationInsightsLaravel;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class MSApplicationInsightsExceptionHandler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        foreach ($this->dontReport as $type)
        {
            if ($e instanceof $type)
            {
                return parent::report($e);
            }
        }

        $telemetryClient = app('MSApplicationInsightsServer');

        if ($telemetryClient)
        {
            $telemetryClient->trackException($e);
        }

        return parent::report($e);
    }
}