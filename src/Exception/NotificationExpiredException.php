<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Exception;

class NotificationExpiredException extends \Exception implements IException
{
	/**
	 * @param string $name
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Exception\NotificationExpiredException
	 */
	public static function error(string $name): self
	{
		return new static(sprintf(
			'Notification %s is expired.',
			$name
		));
	}
}
