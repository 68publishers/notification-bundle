<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Storage;

use Nette;
use SixtyEightPublishers;

class UserStorage implements IExtendedStorage
{
	use Nette\SmartObject;

	public const 	CACHE_NAMESPACE = 'NotifierBundle.NetteCacheStorage',
					CACHE_KEY = 'notifications';

	/** @var \Nette\Caching\Cache  */
	private $cache;

	/** @var string  */
	private $cacheKey;

	/** @var NULL|\SixtyEightPublishers\NotificationBundle\Notification\Notification[] */
	private $loaded;

	/** @var NULL|int */
	private $countOnLoad;

	/**
	 * @param \Nette\Caching\Cache $cache
	 * @param string               $userId
	 */
	public function __construct(Nette\Caching\Cache $cache, string $userId)
	{
		$this->cache = $cache;
		$this->cacheKey = sprintf('%s/%s', self::CACHE_KEY, $userId);
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\Notification[]
	 */
	private function getNotifications(): array
	{
		if (NULL === $this->loaded) {
			$this->loaded = (array) $this->cache->load($this->cacheKey, static function () {
				return [];
			});
			$this->countOnLoad = count($this->loaded);
		}

		return $this->loaded;
	}

	/**
	 * @param \SixtyEightPublishers\NotificationBundle\Notification\Notification[] $notifications
	 *
	 * @return void
	 * @throws \Throwable
	 */
	private function save(array $notifications): void
	{
		$this->loaded = $this->cache->save($this->cacheKey, $notifications);
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
		$notifications = $this->getNotifications();
		$notifications[$notification->getName()] = $notification;
		$this->loaded = $notifications;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(string $name): SixtyEightPublishers\NotificationBundle\Notification\Notification
	{
		if (!$this->has($name)) {
			throw SixtyEightPublishers\NotificationBundle\Exception\MissingNotificationException::error($name);
		}

		return $this->getNotifications()[$name];
	}

	/**
	 * {@inheritdoc}
	 */
	public function has(string $name): bool
	{
		return array_key_exists($name, $this->getNotifications());
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove(string $name): void
	{
		if (!$this->has($name)) {
			throw SixtyEightPublishers\NotificationBundle\Exception\MissingNotificationException::error($name);
		}
		$notifications = $this->getNotifications();
		unset($notifications[$name]);
		$this->loaded = $notifications;
	}

	/**
	 * {@inheritdoc}
	 */
	public function all(): array
	{
		return $this->getNotifications();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Throwable
	 */
	public function onApplicationResponse(): void
	{
		if (count($count = $this->getNotifications()) || $count !== $this->countOnLoad) {
			$this->save($this->getNotifications());
		}
	}
}
