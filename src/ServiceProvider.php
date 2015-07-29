<?php namespace Marchie\MSApplicationInsightsLaravel;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('MSApplicationInsightsLaravel', function($app) {
            return new MSApplicationInsightsLaravel();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {

        return [
            'msapplicationinsights'
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
}
