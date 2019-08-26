<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Exception;

class MissingNotificationException extends \Exception implements IException
{
	/**
	 * @param string $name
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Exception\MissingNotificationException
	 */
	public static function error(string $name): self
	{
		return new static(sprintf(
			'Notification %s is not accessible in storage',
			$name
		));
	}
}
