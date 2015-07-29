<?php namespace Marchie\MSApplicationInsightsLaravel;

use Illuminate\Support\Facades\Facade;

class MSApplicationInsightsLaravelFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'msapplicationinsights';
    }
}