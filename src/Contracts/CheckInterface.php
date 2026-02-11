<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Contracts;

use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;

interface CheckInterface
{
    public function key(): string;

    public function title(): string;

    public function run(DoctorContext $context): CheckResult;
}
