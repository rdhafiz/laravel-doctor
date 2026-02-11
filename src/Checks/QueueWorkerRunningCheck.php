<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;

class QueueWorkerRunningCheck implements CheckInterface
{
    public function key(): string
    {
        return 'queue_worker_running';
    }

    public function title(): string
    {
        return 'Queue worker running';
    }

    public function run(DoctorContext $context): CheckResult
    {
        if (! $context->isProduction()) {
            return CheckResult::passed('Queue worker check not required for this environment.');
        }

        $driver = (string) $context->config('queue.default');
        if ($driver === 'sync') {
            return CheckResult::passed('Queue driver is sync; worker not required.');
        }

        if (! function_exists('shell_exec') || in_array('shell_exec', array_map('trim', explode(',', (string) ini_get('disable_functions'))), true)) {
            return CheckResult::suggestion(
                'Cannot verify queue worker process from PHP. Ensure a supervisor/systemd process runs: php artisan queue:work'
            );
        }

        $output = stripos(PHP_OS_FAMILY, 'Windows') !== false
            ? (string) @shell_exec('tasklist 2>nul')
            : (string) @shell_exec('ps aux 2>/dev/null | grep -v grep 2>/dev/null');

        $workerIndicators = ['queue:work', 'queue:listen', 'horizon'];
        $found = false;
        foreach ($workerIndicators as $indicator) {
            if ($output !== '' && stripos($output, $indicator) !== false) {
                $found = true;
                break;
            }
        }

        if ($found) {
            return CheckResult::passed('Queue worker process appears to be running.');
        }

        if (stripos(PHP_OS_FAMILY, 'Windows') !== false) {
            return CheckResult::suggestion(
                'Cannot reliably verify queue worker on Windows. Ensure a supervisor or process runs: php artisan queue:work'
            );
        }

        return CheckResult::warning(
            'Queue driver requires a worker, but none was detected. Start a worker/supervisor for queue:work.'
        );
    }
}
