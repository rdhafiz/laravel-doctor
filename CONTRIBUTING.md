# Contributing to Laravel Doctor

Thank you for considering contributing.

## Running Tests

```bash
composer install
composer test
```

Or with PHPUnit directly:

```bash
vendor/bin/phpunit --testdox
```

## Adding a Check

1. **Create a class** in `src/Checks/` implementing `Codevioso\LaravelDoctor\Contracts\CheckInterface`:
   - `key(): string` — unique identifier (e.g. `my_check`)
   - `title(): string` — human-readable title
   - `run(DoctorContext $context): CheckResult` — run the check, return `CheckResult::error()`, `warning()`, `suggestion()`, or `passed()`

2. **Enable in config** — Add `YourCheck::class => true` to `config/doctor.php` under `checks`.

3. **Add tests** — Create a test in `tests/Unit/` and assert expected status/message for key scenarios.

## Coding Style

- Use `declare(strict_types=1);`
- Follow PSR-12 for formatting
- Keep checks small and focused; use `DoctorContext` for config reads
- Prefer warnings in production, suggestions elsewhere
