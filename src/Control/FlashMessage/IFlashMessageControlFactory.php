<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Control\FlashMessage;

interface IFlashMessageControlFactory
{
	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Control\FlashMessage\FlashMessageControl
	 */
	public function create(): FlashMessageControl;
}
