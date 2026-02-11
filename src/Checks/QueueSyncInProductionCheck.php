<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;

class QueueSyncInProductionCheck implements CheckInterface
{
    public function key(): string
    {
        return 'queue_sync_in_production';
    }

    public function title(): string
    {
        return 'Queue driver sync in production';
    }

    public function run(DoctorContext $context): CheckResult
    {
        $driver = (string) $context->config('queue.default');

        if ($context->isProduction() && $driver === 'sync') {
            return CheckResult::warning(
                'Queue driver is sync in production. Set QUEUE_CONNECTION to database/redis and run a worker.'
            );
        }

        return CheckResult::passed('Queue driver is not sync in production.');
    }
}
