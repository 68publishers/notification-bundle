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

	/** @var \Nette\Application\IResponse|NULL */
	private $response;

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
	 */
	public function onResponse(Nette\Application\Application $application, Nette\Application\IResponse $response): void
	{
		$this->response = $response;
	}

	/**
	 * @internal
	 *
	 * @param \Nette\Application\Application $application
	 * @param \Throwable|NULL                $e
	 *
	 * @throws \SixtyEightPublishers\NotificationBundle\Exception\MissingNotificationException
	 */
	public function onShutdown(Nette\Application\Application $application, ?\Throwable $e = NULL): void
	{
		$isRedirect = $this->response instanceof Nette\Application\Responses\ForwardResponse
			|| $this->response instanceof Nette\Application\Responses\RedirectResponse;

		if (NULL === $e && FALSE === $this->alreadyCalled) {
			foreach ($this->provider->getAll($this->user->loggedIn ? (string) $this->user->getId() : NULL) as $storage) {
				foreach ($storage->all() as $notification) {
					if (FALSE === $isRedirect && $notification->getNumberOfRemainingHiddenRequests() > 0) {
						$notification->hide($notification->getNumberOfRemainingHiddenRequests() - 1);
					}

					/** @noinspection PhpUnhandledExceptionInspection */
					if ($notification->isExpired()) {
						$storage->remove($notification->getName());
					}
				}
				$storage->onApplicationShutdown();
			}
			$this->alreadyCalled = TRUE;
		}
	}
}
