# Microsoft Application Insights for Laravel 5

A simple Laravel implementation for [Microsoft Application Insights](http://azure.microsoft.com/en-gb/services/application-insights/)

## Installation

Update the `require` section of your application's **composer.json** file:

```js
"require": {
	...
	"marchie/ms-application-insights-laravel": "0.2.0",
	...
}
```

### Service Provider

Add the service provider to the *providers* array in your application's **config/app.php** file:

```php
'providers' => [
	...
	Marchie\MSApplicationInsightsLaravel\Providers\MSApplicationInsightsServiceProvider::class,
	...
]
```

### Facade

Add the facades to the *aliases* array in your application's **config/app.php** file:

```php
'aliases' => [
	...
	'AIClient' => Marchie\MSApplicationInsightsLaravel\Facades\MSApplicationInsightsClientFacade::class,
	'AIServer' => Marchie\MSApplicationInsightsLaravel\Facades\MSApplicationInsightsServerFacade::class,
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

### Request Tracking Middleware

To monitor your application's performance with request tracking, add the middleware to your application's *global* HTTP middleware stack , found in **app/Http/Kernel.php**:

```php

protected $middleware = [
	...
	'MSApplicationInsightsMiddleware',
	...
]

```

The request will send the following additional parameters to Application Insights:

- **ajax** *(boolean)*: *true* if the request is an AJAX request
- **ip** *(string)*: The client's IP address
- **pjax** *(boolean)*: *true* if the request is a PJAX request
- **secure** *(boolean)*: *true* if the request was sent over HTTPS
- **route** *(string)*: The name of the route, if applicable
- **user** *(integer)*: The ID of the logged in user, if applicable

The middleware is also used to estimate the time that a user has spent on a particular page.  This is sent as a *trace* event named **browse_duration**.

### Exception Handler

To report exceptions that occur in your application, use the provided exception handler.  *Replace* the following line in your application's **app/Handlers/Exception.php** file:

```php
...

# Delete this line
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

# Insert this line
use Marchie\MSApplicationInsightsLaravel\Handlers\MSApplicationInsightsExceptionHandler as ExceptionHandler;

...
```

### Client Side

In order to register page view information from the client with Application Insights, simply insert the following code into your Blade views:

```php
{!! MSAppInsights::javascript() !!}
```

NOTE: Microsoft recommend that you put the script in the `<head>` section of your pages, in order to calculate the fullest extent of page load time on the client.

### Custom

If you want to use any of the underlying [ApplicationInsights-PHP](https://github.com/Microsoft/ApplicationInsights-PHP) functionality, you can call the methods directly from the server facade:

```php
...
MSAIServer::trackEvent('Test event');
...
```

See the [ApplicationInsights-PHP](https://github.com/Microsoft/ApplicationInsights-PHP) page for more information on the available methods.

NOTE: You don't need to worry about calling the *flush* method; the package takes care of that bit for you when it shuts down.

## Version History

### 0.2.0
- Exception handler and request tracking

### 0.1.2
- Correcting silly mistake!

### 0.1.1
- Empty key no longer results in an exception being thrown (no JavaScript is inserted into the view)

### 0.1.0
- Client-side JavaScript only