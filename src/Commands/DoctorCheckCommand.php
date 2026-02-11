<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Commands;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckRegistry;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\ConsoleReporter;
use Codevioso\LaravelDoctor\Support\DoctorContext;
use Illuminate\Console\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

class DoctorCheckCommand extends Command
{
    protected $signature = 'doctor:check
                            {--demo : Print a sample report (for screenshots)}
                            {--only= : Comma-separated keys or class names to run}
                            {--skip= : Comma-separated keys or class names to skip}';

    protected $description = 'Scan the current Laravel app for common config/perf issues.';

    public function handle(CheckRegistry $registry, ConsoleReporter $reporter): int
    {
        $start = microtime(true);

        if ($this->option('demo')) {
            $grouped = [
                'errors' => [
                    CheckResult::error('APP_DEBUG is enabled in production. Set APP_DEBUG=false.'),
                    CheckResult::error('Storage symlink does not exist. Run: php artisan storage:link'),
                ],
                'warnings' => [
                    CheckResult::warning('Cache driver is file in production. Use redis/memcached/database for better performance.'),
                    CheckResult::warning('Config is not cached. Run: php artisan config:cache'),
                    CheckResult::warning('Session driver is file in production. Consider redis/database for scalability.'),
                ],
                'suggestions' => [
                    CheckResult::suggestion('Cannot verify queue worker process from PHP. Ensure a supervisor/systemd process runs: php artisan queue:work'),
                    CheckResult::suggestion('Composer autoload could be optimized. Run: composer install --optimize-autoloader --no-dev'),
                ],
                'passed' => [
                    CheckResult::passed('APP_ENV matches the environment.'),
                    CheckResult::passed('Route cache not required for this environment.'),
                    CheckResult::passed('View cache not required for this environment.'),
                    CheckResult::passed('Queue driver is not sync in production.'),
                    CheckResult::passed('Log channel is appropriate for environment.'),
                    CheckResult::passed('All required PHP extensions are available.'),
                    CheckResult::passed('Storage and bootstrap/cache directories are writable.'),
                ],
            ];
            $io = new SymfonyStyle($this->input, $this->output);
            $reporter->render($io, $grouped, 45);
            return self::SUCCESS;
        }

        $enabled = $registry->enabled();

        if (count($enabled) === 0 && config('doctor.enabled', true) === false) {
            $this->line('Laravel Doctor is disabled (doctor.enabled=false).');
            return self::SUCCESS;
        }

        $enabled = $this->filterChecks($enabled);

        if (count($enabled) === 0) {
            $this->warn('No checks matched the filter. Adjust --only or --skip.');
            return self::SUCCESS;
        }

        $io = new SymfonyStyle($this->input, $this->output);
        $context = DoctorContext::fromApp($this->laravel);

        $grouped = [
            'errors' => [],
            'warnings' => [],
            'suggestions' => [],
            'passed' => [],
        ];

        foreach ($enabled as $class) {
            /** @var CheckInterface $check */
            $check = $this->laravel->make($class);
            $result = $check->run($context);
            $grouped[$result->status][] = $result;
        }

        $elapsed = round((microtime(true) - $start) * 1000);
        $reporter->render($io, $grouped, $elapsed);

        return count($grouped['errors']) > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @param  array<string>  $classes
     * @return array<string>
     */
    private function filterChecks(array $classes): array
    {
        $only = $this->parseOption('only');
        $skip = $this->parseOption('skip');

        if (count($only) === 0 && count($skip) === 0) {
            return $classes;
        }

        $result = [];
        foreach ($classes as $class) {
            /** @var CheckInterface $check */
            $check = $this->laravel->make($class);
            $identifiers = [
                $class,
                class_basename($class),
                $check->key(),
            ];

            $matchesOnly = count($only) === 0 || $this->matchesAny($identifiers, $only);
            $matchesSkip = count($skip) > 0 && $this->matchesAny($identifiers, $skip);

            if ($matchesOnly && ! $matchesSkip) {
                $result[] = $class;
            }
        }

        return $result;
    }

    /**
     * @return array<string>
     */
    private function parseOption(string $name): array
    {
        $value = $this->option($name);
        if (! is_string($value) || trim($value) === '') {
            return [];
        }
        return array_map('trim', explode(',', $value));
    }

    /**
     * @param  array<string>  $identifiers
     * @param  array<string>  $tokens
     */
    private function matchesAny(array $identifiers, array $tokens): bool
    {
        foreach ($tokens as $token) {
            foreach ($identifiers as $id) {
                if (strcasecmp($id, $token) === 0) {
                    return true;
                }
            }
        }
        return false;
    }
}
