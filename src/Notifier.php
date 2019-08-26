<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle;

use Nette;

class Notifier
{
	use Nette\SmartObject;

	/** @var string|NULL  */
	private $prefix;

	/** @var \SixtyEightPublishers\NotificationBundle\Storage\StorageProvider  */
	private $provider;

	/** @var \Nette\Security\User  */
	private $user;

	/**
	 * @param string|NULL                                                      $prefix
	 * @param \SixtyEightPublishers\NotificationBundle\Storage\StorageProvider $provider
	 * @param \Nette\Security\User                                             $user
	 */
	public function __construct(?string $prefix, Storage\StorageProvider $provider, Nette\Security\User $user)
	{
		$this->prefix = $prefix;
		$this->provider = $provider;
		$this->user = $user;
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Storage\IStorage
	 */
	public function session(): Storage\IStorage
	{
		return $this->provider
			->getSessionStorage()
			->createNonExtendedStorage($this->prefix);
	}

	/**
	 * @param string|NULL $userId
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Storage\IStorage
	 * @throws \SixtyEightPublishers\NotificationBundle\Exception\InvalidStateException
	 */
	public function user(?string $userId = NULL): Storage\IStorage
	{
		if (NULL === $userId && (FALSE === $this->user->loggedIn || NULL === $this->user->getId())) {
			throw Exception\InvalidStateException::userNotLoggedIn();
		}

		return $this->provider
			->getUserStorage((string) ($userId ?? $this->user->getId()))
			->createNonExtendedStorage($this->prefix);
	}

	/***************** shortcuts *****************/

	/**
	 * @param string|\SixtyEightPublishers\NotificationBundle\Phrase\IPhrase $message
	 * @param int|string|array|NULL                                          $args
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function success($message, $args = NULL): Notification\NotificationBuilder
	{
		return $this->session()
			->createNotificationBuilder($this->resolveMessage($message, $args))
			->success();
	}

	/**
	 * @param string|\SixtyEightPublishers\NotificationBundle\Phrase\IPhrase $message
	 * @param int|string|array|NULL                                          $args
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function error($message, $args = NULL): Notification\NotificationBuilder
	{
		return $this->session()
			->createNotificationBuilder($this->resolveMessage($message, $args))
			->error();
	}

	/**
	 * @param string|\SixtyEightPublishers\NotificationBundle\Phrase\IPhrase $message
	 * @param int|string|array|NULL                                          $args
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function info($message, $args = NULL): Notification\NotificationBuilder
	{
		return $this->session()
			->createNotificationBuilder($this->resolveMessage($message, $args))
			->info();
	}

	/**
	 * @param string|\SixtyEightPublishers\NotificationBundle\Phrase\IPhrase $message
	 * @param int|string|array|NULL                                          $args
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function warning($message, $args = NULL): Notification\NotificationBuilder
	{
		return $this->session()
			->createNotificationBuilder($this->resolveMessage($message, $args))
			->warning();
	}

	/**
	 * @param string|\SixtyEightPublishers\NotificationBundle\Phrase\IPhrase $message
	 * @param mixed                                                          $args
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Phrase\IPhrase
	 */
	private function resolveMessage($message, $args): Phrase\IPhrase
	{
		if ($message instanceof Phrase\IPhrase) {
			return $message;
		}

		return new Phrase\Phrase((string) $message, $args);
	}
}
