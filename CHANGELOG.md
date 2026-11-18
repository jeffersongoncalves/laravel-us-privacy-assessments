# Changelog

All notable changes to this project will be documented in this file.

## 1.1.0 - 2026-11-18

### Changed

- Requires the 2.0 of the suite core, which runs on [laravel-privacy](https://github.com/jeffersongoncalves/laravel-privacy), the engine shared by the privacy suites. No change to this package's API, config or tables.

## 1.0.0 - 2026-11-17

First release of laravel-us-privacy-assessments, data protection assessments under the US state privacy laws (on top of laravel-us-privacy and laravel-us-privacy-notices).

- Assessments with description, benefits, necessity and risks scored likelihood x impact before and after the safeguards (LINDDUN threats optional).
- Approval with a yearly review (`review_every`); editing an approved assessment sends it back to draft.
- `candidates()`: processing activities that need an assessment because their data is used for targeted advertising, sold, used for profiling or sensitive.
- `php artisan us-privacy:assessments [--json]`, ready to hand to an attorney general on request.

## [Unreleased]
