<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;
use Illuminate\Contracts\Foundation\Application;

class FilePermissionsCheck implements CheckInterface
{
    public function __construct(
        private readonly Application $app,
    ) {}

    public function key(): string
    {
        return 'file_permissions';
    }

    public function title(): string
    {
        return 'File permissions (storage & cache)';
    }

    public function run(DoctorContext $context): CheckResult
    {
        $basePath = $this->app->basePath();
        $directories = [
            'storage' => $basePath.DIRECTORY_SEPARATOR.'storage',
            'storage/framework' => $basePath.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'framework',
            'storage/logs' => $basePath.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'logs',
            'bootstrap/cache' => $basePath.DIRECTORY_SEPARATOR.'bootstrap'.DIRECTORY_SEPARATOR.'cache',
        ];

        $missing = [];
        $notWritable = [];

        foreach ($directories as $label => $path) {
            if (! is_dir($path)) {
                $missing[] = $label;
                continue;
            }
            if (! is_writable($path)) {
                $notWritable[] = $label;
                continue;
            }
            if (! $this->canWrite($path)) {
                $notWritable[] = $label;
            }
        }

        if (count($missing) > 0) {
            $list = implode(', ', $missing);
            $message = "Missing directory: {$list}. Ensure you deployed storage and bootstrap/cache.";
            if ($context->isProduction()) {
                return CheckResult::warning($message, ['missing' => $missing]);
            }
            return CheckResult::suggestion($message, ['missing' => $missing]);
        }

        if (count($notWritable) > 0) {
            $list = implode(', ', $notWritable);
            $message = "Directory is not writable: {$list}. Fix permissions/ownership for the web/PHP user.";
            if ($context->isProduction()) {
                return CheckResult::error($message, ['not_writable' => $notWritable]);
            }
            return CheckResult::warning($message, ['not_writable' => $notWritable]);
        }

        return CheckResult::passed('Storage and bootstrap/cache directories are writable.');
    }

    private function canWrite(string $path): bool
    {
        $testFile = $path.DIRECTORY_SEPARATOR.'.doctor_write_test';
        $written = @file_put_contents($testFile, '1') !== false;
        if ($written) {
            @unlink($testFile);
        }
        return $written;
    }
}
