<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Support;

final class CheckResult
{
    public function __construct(
        public readonly string $status,
        public readonly string $message,
        public readonly array $details = [],
    ) {}

    public static function error(string $message, array $details = []): self
    {
        return new self('error', $message, $details);
    }

    public static function warning(string $message, array $details = []): self
    {
        return new self('warning', $message, $details);
    }

    public static function suggestion(string $message, array $details = []): self
    {
        return new self('suggestion', $message, $details);
    }

    public static function passed(string $message = 'OK', array $details = []): self
    {
        return new self('passed', $message, $details);
    }
}
