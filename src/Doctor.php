<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor;

class Doctor
{
    /** @var array<string> */
    private array $customChecks = [];

    public function registerCheck(string $checkClass): void
    {
        $this->customChecks[] = $checkClass;
    }

    /**
     * @param  array<string>  $checkClasses
     */
    public function registerChecks(array $checkClasses): void
    {
        foreach ($checkClasses as $class) {
            $this->registerCheck($class);
        }
    }

    /**
     * @return array<string>
     */
    public function customChecks(): array
    {
        return $this->customChecks;
    }
}
