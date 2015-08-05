<?php
namespace Marchie\MSApplicationInsightsLaravel;

use Marchie\MSApplicationInsightsLaravel\Exceptions\InvalidMSInstrumentationKeyException;

class InstrumentationKey
{
    protected $instrumentationKey;

    public function __construct()
    {
        $this->setInstrumentationKey();
    }

    protected function setInstrumentationKey()
    {
        $instrumentationKey = config('MSApplicationInsightsLaravel.instrumentationKey');

        if ( ! empty($instrumentationKey)
            && $this->checkInstrumentationKeyValidity($instrumentationKey))
        {
            $this->instrumentationKey = $instrumentationKey;

            return;
        }

        $this->instrumentationKey = null;
    }

    protected function checkInstrumentationKeyValidity($instrumentationKey)
    {
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $instrumentationKey) === 1)
        {
            return true;
        }

        throw new InvalidMSInstrumentationKeyException("'{$instrumentationKey}' is not a valid Microsoft Application Insights instrumentation key.");
    }
}