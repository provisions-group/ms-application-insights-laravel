<?php namespace Marchie\MSApplicationInsightsLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class MSApplicationInsightsServerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'MSApplicationInsightsServer';
    }
}