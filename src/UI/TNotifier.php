<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\UI;

use SixtyEightPublishers;

/**
 * Use this trait in your BasePresenter and BaseControl.
 *
 * @property-read \SixtyEightPublishers\NotificationBundle\Notifier $notifier
 */
trait TNotifier
{
	use TRedrawMessages;

	/** @var \SixtyEightPublishers\NotificationBundle\INotifierFactory|NULL */
	private $notifierFactory;

	/** @var \SixtyEightPublishers\NotificationBundle\Notifier[]  */
	private $notifiers = [];

	/**
	 * You can override this method with some custom strategy.
	 *
	 * @return string
	 */
	protected function createNotifierTranslatorDomain(): string
	{
		return SixtyEightPublishers\SmartNetteComponent\Translator\TranslatorDomain::fromClassName(static::class) . '.message';
	}

	/**
	 * @param \SixtyEightPublishers\NotificationBundle\INotifierFactory $notifierFactory
	 *
	 * @return void
	 */
	public function injectNotifierFactory(SixtyEightPublishers\NotificationBundle\INotifierFactory $notifierFactory): void
	{
		$this->notifierFactory = $notifierFactory;
	}

	/**
	 * @param string|NULL $prefix
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notifier
	 */
	public function getNotifier(?string $prefix = NULL): SixtyEightPublishers\NotificationBundle\Notifier
	{
		$key = $prefix ?? '*';

		if (!isset($this->notifiers[$key])) {
			$this->notifiers[$key] = $this->notifierFactory->create($prefix ?? $this->createNotifierTranslatorDomain());
		}

		return $this->notifiers[$key];
	}

	/**
	 * @param string|\SixtyEightPublishers\NotificationBundle\Phrase\Phrase $message
	 * @param string                                                        $type
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\Notification
	 */
	public function flashMessage($message, $type = 'info'): SixtyEightPublishers\NotificationBundle\Notification\Notification
	{
		return $this->getNotifier()
			->session()
			->createNotificationBuilder($message)
			->setType($type)
			->scheduleFlashMessage();
	}
}
