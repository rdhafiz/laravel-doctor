<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;
use Illuminate\Contracts\Foundation\Application;

class StorageSymlinkCheck implements CheckInterface
{
    public function __construct(
        private readonly Application $app,
    ) {}

    public function key(): string
    {
        return 'storage_symlink';
    }

    public function title(): string
    {
        return 'Storage symlink';
    }

    public function run(DoctorContext $context): CheckResult
    {
        $link = $this->app->publicPath('storage');
        $target = $this->app->storagePath('app/public');

        if (! is_dir($target)) {
            return CheckResult::suggestion(
                'storage/app/public is missing. Ensure storage directories exist.'
            );
        }

        if (! file_exists($link) || ! is_link($link)) {
            if ($context->isProduction()) {
                return CheckResult::warning('Storage symlink missing. Run: php artisan storage:link');
            }
            return CheckResult::suggestion('Storage symlink missing. Run: php artisan storage:link');
        }

        $resolved = realpath($link);
        $expectedTarget = realpath($target);
        if ($resolved !== false && $expectedTarget !== false && $resolved !== $expectedTarget) {
            return CheckResult::warning(
                'Storage symlink points to unexpected location. Recreate with: php artisan storage:link'
            );
        }

        return CheckResult::passed('Storage symlink exists.');
    }
}
