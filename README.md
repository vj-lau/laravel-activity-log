# laravel-activity-log
Laravel Activity Log

## Installation
Make sure you have the MongoDB PHP driver installed. You can find installation instructions at 
http://php.net/manual/en/mongodb.installation.php


Install through Composer
```
composer require vjlau/laravel-activity-log
```

## Configuration
And add a new mongodb connection `config/database.php`:
```
'mongodb' => [
    'driver'   => 'mongodb',
    'host'     => env('DB_HOST', 'localhost'),
    'port'     => env('DB_PORT', 27017),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'options'  => [
        'database' => 'admin' // sets the authentication database required by mongo 3
    ]
],
```

## Setup
### Step 1: Register the Service Provider
Add the ActivityLogServiceProvider to the providers array in the `config/app.php` file;
```
Jenssegers\Mongodb\MongodbServiceProvider::class,
VJLau\ActivityLog\ActivityLogServiceProvider::class,
VJLau\ActivityLog\QueryLogServiceProvider::class,
```

### Step 2: Publish config
```
php artisan vendor:publish --provider="VJLau\ActivityLog\ActivityLogServiceProvider" --tag="config"
```

## Usage
To subscribe model for activity log just use `VJLau\ActivityLog\Loggable`
```
use VJLau\ActivityLog\Loggable;
```

To add usage in your model
```
use Loggable;
```

To add middleware in your kernel
```
protected $routeMiddleware = [
    ......
    'log.request' => \VJLau\ActivityLog\Middleware\LogAfterRequest::class,
];
```

## Credits
https://github.com/leomarquine/activity-log - Activity Log for Laravel Eloquent Models

## License
MIT - http://opensource.org/licenses/MIT