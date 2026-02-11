<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor;

use Codevioso\LaravelDoctor\Commands\DoctorCheckCommand;
use Codevioso\LaravelDoctor\Support\CheckRegistry;
use Codevioso\LaravelDoctor\Support\ConsoleReporter;
use Illuminate\Support\ServiceProvider;

class DoctorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/doctor.php', 'doctor');

        $this->app->singleton(Doctor::class, fn () => new Doctor());

        $this->app->singleton(CheckRegistry::class, function () {
            $configuredChecks = config('doctor.checks', []);
            $configuredCustomChecks = config('doctor.custom_checks', []);
            $doctor = $this->app->make(Doctor::class);
            $enabled = config('doctor.enabled', true);

            return new CheckRegistry(
                $configuredChecks,
                $configuredCustomChecks,
                $doctor,
                $enabled
            );
        });

        $this->app->singleton(ConsoleReporter::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/doctor.php' => config_path('doctor.php'),
            ], 'doctor-config');

            $this->commands([DoctorCheckCommand::class]);
        }
    }
}
