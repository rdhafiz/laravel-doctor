<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Tests\Unit;

use Codevioso\LaravelDoctor\Checks\AppDebugInProductionCheck;
use Codevioso\LaravelDoctor\Support\DoctorContext;
use Codevioso\LaravelDoctor\Tests\TestCase;

class AppDebugInProductionCheckTest extends TestCase
{
    public function test_returns_error_when_debug_enabled_in_production(): void
    {
        $this->app['env'] = 'production';
        $this->app['config']->set('app.debug', true);

        $context = DoctorContext::fromApp($this->app);
        $check = new AppDebugInProductionCheck();
        $result = $check->run($context);

        $this->assertSame('error', $result->status);
        $this->assertStringContainsString('APP_DEBUG', $result->message);
        $this->assertStringContainsString('enabled in production', $result->message);
    }

    public function test_returns_passed_when_not_production(): void
    {
        $this->app['env'] = 'local';
        $this->app['config']->set('app.debug', true);

        $context = DoctorContext::fromApp($this->app);
        $check = new AppDebugInProductionCheck();
        $result = $check->run($context);

        $this->assertSame('passed', $result->status);
    }
}
