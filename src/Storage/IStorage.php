<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Storage;

use SixtyEightPublishers;

interface IStorage
{
	/**
	 * @param string|\SixtyEightPublishers\NotificationBundle\Phrase\IPhrase $message
	 * @param string|NULL                                                    $name
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function createNotificationBuilder($message, ?string $name = NULL): SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder;

	/**
	 * @param string $name
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\Notification
	 * @throws \SixtyEightPublishers\NotificationBundle\Exception\MissingNotificationException
	 */
	public function get(string $name): SixtyEightPublishers\NotificationBundle\Notification\Notification;

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function has(string $name): bool;

	/**
	 * @param string $name
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\NotificationBundle\Exception\MissingNotificationException
	 */
	public function remove(string $name): void;
}
