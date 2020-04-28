<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Event;

use Nette;
use SixtyEightPublishers;

final class NotificationExpirationHandler
{
	use Nette\SmartObject;

	/** @var \SixtyEightPublishers\NotificationBundle\Storage\StorageProvider  */
	private $provider;

	/** @var \Nette\Security\User  */
	private $user;

	/** @var bool  */
	private $alreadyCalled = FALSE;

	/**
	 * @param \SixtyEightPublishers\NotificationBundle\Storage\StorageProvider $provider
	 * @param \Nette\Security\User                                             $user
	 */
	public function __construct(SixtyEightPublishers\NotificationBundle\Storage\StorageProvider $provider, Nette\Security\User $user)
	{
		$this->provider = $provider;
		$this->user = $user;
	}

	/**
	 * @internal
	 *
	 * @param \Nette\Application\Application $application
	 * @param \Nette\Application\IResponse   $response
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function onResponse(Nette\Application\Application $application, Nette\Application\IResponse $response): void
	{
		if (TRUE === $this->alreadyCalled || 'cli' === PHP_SAPI || $response instanceof Nette\Application\Responses\ForwardResponse || $response instanceof Nette\Application\Responses\RedirectResponse) {
			return;
		}

		foreach ($this->provider->getAll($this->user->loggedIn ? (string) $this->user->getId() : NULL) as $storage) {
			foreach ($storage->all() as $notification) {
				$this->processNotification($notification, $storage);
			}
			$storage->onApplicationResponse();
		}

		$this->alreadyCalled = TRUE;
	}

	/**
	 * @param \SixtyEightPublishers\NotificationBundle\Notification\Notification $notification
	 * @param \SixtyEightPublishers\NotificationBundle\Storage\IStorage          $storage
	 *
	 * @return void
	 * @throws \Exception
	 */
	private function processNotification(SixtyEightPublishers\NotificationBundle\Notification\Notification $notification, SixtyEightPublishers\NotificationBundle\Storage\IStorage $storage): void
	{
		if ($notification->getNumberOfRemainingHiddenRequests() > 0) {
			$notification->hide($notification->getNumberOfRemainingHiddenRequests() - 1);
		}

		try {
			if ($notification->isExpired()) {
				$storage->remove($notification->getName());
			}
		} catch (SixtyEightPublishers\NotificationBundle\Exception\MissingNotificationException $e) {
			# theoretically it's not possible in this moment ... log it?
		}
	}
}
