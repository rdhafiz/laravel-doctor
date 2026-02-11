<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;

class CacheFileInProductionCheck implements CheckInterface
{
    public function key(): string
    {
        return 'cache_file_in_production';
    }

    public function title(): string
    {
        return 'Cache driver file in production';
    }

    public function run(DoctorContext $context): CheckResult
    {
        $driver = (string) $context->config('cache.default');

        if ($context->isProduction() && $driver === 'file') {
            return CheckResult::warning(
                'Cache driver is file in production. Use redis/memcached/database for better performance.'
            );
        }

        return CheckResult::passed('Cache driver is not file in production.');
    }
}
