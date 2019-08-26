<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Notification;

use Nette;
use SixtyEightPublishers;

class ActiveNotificationProvider
{
	use Nette\SmartObject;

	/** @var \SixtyEightPublishers\NotificationBundle\Storage\StorageProvider  */
	private $storageProvider;

	/**
	 * @param \SixtyEightPublishers\NotificationBundle\Storage\StorageProvider $storageProvider
	 */
	public function __construct(SixtyEightPublishers\NotificationBundle\Storage\StorageProvider $storageProvider)
	{
		$this->storageProvider = $storageProvider;
	}

	/**
	 * @param string|NULL $endpoint
	 * @param string|NULL $userId
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\Notification[]
	 */
	public function provide(?string $endpoint = NULL, ?string $userId = NULL): array
	{
		$notifications = [];
		foreach ($this->storageProvider->getAll($userId) as $k => $storage) {
			foreach ($storage->all() as $nk => $notification) {
				$notifications[$k . '__' . $nk] = $notification;
			}
		}

		return array_filter($notifications, static function (Notification $notification) use ($endpoint) {
			return ($endpoint === NULL || $notification->getEndpoint() === $endpoint) && $notification->canShow();
		});
	}
}
