# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.4.0] - 2026-10-28

### Added

- Added support for PHP 8.4 and 8.5, tested with `nette/application` 3.1 – 3.3 and `latte/latte` 3.0 – 3.1.
- Added tests and GitHub Actions (coding style, tests on PHP 8.3, 8.4 and 8.5 with highest dependencies and on PHP 8.3 with lowest dependencies).

### Changed

- Minimal PHP version is 8.3 (BC break).
- Required `68publishers/smart-nette-component` `^2.0` and `68publishers/translation-bridge` `^3.0` (BC break, see their changelogs).
- Minimal versions of `nette/application` (`^3.1.15`), `nette/caching` (`^3.1`), `nette/di` (`^3.1.3`), `nette/http` (`^3.1`), `nette/security` (`^3.1.4`) and `nette/utils` (`^3.2.10 || ^4.0.1`), older releases cannot be installed on PHP 8.3 or do not work with Latte 3 there.
- The deprecated interfaces `Nette\Localization\ITranslator`, `Nette\Caching\IStorage` and `Nette\Application\IResponse` are replaced with `Translator`, `Storage` and `Response` in the signatures (they are aliases, existing implementations stay compatible).
- `SessionStorage` uses `SessionSection::get()`, `set()` and `remove()` instead of the deprecated array access.
- `NotificationBundleExtension` looks up the application service by its type instead of the name `application`.
- PHP-CS-Fixer 3 (`.php-cs-fixer.dist.php` replaces `.php_cs.dist`), `nette/tester` 2.6+, `roave/security-advisories` `dev-latest`, docker dev environment with PHP 8.3, 8.4 and 8.5.

### Removed

- Removed `SixtyEightPublishers\NotificationBundle\Phrase\KdybyPhrase` (BC break), `kdyby/translation` is abandoned and does not run on PHP 8.
- Removed Travis CI configuration.

### Fixed

- The title of a notification was lost after the notification was serialized (e.g. stored in the session before a redirect).
- The default flash message template used an undefined variable `$flash` instead of `$notification` when a notification had a title.
- `UserStorage` saved the notifications into the cache on every response, even if there were no notifications.

## [0.3.1] - 2024-08-06

### Changed

- Allowed `nette/utils` v4.

## [0.3.0] - 2023-01-04

### Added

- Added Nette 3 and PHP 8.1 into `composer.json` and a support warning into the README.

## [0.2.1] - 2021-01-29

### Fixed

- Fixed user deprecated error in method `UserStorage::save()`.

## [0.2] - 2020-11-30

### Added

- Added Nette 3 support.

## [0.1.3] - 2020-05-27

### Fixed

- Fixed PHP User Deprecated: Inside macro `{var type = $notification->getType()}` should be `type` replaced with `$type`.

## [0.1.2] - 2020-04-28

### Fixed

- States of storages aren't updated in CLI mode.

## [0.1.1] - 2019-11-28

### Fixed

- Fixed problems with late session starting.

## [0.1] - 2019-08-26

### Added

- First release (Nette 2.4).

[Unreleased]: https://github.com/68publishers/notification-bundle/compare/v0.4.0...HEAD
[0.4.0]: https://github.com/68publishers/notification-bundle/compare/v0.3.1...v0.4.0
[0.3.1]: https://github.com/68publishers/notification-bundle/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/68publishers/notification-bundle/compare/v0.2.1...v0.3.0
[0.2.1]: https://github.com/68publishers/notification-bundle/compare/v0.2...v0.2.1
[0.2]: https://github.com/68publishers/notification-bundle/compare/v0.1.3...v0.2
[0.1.3]: https://github.com/68publishers/notification-bundle/compare/v0.1.2...v0.1.3
[0.1.2]: https://github.com/68publishers/notification-bundle/compare/v0.1.1...v0.1.2
[0.1.1]: https://github.com/68publishers/notification-bundle/compare/v0.1...v0.1.1
[0.1]: https://github.com/68publishers/notification-bundle/releases/tag/v0.1
