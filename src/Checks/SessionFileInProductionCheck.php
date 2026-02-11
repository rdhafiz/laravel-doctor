<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;

class SessionFileInProductionCheck implements CheckInterface
{
    public function key(): string
    {
        return 'session_file_in_production';
    }

    public function title(): string
    {
        return 'Session driver file in production';
    }

    public function run(DoctorContext $context): CheckResult
    {
        $driver = $context->config('session.driver');

        if ($context->isProduction() && ($driver === 'file' || $driver === null)) {
            return CheckResult::warning(
                'Session driver is file in production. Use redis/database for scalability.'
            );
        }

        return CheckResult::passed('Session driver is not file in production.');
    }
}
