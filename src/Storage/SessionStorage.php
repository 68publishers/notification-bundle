<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Storage;

use Nette;
use SixtyEightPublishers;

class SessionStorage implements IExtendedStorage
{
	use Nette\SmartObject;

	public const SESSION_NAMESPACE = 'NotifierBundle.SessionStorage';

	/** @var \Nette\Http\SessionSection  */
	private $session;

	/**
	 * @param \Nette\Http\Session $session
	 */
	public function __construct(Nette\Http\Session $session)
	{
		$this->session = $session->getSection(self::SESSION_NAMESPACE);
	}

	/******************* interface \SixtyEightPublishers\NotificationBundle\Storage\IExtendedStorage *******************/

	/**
	 * {@inheritdoc}
	 */
	public function createNonExtendedStorage(?string $prefix = NULL): IStorage
	{
		return new NonExtendedStorage($this, $prefix);
	}

	/**
	 * {@inheritdoc}
	 */
	public function createNotificationBuilder($message, ?string $name = NULL): SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	{
		return new SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder(
			$this,
			$name ?? uniqid('', TRUE),
			$message
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function add(SixtyEightPublishers\NotificationBundle\Notification\Notification $notification): void
	{
		$this->session[$notification->getName()] = $notification;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(string $name): SixtyEightPublishers\NotificationBundle\Notification\Notification
	{
		if (!$this->has($name)) {
			throw SixtyEightPublishers\NotificationBundle\Exception\MissingNotificationException::error($name);
		}

		return $this->session->offsetGet($name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function has(string $name): bool
	{
		return $this->session->offsetExists($name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove(string $name): void
	{
		if (!$this->has($name)) {
			throw SixtyEightPublishers\NotificationBundle\Exception\MissingNotificationException::error($name);
		}
		$this->session->offsetUnset($name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function all(): array
	{
		return iterator_to_array($this->session);
	}

	/**
	 * {@inheritdoc}
	 */
	public function onApplicationShutdown(): void
	{
	}
}
