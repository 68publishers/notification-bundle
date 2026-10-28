> :warning: Warning! This package does not have active support, it exists only for the historical needs of the author.

# Notification Bundle

:bell: Nette extension for user's notifications, flash messages also with Toastr (JS library) support.

## Installation

The best way to install 68publishers/notification-bundle is using Composer:

```bash
composer require 68publishers/notification-bundle
```

Requirements: PHP `^8.3`, `nette/application` `^3.1.15`, [68publishers/smart-nette-component](https://github.com/68publishers/smart-nette-component) `^2.0` and [68publishers/translation-bridge](https://github.com/68publishers/translation-bridge) `^3.0` (the controls are translator-aware). The default templates require Latte 3.

then you can register extension into DIC:

```yaml
extensions:
    notification_bundle: SixtyEightPublishers\NotificationBundle\DI\NotificationBundleExtension
```

## Upgrade

### From 0.3 to 0.4

- The minimal PHP version is 8.3.
- `68publishers/smart-nette-component` `^2.0` and `68publishers/translation-bridge` `^3.0` are required, upgrade them together with this package.
- The class `SixtyEightPublishers\NotificationBundle\Phrase\KdybyPhrase` has been removed (`kdyby/translation` is abandoned), use `Phrase` or your own `IPhrase` implementation.
- The signatures use `Nette\Localization\Translator`, `Nette\Caching\Storage` and `Nette\Application\Response` instead of the deprecated `ITranslator`, `IStorage` and `IResponse`. These are aliases of the same interfaces, so existing `IPhrase` implementations keep working.
- Notifications stored in the session or cache before the upgrade are restored without changes.

## Contributing

Before committing any changes, don't forget to run

```bash
vendor/bin/php-cs-fixer fix -v --dry-run
```

and

```bash
vendor/bin/tester ./tests
```
