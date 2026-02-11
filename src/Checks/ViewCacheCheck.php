<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;
use Illuminate\Contracts\Foundation\Application;

class ViewCacheCheck implements CheckInterface
{
    public function __construct(
        private readonly Application $app,
    ) {}

    public function key(): string
    {
        return 'view_cache';
    }

    public function title(): string
    {
        return 'View cache';
    }

    public function run(DoctorContext $context): CheckResult
    {
        $viewsPath = $this->app->storagePath('framework/views');
        $dirExists = is_dir($viewsPath);
        $hasFiles = $dirExists && count(glob($viewsPath.DIRECTORY_SEPARATOR.'*')) > 0;

        if ($context->isProduction()) {
            if (! $dirExists || ! $hasFiles) {
                return CheckResult::warning('Views are not cached/compiled yet. Run: php artisan view:cache');
            }
            return CheckResult::passed('View cache exists.');
        }

        if ($hasFiles) {
            return CheckResult::suggestion(
                'Compiled views exist. If debugging Blade output, run: php artisan view:clear'
            );
        }

        return CheckResult::passed('View cache not required for this environment.');
    }
}
