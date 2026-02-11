<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Support;

use Codevioso\LaravelDoctor\Doctor;

class CheckRegistry
{
    /**
     * @param  array<string, bool>  $configuredChecks
     * @param  array<string>  $configuredCustomChecks
     */
    public function __construct(
        private readonly array $configuredChecks,
        private readonly array $configuredCustomChecks,
        private readonly Doctor $doctor,
        private readonly bool $enabled = true,
    ) {}

    /**
     * @return array<string>
     */
    public function enabled(): array
    {
        if (! $this->enabled) {
            return [];
        }

        $classes = [];

        foreach ($this->configuredChecks as $class => $value) {
            if ($value === true) {
                $classes[] = $class;
            }
        }

        foreach ($this->configuredCustomChecks as $class) {
            $classes[] = $class;
        }

        foreach ($this->doctor->customChecks() as $class) {
            $classes[] = $class;
        }

        return $this->normalize($classes);
    }

    /**
     * @return array<string>
     */
    public function all(): array
    {
        $classes = array_merge(
            array_keys($this->configuredChecks),
            $this->configuredCustomChecks,
            $this->doctor->customChecks()
        );
        return $this->normalize($classes);
    }

    /**
     * @return array<string>
     */
    public function disabled(): array
    {
        $disabled = array_filter($this->configuredChecks, fn (bool $v) => $v === false);
        return array_keys($disabled);
    }

    /**
     * @param  array<string>  $classes
     * @return array<string>
     */
    private function normalize(array $classes): array
    {
        $result = [];
        foreach ($classes as $class) {
            if (is_string($class) && class_exists($class) && ! in_array($class, $result, true)) {
                $result[] = $class;
            }
        }
        return $result;
    }
}
