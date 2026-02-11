<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;
use Illuminate\Contracts\Foundation\Application;

class ComposerAutoloadOptimizedCheck implements CheckInterface
{
    public function __construct(
        private readonly Application $app,
    ) {}

    public function key(): string
    {
        return 'composer_autoload_optimized';
    }

    public function title(): string
    {
        return 'Composer autoload optimized';
    }

    public function run(DoctorContext $context): CheckResult
    {
        if (! $context->isProduction()) {
            return CheckResult::passed('Composer autoload optimization not required for this environment.');
        }

        $vendorPath = $this->app->basePath('vendor');
        if (! is_dir($vendorPath)) {
            return CheckResult::suggestion('Vendor directory not found. Run composer install.');
        }

        $classmapPath = $vendorPath.DIRECTORY_SEPARATOR.'composer'.DIRECTORY_SEPARATOR.'autoload_classmap.php';
        if (! file_exists($classmapPath)) {
            return CheckResult::warning(
                'Composer autoload classmap not present. Run: composer install --optimize-autoloader --no-dev'
            );
        }

        $content = file_get_contents($classmapPath);
        if ($content === false || strpos($content, 'return array') === false) {
            return CheckResult::warning(
                'Composer autoload classmap appears empty. Run: composer install --optimize-autoloader --no-dev'
            );
        }

        $installedPath = $vendorPath.DIRECTORY_SEPARATOR.'composer'.DIRECTORY_SEPARATOR.'installed.json';
        if (file_exists($installedPath)) {
            $json = @file_get_contents($installedPath);
            if ($json !== false && str_contains($json, '"dev": true')) {
                return CheckResult::warning(
                    'Dev dependencies appear present in production. Run: composer install --no-dev --optimize-autoloader'
                );
            }
        }

        return CheckResult::passed('Composer autoload appears optimized.');
    }
}
