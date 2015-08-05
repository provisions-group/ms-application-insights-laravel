<?php namespace Marchie\MSApplicationInsightsLaravel;

use Illuminate\Support\Facades\Facade;

class ServerFacade extends Facade
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