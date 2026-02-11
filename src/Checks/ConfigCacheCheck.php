<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;
use Illuminate\Contracts\Foundation\Application;

class ConfigCacheCheck implements CheckInterface
{
    public function __construct(
        private readonly Application $app,
    ) {}

    public function key(): string
    {
        return 'config_cache';
    }

    public function title(): string
    {
        return 'Config cache';
    }

    public function run(DoctorContext $context): CheckResult
    {
        $configPath = $this->app->bootstrapPath('cache/config.php');
        $exists = file_exists($configPath);

        if ($context->isProduction()) {
            if (! $exists) {
                return CheckResult::warning('Config is not cached. Run: php artisan config:cache');
            }
            return CheckResult::passed('Config cache exists.');
        }

        if ($exists) {
            return CheckResult::suggestion(
                'Config cache exists in non-production. If you are debugging config/env issues, run: php artisan config:clear'
            );
        }

        return CheckResult::passed('Config cache not required for this environment.');
    }
}
