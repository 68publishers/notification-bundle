<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Tests\Storage;

use Tester\Assert;
use Tester\TestCase;
use Nette\Caching\Cache;
use Nette\Caching\Storages\MemoryStorage;
use SixtyEightPublishers\NotificationBundle\Storage\UserStorage;

require __DIR__ . '/../bootstrap.php';

final class UserStorageTest extends TestCase
{
	public function testEmptyStorageShouldNotBeSaved(): void
	{
		$cacheStorage = $this->createCacheStorage();
		$storage = new UserStorage(new Cache($cacheStorage), '1');
		$storage->all(); # the first load saves an empty list (Cache::load() with a fallback)
		$cacheStorage->writes = 0;

		$storage->onApplicationResponse();

		Assert::same(0, $cacheStorage->writes);
	}

	public function testNotificationsShouldBeSavedAndRemoved(): void
	{
		$cacheStorage = $this->createCacheStorage();
		$cache = new Cache($cacheStorage);

		$storage = new UserStorage($cache, '1');
		$storage->createNotificationBuilder('Message', 'test')->scheduleToastr();
		$cacheStorage->writes = 0;
		$storage->onApplicationResponse();

		Assert::same(1, $cacheStorage->writes);

		$storage = new UserStorage($cache, '1');
		Assert::true($storage->has('test'));
		Assert::same('Message', $storage->get('test')->getMessage()->getMessage());

		$storage->remove('test');
		$cacheStorage->writes = 0;
		$storage->onApplicationResponse(); # all notifications removed
		$storage->onApplicationResponse(); # nothing changed

		Assert::same(1, $cacheStorage->writes);
		Assert::false((new UserStorage($cache, '1'))->has('test'));
	}

	private function createCacheStorage(): MemoryStorage
	{
		return new class extends MemoryStorage {
			public int $writes = 0;

			public function write(string $key, $data, array $dependencies): void
			{
				$this->writes++;
				parent::write($key, $data, $dependencies);
			}
		};
	}
}

(new UserStorageTest())->run();
