<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Storage;

use SixtyEightPublishers;

interface IExtendedStorage extends IStorage
{
	/**
	 * @param string|NULL $prefix
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Storage\IStorage
	 */
	public function createNonExtendedStorage(?string $prefix = NULL): IStorage;

	/**
	 * @param \SixtyEightPublishers\NotificationBundle\Notification\Notification $notification
	 *
	 * @return void
	 */
	public function add(SixtyEightPublishers\NotificationBundle\Notification\Notification $notification): void;

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\Notification[]
	 */
	public function all(): array;

	/**
	 * Method is called during event Application::$onShutdown, but without arguments
	 *
	 * @return void
	 */
	public function onApplicationShutdown(): void;
}
