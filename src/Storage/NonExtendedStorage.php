<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Storage;

use Nette;
use SixtyEightPublishers;

class NonExtendedStorage implements IStorage
{
	use Nette\SmartObject;

	/** @var \SixtyEightPublishers\NotificationBundle\Storage\IExtendedStorage  */
	private $storage;

	/** @var string|NULL  */
	private $prefix;

	/**
	 * NonExtendedStorage constructor.
	 *
	 * @param \SixtyEightPublishers\NotificationBundle\Storage\IExtendedStorage $storage
	 * @param string|NULL                                                       $prefix
	 */
	public function __construct(IExtendedStorage $storage, ?string $prefix = NULL)
	{
		$this->storage = $storage;
		$this->prefix = $prefix;
	}

	/******************* interface \SixtyEightPublishers\NotificationBundle\Storage\IExtendedStorage *******************/

	/**
	 * {@inheritdoc}
	 */
	public function createNotificationBuilder($message, ?string $name = NULL): SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	{
		$builder = $this->storage->createNotificationBuilder($message, $name);
		if (NULL !== $this->prefix) {
			$builder->setMessageDomain($this->prefix);
		}

		return $builder;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(string $name): SixtyEightPublishers\NotificationBundle\Notification\Notification
	{
		return $this->storage->get($name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function has(string $name): bool
	{
		return $this->storage->has($name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove(string $name): void
	{
		$this->storage->remove($name);
	}
}
