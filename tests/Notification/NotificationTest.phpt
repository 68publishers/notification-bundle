<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Tests\Notification;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\NotificationBundle\Phrase\Phrase;
use SixtyEightPublishers\NotificationBundle\Notification\Notification;

require __DIR__ . '/../bootstrap.php';

final class NotificationTest extends TestCase
{
	public function testTitleShouldBeKeptAfterSerialization(): void
	{
		$notification = new Notification('test', new Phrase('Message'), Notification::TYPE_SUCCESS, Notification::ENDPOINT_TOASTR, new Phrase('Title'), NULL, NULL, FALSE, 2);
		$notification->renderMessage();

		$unserialized = unserialize(serialize($notification));

		Assert::type(Notification::class, $unserialized);
		Assert::same('Title', $unserialized->renderTitle());
		Assert::same('Message', $unserialized->renderMessage());
		Assert::same(Notification::TYPE_SUCCESS, $unserialized->getType());
		Assert::same(Notification::ENDPOINT_TOASTR, $unserialized->getEndpoint());
		Assert::true($unserialized->isExpired()); # viewed twice, the max number of view is 2
	}
}

(new NotificationTest())->run();
