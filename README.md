# Microsoft Application Insights for Laravel 5

At this moment in time, this is just a simple Laravel implementation for the client-side JavaScript element of [Microsoft Application Insights](http://azure.microsoft.com/en-gb/services/application-insights/)

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

The package will check your application's **.env** file for your *Instrumentation Key*.

Add the following to your **.env** file:

```
...
MS_INSTRUMENTATION_KEY=<your instrumentation key>
...
```

You can find your instrumentation key on the [Microsoft Azure Portal](https://portal.azure.com).

Navigate to:

**Microsoft Azure** > **Browse** > **Application Insights** > *(Application Name)* > **Settings** > **Properties**

## Usage

### Client Side

In order to register information from the client with Application Insights, simply insert the following code into your Blade views:

```
{!! MSAppInsights::javascript() !!}
```

## Version History

### 0.1
- Client-side JavaScript only