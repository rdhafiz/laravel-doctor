# Changelog

All notable changes to this project will be documented in this file.

## [0.1.0] - 2025-02-10

### Added

- Initial release
- `doctor:check` command to scan Laravel apps for config/perf issues
- 14 built-in checks:
  - APP_DEBUG in production
  - APP_ENV mismatch
  - Config cache
  - Route cache
  - View cache
  - Queue driver sync in production
  - Cache driver file in production
  - Session driver file in production
  - Storage symlink
  - Queue worker running
  - Log channel single in high-traffic
  - Composer autoload optimized
  - Required PHP extensions
  - File permissions (storage & bootstrap/cache)
- Configurable checks via `config/doctor.php`
- Custom checks via `doctor.custom_checks` or `Doctor::registerCheck()`
- `--only` and `--skip` options for selective runs
- Execution time and summary in report output
