<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Tests;

use Codevioso\LaravelDoctor\DoctorServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            DoctorServiceProvider::class,
        ];
    }
}
