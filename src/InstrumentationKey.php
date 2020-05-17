<?php
namespace Marchie\MSApplicationInsightsLaravel;

use Marchie\MSApplicationInsightsLaravel\Exceptions\InvalidMSInstrumentationKeyException;

class InstrumentationKey
{
    /** @var string */
    protected $instrumentationKey;

    protected static $alreadyThrown = false;

    /**
     * InstrumentationKey constructor.
     *
     * @throws InvalidMSInstrumentationKeyException
     */
    public function __construct()
    {
        $this->setInstrumentationKey();
    }

    /**
     * @throws InvalidMSInstrumentationKeyException
     *
     * @since 0.2.5
     */
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


    /**
     * @param $instrumentationKey
     *
     * @return bool
     *
     * @throws InvalidMSInstrumentationKeyException
     */
    protected function checkInstrumentationKeyValidity($instrumentationKey)
    {
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $instrumentationKey) === 1)
        {
            return true;
        }

        if ($this->InvalidKeyExceptionWasNotThrown()) {
            throw new InvalidMSInstrumentationKeyException(
                "'{$instrumentationKey}' is not a valid Microsoft Application Insights instrumentation key."
            );
        }

        return false;
    }

    protected function InvalidKeyExceptionWasNotThrown()
    {
        if (!static::$alreadyThrown) {
            static::$alreadyThrown = true;

            return false;
        }

        return true;
    }
}
