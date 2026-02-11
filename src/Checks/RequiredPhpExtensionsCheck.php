<?php

declare(strict_types=1);

namespace Codevioso\LaravelDoctor\Checks;

use Codevioso\LaravelDoctor\Contracts\CheckInterface;
use Codevioso\LaravelDoctor\Support\CheckResult;
use Codevioso\LaravelDoctor\Support\DoctorContext;

class RequiredPhpExtensionsCheck implements CheckInterface
{
    private const REQUIRED = [
        'openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo',
    ];

    private const RECOMMENDED = [
        'curl',
    ];

    public function key(): string
    {
        return 'required_php_extensions';
    }

    public function title(): string
    {
        return 'Required PHP extensions';
    }

    public function run(DoctorContext $context): CheckResult
    {
        $missingRequired = [];
        foreach (self::REQUIRED as $ext) {
            if (! extension_loaded($ext)) {
                $missingRequired[] = $ext;
            }
        }

        $missingRecommended = [];
        foreach (self::RECOMMENDED as $ext) {
            if (! extension_loaded($ext)) {
                $missingRecommended[] = $ext;
            }
        }

        if (count($missingRequired) > 0) {
            $list = implode(', ', $missingRequired);
            return CheckResult::error(
                "Missing required PHP extensions: {$list}. Install/enable them and restart PHP.",
                ['extensions' => $missingRequired]
            );
        }

        if (count($missingRecommended) > 0) {
            $list = implode(', ', $missingRecommended);
            return CheckResult::suggestion(
                "Missing recommended PHP extensions: {$list} (may impact common features).",
                ['extensions' => $missingRecommended]
            );
        }

        return CheckResult::passed('All required PHP extensions are available.');
    }
}
