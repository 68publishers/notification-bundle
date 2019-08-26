<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Exception;

class InvalidStateException extends \Exception implements IException
{
	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Exception\InvalidStateException
	 */
	public static function userNotLoggedIn(): self
	{
		return new static('User is not logged in.');
	}
}
