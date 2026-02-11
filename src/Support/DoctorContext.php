<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Support;

use Illuminate\Contracts\Foundation\Application;

final class DoctorContext
{
    public function __construct(
        private readonly Application $app,
    ) {}

    public static function fromApp(Application $app): self
    {
        return new self($app);
    }

    public function env(): string
    {
        return (string) $this->app->environment();
    }

    public function debug(): bool
    {
        return (bool) config('app.debug', false);
    }

    public function isProduction(): bool
    {
        return $this->env() === 'production';
    }

    public function config(string $key, mixed $default = null): mixed
    {
        return config($key, $default);
    }
}
