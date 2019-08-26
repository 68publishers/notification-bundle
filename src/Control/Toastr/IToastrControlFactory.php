<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Control\Toastr;

interface IToastrControlFactory
{
	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Control\Toastr\ToastrControl
	 */
	public function create(): ToastrControl;
}
