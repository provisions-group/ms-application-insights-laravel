<?php
namespace Marchie\MSApplicationInsightsLaravel\Handlers;

use Exception;
use Marchie\MSApplicationInsightsLaravel\MSApplicationInsightsHelpers;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class MSApplicationInsightsExceptionHandler extends ExceptionHandler
{

    /**
     * @var MSApplicationInsightsHelpers
     */
    private $msApplicationInsightsHelpers;


    public function __construct(MSApplicationInsightsHelpers $msApplicationInsightsHelpers, LoggerInterface $log, Container $container)
    {
        $this->msApplicationInsightsHelpers = $msApplicationInsightsHelpers;

        // Laravel 5.3 introduced a breaking change in the Exception Handler constructor.
        // See the section 'Exception Handler'->'Constructor' in https://laravel.com/docs/5.3/upgrade#upgrade-5.3.0
        if (version_compare(app()->version(), '5.3.0', '>=')) {
            parent::__construct($container);
        } else {
            parent::__construct($log);
        }
    }

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

        $this->msApplicationInsightsHelpers->trackException($e);

        return parent::report($e);
    }
}