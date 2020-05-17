<?php

namespace Marchie\MSApplicationInsightsLaravel\Handlers;

use Exception;
use Illuminate\Container\Container;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Marchie\MSApplicationInsightsLaravel\MSApplicationInsightsHelpers;

class MSApplicationInsightsExceptionHandler extends ExceptionHandler
{

    /**
     * @var MSApplicationInsightsHelpers
     */
    private $msApplicationInsightsHelpers;


    public function __construct(Container $app)
    {
        $this->msApplicationInsightsHelpers = app(MSApplicationInsightsHelpers::class);

        parent::__construct($app);
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function report(\Throwable $e)
    {
        foreach ($this->dontReport as $type)
        {
            if ($e instanceof $type)
            {
                return parent::report($e);
            }
        }

        $this->msApplicationInsightsHelpers->trackException($e);

        return parent::report($e);
    }
}
