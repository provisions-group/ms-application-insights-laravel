<?php namespace Marchie\MSApplicationInsightsLaravel;

use Exception;

class MSApplicationInsightsLaravel
{
    private $instrumentationKey;

    public function __construct()
    {
        $this->instrumentationKey = $this->setInstrumentationKey();
    }

    public function javascript()
    {
        $script = <<<SCRIPT
<script type="text/javascript">''
    var appInsights=window.appInsights||function(config){
        function s(config){t[config]=function(){var i=arguments;t.queue.push(function(){t[config].apply(t,i)})}}var t={config:config},r=document,f=window,e="script",o=r.createElement(e),i,u;for(o.src=config.url||"//az416426.vo.msecnd.net/scripts/a/ai.0.js",r.getElementsByTagName(e)[0].parentNode.appendChild(o),t.cookie=r.cookie,t.queue=[],i=["Event","Exception","Metric","PageView","Trace"];i.length;)s("track"+i.pop());return config.disableExceptionTracking||(i="onerror",s("_"+i),u=f[i],f[i]=function(config,r,f,e,o){var s=u&&u(config,r,f,e,o);return s!==!0&&t["_"+i](config,r,f,e,o),s}),t
            }({
        instrumentationKey: "{$this->instrumentationKey}"
            });

    window.appInsights=appInsights;
    appInsights.trackPageView();
</script>
SCRIPT;

        return $script;

    }

    private function setInstrumentationKey()
    {
        $instrumentationKey = config('MSApplicationInsightsLaravel.instrumentationKey');

        if ($this->checkInstrumentationKeyValidity($instrumentationKey))
        {
            $this->instrumentationKey = $instrumentationKey;
        }
    }

    private function checkInstrumentationKeyValidity($instrumentationKey)
    {
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{8}$/', $instrumentationKey) === 1)
        {
            return true;
        }

        throw new InvalidMSInstrumentationKeyException("'{$instrumentationKey}' is not a valid Microsoft Application Insights instrumentation key.");
    }
}

class MSApplicationInsightsException extends Exception {}

class InvalidMSInstrumentationKeyException extends MSApplicationInsightsException {}