<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\UI;

use SixtyEightPublishers;

/**
 * Adds a component "flashMessage" into your presenter or control.
 */
trait TFlashMessageControl
{
	/** @var \SixtyEightPublishers\NotificationBundle\Control\FlashMessage\IFlashMessageControlFactory|NULL */
	private $flashMessageControlFactory;

	/**
	 * @internal
	 * @param string $name
	 *
	 * @return void
	 */
	abstract protected function addMessageControlName(string $name): void;
	
	/**
	 * @param \SixtyEightPublishers\NotificationBundle\Control\FlashMessage\IFlashMessageControlFactory $flashMessageControlFactory
	 *
	 * @return void
	 */
	public function injectFlashMessageControlFactory(SixtyEightPublishers\NotificationBundle\Control\FlashMessage\IFlashMessageControlFactory $flashMessageControlFactory): void
	{
		$this->flashMessageControlFactory = $flashMessageControlFactory;
		$this->addMessageControlName('flashMessage');
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Control\FlashMessage\FlashMessageControl
	 */
	protected function createComponentFlashMessage(): SixtyEightPublishers\NotificationBundle\Control\FlashMessage\FlashMessageControl
	{
		return $this->flashMessageControlFactory->create();
	}
}
