<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;

class LogChannelSingleHighTrafficCheck implements CheckInterface
{
    public function key(): string
    {
        return 'log_channel_single_high_traffic';
    }

    public function title(): string
    {
        return 'Log channel single in high-traffic';
    }

    public function run(DoctorContext $context): CheckResult
    {
        $highTraffic = (bool) $context->config('doctor.environment.high_traffic', false);
        $channel = (string) $context->config('logging.default');

        if ($highTraffic && $channel === 'single') {
            return CheckResult::warning(
                "Log channel is 'single' in high-traffic mode. Consider 'daily' or centralized logging to avoid huge files."
            );
        }

        return CheckResult::passed('Log channel is appropriate for environment.');
    }
}
