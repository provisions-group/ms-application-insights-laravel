# Microsoft Application Insights for Laravel 5

## Installation

Update the `require` section of your application's **composer.json** file:

```
"require": {
	...
	"marchie/ms-application-insights-laravel": "dev-master",
	...
}
```

### Service Provider

Add the service provider to the *providers* array in your application's **config/app.php** file:

```
'providers' => [
	...
	Marchie\MSApplicationInsightsLaravel\ServiceProvider::class,
	...
]
```

### Facade

Add the facade to the *aliases* array in your application's **config/app.php** file:

```
'aliases' => [
	...
	'MSAppInsights' => Marchie\MSApplicationInsightsLaravel\Facade::class,
	...
]
```

### Instrumentation Key

#### Using .env

The package is set up by default to check your application's **.env** file for your *Instrumentation Key*.

Add the following to your **.env** file:

```
...
MS_INSTRUMENTATION_KEY=<your instrumentation key>
...
```

#### Using a published configuration file