<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Storage;

use Nette;

class StorageProvider
{
	use Nette\SmartObject;

	public const CACHE_NAMESPACE = 'NotifierBundle.UserStorage';

	/** @var \Nette\Http\Session  */
	private $session;

	/** @var \Nette\Caching\IStorage  */
	private $storage;

	/** @var NULL|\Nette\Caching\Cache */
	private $cache;

	/** @var \SixtyEightPublishers\NotificationBundle\Storage\IExtendedStorage[] */
	private $instances = [];

	/**
	 * @param \Nette\Http\Session     $session
	 * @param \Nette\Caching\IStorage $storage
	 */
	public function __construct(Nette\Http\Session $session, Nette\Caching\IStorage $storage)
	{
		$this->session = $session;
		$this->storage = $storage;
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Storage\IExtendedStorage
	 */
	public function getSessionStorage(): IExtendedStorage
	{
		if (!array_key_exists('session', $this->instances)) {
			$this->instances['session'] = new SessionStorage($this->session);
		}

		return $this->instances['session'];
	}

	/**
	 * @param string $userId
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Storage\IExtendedStorage
	 */
	public function getUserStorage(string $userId): IExtendedStorage
	{
		if (!array_key_exists($key = 'user_' . $userId, $this->instances)) {
			$this->instances[$key] = new UserStorage($this->getCache(), $userId);
		}

		return $this->instances[$key];
	}

	/**
	 * @param null|string $userId
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Storage\IExtendedStorage[]
	 */
	public function getAll(?string $userId = NULL): array
	{
		$storage = [
			$this->getSessionStorage(),
		];
		if (NULL !== $userId) {
			$storage[] = $this->getUserStorage($userId);
		}

		return $storage;
	}

	/**
	 * @return \Nette\Caching\Cache
	 */
	private function getCache(): Nette\Caching\Cache
	{
		if (NULL === $this->cache) {
			$this->cache = new Nette\Caching\Cache($this->storage, self::CACHE_NAMESPACE);
		}

		return $this->cache;
	}
}
