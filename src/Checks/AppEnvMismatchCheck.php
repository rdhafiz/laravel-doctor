<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;

class AppEnvMismatchCheck implements CheckInterface
{
    public function key(): string
    {
        return 'app_env_mismatch';
    }

    public function title(): string
    {
        return 'APP_ENV mismatch';
    }

    public function run(DoctorContext $context): CheckResult
    {
        $configEnv = (string) $context->config('app.env');
        $rawEnv = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? null) ?: ($_SERVER['APP_ENV'] ?? null);

        if ($rawEnv === null || $rawEnv === false) {
            return CheckResult::suggestion('APP_ENV is not set in the environment. Ensure it is defined for deployments.');
        }

        $rawEnv = (string) $rawEnv;
        if ($configEnv !== $rawEnv) {
            return CheckResult::warning(
                "APP_ENV mismatch: config(app.env)='{$configEnv}' but environment APP_ENV='{$rawEnv}'. Clear/rebuild config cache."
            );
        }

        return CheckResult::passed('APP_ENV matches the environment.');
    }
}
