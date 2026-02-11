<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;
use Illuminate\Contracts\Foundation\Application;

class RouteCacheCheck implements CheckInterface
{
    private const ROUTE_CACHE_FILES = [
        'cache/routes.php',
        'cache/routes-v7.php',
        'cache/routes-v8.php',
        'cache/routes-v9.php',
    ];

    public function __construct(
        private readonly Application $app,
    ) {}

    public function key(): string
    {
        return 'route_cache';
    }

    public function title(): string
    {
        return 'Route cache';
    }

    public function run(DoctorContext $context): CheckResult
    {
        $cacheExists = false;
        foreach (self::ROUTE_CACHE_FILES as $file) {
            if (file_exists($this->app->bootstrapPath($file))) {
                $cacheExists = true;
                break;
            }
        }

        if ($context->isProduction()) {
            if (! $cacheExists) {
                return CheckResult::warning('Routes are not cached. Run: php artisan route:cache');
            }
            return CheckResult::passed('Route cache exists.');
        }

        if ($cacheExists) {
            return CheckResult::suggestion(
                'Route cache exists in non-production. If changing routes and seeing stale behavior, run: php artisan route:clear'
            );
        }

        return CheckResult::passed('Route cache not required for this environment.');
    }
}
