<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\DI;

use Nette;
use SixtyEightPublishers;

final class NotificationBundleExtension extends Nette\DI\CompilerExtension
{
	/**
	 * {@inheritDoc}
	 */
	public function getConfigSchema(): Nette\Schema\Schema
	{
		return Nette\Schema\Expect::structure([
			'storage' => Nette\Schema\Expect::anyOf(Nette\Schema\Expect::string(), Nette\Schema\Expect::type(Nette\DI\Definitions\Statement::class))->nullable()->before(static function ($def) {
				return is_string($def) ? new Nette\DI\Definitions\Statement($def) : $def;
			}),
			'templates' => Nette\Schema\Expect::structure([
				'flash_message' => Nette\Schema\Expect::string()->nullable(),
				'toastr' => Nette\Schema\Expect::string()->nullable(),
			]),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$provider = $builder->addDefinition($this->prefix('storage_provider'))
			->setType(SixtyEightPublishers\NotificationBundle\Storage\StorageProvider::class)
			->setAutowired(FALSE);

		if (NULL !== $this->config->storage) {
			$provider->setArguments([
				'storage' => $this->config->storage,
			]);
		}

		$builder->addFactoryDefinition($this->prefix('notifier_factory'))
			->setImplement(SixtyEightPublishers\NotificationBundle\INotifierFactory::class)
			->getResultDefinition()
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

		$flashMessageControl = $builder->addFactoryDefinition($this->prefix('flash_message_control'))
			->setImplement(SixtyEightPublishers\NotificationBundle\Control\FlashMessage\IFlashMessageControlFactory::class);

		$toastrControl = $builder->addFactoryDefinition($this->prefix('toastr_control'))
			->setImplement(SixtyEightPublishers\NotificationBundle\Control\Toastr\IToastrControlFactory::class);

		if (NULL !== $this->config->templates->flash_message) {
			$flashMessageControl->getResultDefinition()->addSetup('setFile', [
				$this->config->templates->flash_message,
			]);
		}

		if (NULL !== $this->config->templates->toastr) {
			$toastrControl->getResultDefinition()->addSetup('setFile', [
				$this->config->templates->toastr,
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
