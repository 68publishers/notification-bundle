<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\UI;

use SixtyEightPublishers;

/**
 * Adds a component "toastr" into your presenter or control.
 */
trait TToastrControl
{
	/** @var \SixtyEightPublishers\NotificationBundle\Control\Toastr\IToastrControlFactory|NULL */
	private $toastrControlFactory;

	/**
	 * @internal
	 * @param string $name
	 *
	 * @return void
	 */
	abstract protected function addMessageControlName(string $name): void;

	/**
	 * @param \SixtyEightPublishers\NotificationBundle\Control\Toastr\IToastrControlFactory $toastrControlFactory
	 *
	 * @return void
	 */
	public function injectToastrControlFactory(SixtyEightPublishers\NotificationBundle\Control\Toastr\IToastrControlFactory $toastrControlFactory): void
	{
		$this->toastrControlFactory = $toastrControlFactory;
		$this->addMessageControlName('toastr');
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Control\Toastr\ToastrControl
	 */
	protected function createComponentToastr(): SixtyEightPublishers\NotificationBundle\Control\Toastr\ToastrControl
	{
		return $this->toastrControlFactory->create();
	}
}
