<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Control\Toastr;

use SixtyEightPublishers;

final class ToastrControl extends SixtyEightPublishers\NotificationBundle\Control\AbstractNotificationControl
{
	/** @var string  */
	protected $endpoint = SixtyEightPublishers\NotificationBundle\Notification\Notification::ENDPOINT_TOASTR;
}
