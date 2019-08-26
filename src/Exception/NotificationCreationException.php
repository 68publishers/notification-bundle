<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Exception;

class NotificationCreationException extends \Exception implements IException
{
	/**
	 * @param \DateTime $dateTime
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Exception\NotificationCreationException
	 */
	public static function timeInPast(\DateTime $dateTime): self
	{
		return new static(sprintf(
			'DateTime %s is in past and it\'s not allowed.',
			$dateTime->format('Y-m-d H:i:s')
		));
	}

	/**
	 * @param \DateTime $from
	 * @param \DateTime $to
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Exception\NotificationCreationException
	 */
	public static function chronologicalError(\DateTime $from, \DateTime $to): self
	{
		return new static(sprintf(
			'Chronological error between dates %s and %s',
			$from->format('Y-m-d H:i:s'),
			$to->format('Y-m-d H:i:s')
		));
	}
}
