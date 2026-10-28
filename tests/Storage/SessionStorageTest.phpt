<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Tests\Storage;

use Tester\Assert;
use Tester\TestCase;
use Nette\Http\Request;
use Nette\Http\Session;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use SixtyEightPublishers\NotificationBundle\Storage\SessionStorage;
use SixtyEightPublishers\NotificationBundle\Notification\Notification;
use SixtyEightPublishers\NotificationBundle\Exception\MissingNotificationException;

require __DIR__ . '/../bootstrap.php';

final class SessionStorageTest extends TestCase
{
	public function testNotificationsShouldBeStoredInSession(): void
	{
		$storage = new SessionStorage(new Session(new Request(new UrlScript('http://localhost/')), new Response()));

		Assert::false($storage->has('test'));

		$notification = $storage->createNotificationBuilder('Message', 'test')->scheduleFlashMessage();

		Assert::true($storage->has('test'));
		Assert::same($notification, $storage->get('test'));
		Assert::same(['test' => $notification], $storage->all());
		Assert::same(Notification::ENDPOINT_FLASH_MESSAGE, $storage->get('test')->getEndpoint());

		$storage->remove('test');

		Assert::false($storage->has('test'));
		Assert::same([], $storage->all());
		Assert::exception(static fn () => $storage->get('test'), MissingNotificationException::class);
		Assert::exception(static fn () => $storage->remove('test'), MissingNotificationException::class);
	}
}

(new SessionStorageTest())->run();
