<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Tests\Unit;

use Codevioso\LaravelDoctor\Support\CheckRegistry;
use Codevioso\LaravelDoctor\Tests\TestCase;

class DisabledDoctorTest extends TestCase
{
    public function test_registry_returns_empty_when_doctor_disabled(): void
    {
        $this->app['config']->set('doctor.enabled', false);

        $registry = $this->app->make(CheckRegistry::class);

        $this->assertSame([], $registry->enabled());
    }

    public function test_command_outputs_disabled_message_when_doctor_disabled(): void
    {
        $this->app['config']->set('doctor.enabled', false);

        $this->artisan('doctor:check')
            ->expectsOutput('Laravel Doctor is disabled (doctor.enabled=false).')
            ->assertExitCode(0);
    }
}
