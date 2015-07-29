<?php

return [

    /*
     * Instrumentation Key
     * ===================
     *
     * The instrumentation key can be found on the Application Insights dashboard on portal.azure.com
     * Microsoft Azure > Browse > Application Insights > (Application Name) > Settings > Properties
     *
     * Add the MS_INSTRUMENTATION_KEY field to your application's .env file,
     * then paste in the value found on the properties page shown above.
     *
     * Alternatively, replace the env call below with a string containing your key.
     */

    'instrumentationKey' => env('MS_INSTRUMENTATION_KEY', null),

];
