<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Control\FlashMessage;

use SixtyEightPublishers\NotificationBundle;

final class FlashMessageControl extends NotificationBundle\Control\AbstractNotificationControl
{
	/** @var string  */
	protected $endpoint = NotificationBundle\Notification\Notification::ENDPOINT_FLASH_MESSAGE;
}
