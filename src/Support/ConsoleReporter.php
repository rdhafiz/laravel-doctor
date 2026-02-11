<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Support;

use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleReporter
{
    /**
     * @param  array{errors: CheckResult[], warnings: CheckResult[], suggestions: CheckResult[], passed: CheckResult[]}  $groupedResults
     */
    public function render(SymfonyStyle $io, array $groupedResults, float $elapsedMs = 0): void
    {
        $io->title('Laravel Doctor Report');

        $errors = $groupedResults['errors'] ?? [];
        $warnings = $groupedResults['warnings'] ?? [];
        $suggestions = $groupedResults['suggestions'] ?? [];
        $passed = $groupedResults['passed'] ?? [];

        $summary = sprintf(
            'Errors: %d | Warnings: %d | Suggestions: %d | Passed: %d',
            count($errors),
            count($warnings),
            count($suggestions),
            count($passed)
        );
        $io->text($summary);
        $io->newLine();

        $this->renderSection($io, 'Errors', $errors, 'error');
        $this->renderSection($io, 'Warnings', $warnings, 'warning');
        $this->renderSection($io, 'Suggestions', $suggestions, 'note');
        $this->renderSection($io, 'Passed', $passed, 'success');

        if ($elapsedMs > 0) {
            $io->text(sprintf('<fg=gray>Completed in %d ms</>', (int) $elapsedMs));
        }

        if (count($errors) > 0) {
            $io->text('<fg=red>Fix errors first, then rerun.</>');
        } elseif (count($warnings) > 0) {
            $io->text('<fg=yellow>Address warnings for production readiness.</>');
        } else {
            $io->text('<fg=green>All checks look good.</>');
        }
    }

    /**
     * @param  CheckResult[]  $results
     */
    private function renderSection(SymfonyStyle $io, string $label, array $results, string $style): void
    {
        if (count($results) === 0) {
            $io->text(sprintf('<fg=%s>%s:</> None', $this->styleColor($style), $label));
            return;
        }

        $messages = [];
        foreach ($results as $r) {
            $messages[] = $r->message;
            if (count($r->details) > 0) {
                foreach ($r->details as $key => $val) {
                    $messages[] = '  '.$key.': '.(is_array($val) ? implode(', ', $val) : (string) $val);
                }
            }
        }

        match ($style) {
            'error' => $io->error($messages),
            'warning' => $io->warning($messages),
            'note' => $io->note($messages),
            'success' => $io->success($messages),
            default => $io->listing($messages),
        };
    }

    private function styleColor(string $style): string
    {
        return match ($style) {
            'error' => 'red',
            'warning' => 'yellow',
            'note' => 'blue',
            'success' => 'green',
            default => 'white',
        };
    }
}
