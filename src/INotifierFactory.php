<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle;

interface INotifierFactory
{
	/**
	 * @param NULL|string $prefix
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notifier
	 */
	public function create(?string $prefix = NULL): Notifier;
}
