# Notification Bundle

:bell: Nette extension for user's notifications, flash messages also with Toastr (JS library) support.

## Installation

The best way to install 68publishers/notification-bundle is using Composer:

```bash
composer require 68publishers/notification-bundle
```

then you can register extension into DIC:

```yaml
extensions:
    notification_bundle: SixtyEightPublishers\NotificationBundle\DI\NotificationBundleExtension
```

## Contributing

Before committing any changes, don't forget to run

```bash
vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v --dry-run
```

and

```bash
vendor/bin/tester ./tests
```
