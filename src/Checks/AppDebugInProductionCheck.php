<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;

class AppDebugInProductionCheck implements CheckInterface
{
    public function key(): string
    {
        return 'app_debug_in_production';
    }

    public function title(): string
    {
        return 'APP_DEBUG in production';
    }

    public function run(DoctorContext $context): CheckResult
    {
        if ($context->isProduction() && $context->debug()) {
            return CheckResult::error('APP_DEBUG is enabled in production. Set APP_DEBUG=false.');
        }

        return CheckResult::passed('APP_DEBUG is not enabled in production.');
    }
}
