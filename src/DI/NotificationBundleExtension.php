<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\DI;

use Nette;
use SixtyEightPublishers;

final class NotificationBundleExtension extends Nette\DI\CompilerExtension
{
	/** @var array  */
	private $defaults = [
		'storage' => NULL,
		'templates' => [
			'flash_message' => NULL,
			'toastr' => NULL,
		],
	];

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Nette\Utils\AssertionException
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$provider = $builder->addDefinition($this->prefix('storage_provider'))
			->setType(SixtyEightPublishers\NotificationBundle\Storage\StorageProvider::class)
			->setAutowired(FALSE);

		Nette\Utils\Validators::assertField($config, 'storage', sprintf('null|string|%s', Nette\DI\Statement::class));

		if (NULL !== ($storage = $config['storage'])) {
			$provider->setArguments([
				'storage' => $storage instanceof Nette\DI\Statement ? $storage : new Nette\DI\Statement($storage),
			]);
		}

		$builder->addDefinition($this->prefix('notifier_factory'))
			->setImplement(SixtyEightPublishers\NotificationBundle\INotifierFactory::class)
			->setArguments([
				'provider' => $provider,
			]);

		$builder->addDefinition($this->prefix('active_notification_provider'))
			->setType(SixtyEightPublishers\NotificationBundle\Notification\ActiveNotificationProvider::class)
			->setArguments([
				'storageProvider' => $provider,
			]);

		$builder->addDefinition($this->prefix('notification_expiration_handler'))
			->setType(SixtyEightPublishers\NotificationBundle\Event\NotificationExpirationHandler::class)
			->setArguments([
				'provider' => $provider,
			]);

		$flashMessageControl = $builder->addDefinition($this->prefix('flash_message_control'))
			->setImplement(SixtyEightPublishers\NotificationBundle\Control\FlashMessage\IFlashMessageControlFactory::class);

		$toastrControl = $builder->addDefinition($this->prefix('toastr_control'))
			->setImplement(SixtyEightPublishers\NotificationBundle\Control\Toastr\IToastrControlFactory::class);

		if (NULL !== $config['templates']['flash_message']) {
			$flashMessageControl->addSetup('setFile', [
				$config['templates']['flash_message'],
			]);
		}

		if (NULL !== $config['templates']['toastr']) {
			$toastrControl->addSetup('setFile', [
				$config['templates']['toastr'],
			]);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$application = $builder->getDefinition('application');

		$application->addSetup('$service->onResponse[] = [?, ?]', [
			'@' . $this->prefix('notification_expiration_handler'),
			'onResponse',
		]);
	}
}
