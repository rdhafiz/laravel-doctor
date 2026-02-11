# Release Checklist

Use this checklist when preparing a new release.

## Pre-release

- [ ] Bump version in `CHANGELOG.md` under `[Unreleased]` → new version with date
- [ ] Run tests locally: `composer test`
- [ ] Ensure all CI checks pass on `main`/`master`

## Tagging

- [ ] Create git tag: `git tag v0.1.0` (use semantic versioning)
- [ ] Push tag: `git push origin v0.1.0`

## GitHub Release

- [ ] Go to GitHub → Releases → Draft a new release
- [ ] Choose the tag (e.g. `v0.1.0`)
- [ ] Title: `v0.1.0` or release name
- [ ] Copy changelog entry for this version into the description
- [ ] Publish release

## Packagist

- [ ] Ensure the package is submitted to [packagist.org](https://packagist.org) (if first release)
- [ ] Configure Packagist to auto-update from GitHub (or trigger manual update)
- [ ] Verify the new version appears on Packagist

## Post-release

- [ ] Update any dependent projects or documentation that reference the version
