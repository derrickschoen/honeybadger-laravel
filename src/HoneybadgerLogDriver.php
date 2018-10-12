<?php

namespace Honeybadger\HoneybadgerLaravel;

use Monolog\Logger;
use Honeybadger\Contracts\Reporter;
use Illuminate\Support\Facades\App;

class HoneybadgerLogDriver
{
    public function __invoke(array $config) : Logger
    {
        return tap(new Logger($config['name'] ?? 'honeybadger'), function ($logger) {
            $logger->pushHandler(
                new LogHandler(App::make(Reporter::class))
            );
        });
    }
}
