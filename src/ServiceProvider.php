<?php namespace Marchie\MSApplicationInsightsLaravel;

use ApplicationInsights\Telemetry_Client;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Marchie\MSApplicationInsightsMonolog\MSApplicationInsightsHandler;

class ServiceProvider extends LaravelServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {

        $this->handleConfigs();
        // $this->handleMigrations();
        // $this->handleViews();
        // $this->handleTranslations();
        // $this->handleRoutes();
        $this->pushLoggerHandler();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('MSApplicationInsightsClient', function($app) {
            return new MSApplicationInsightsClient();
        });

        $this->app->bind('MSApplicationInsightsServer', function($app) {
            $telemetryClient = new Telemetry_Client();
            return new MSApplicationInsightsServer($telemetryClient);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {

        return [
            'msapplicationinsightsclient',
            'msapplicationinsightsserver'
        ];
    }

    private function handleConfigs() {

        $configPath = __DIR__ . '/../config/MSApplicationInsightsLaravel.php';

        $this->publishes([$configPath => config_path('MSApplicationInsightsLaravel.php')]);

        $this->mergeConfigFrom($configPath, 'MSApplicationInsightsLaravel');
    }

    private function handleTranslations() {

        $this->loadTranslationsFrom('MSApplicationInsightsLaravel', __DIR__.'/../lang');
    }

    private function handleViews() {

        $this->loadViewsFrom('MSApplicationInsightsLaravel', __DIR__.'/../views');

        $this->publishes([__DIR__.'/../views' => base_path('resources/views/vendor/MSApplicationInsightsLaravel')]);
    }

    private function handleMigrations() {

        $this->publishes([__DIR__ . '/../migrations' => base_path('database/migrations')]);
    }

    private function handleRoutes() {

        include __DIR__.'/../routes.php';
    }

    /**
     * Pushes Microsoft Application Insights Monolog handler
     * This is called when an exception or error is logged
     * within the application
     */
    private function pushLoggerHandler()
    {
        $logger = app('log')->getMonolog();
        $msApplicationInsights = app('MSApplicationInsightsServer');
        if (isset($msApplicationInsights->telemetryClient)) {
            $msApplicationInsightsHandler = new MSApplicationInsightsHandler($msApplicationInsights->telemetryClient);
            $logger->pushHandler($msApplicationInsightsHandler);
        }
    }
}
