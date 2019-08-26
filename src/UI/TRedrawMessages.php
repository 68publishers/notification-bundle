<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\UI;

trait TRedrawMessages
{
	/**
	 * @var array
	 */
	private $messageControlNames = [];

	/**
	 * @internal
	 * @param string $name
	 *
	 * @return void
	 */
	protected function addMessageControlName(string $name): void
	{
		$this->messageControlNames[] = $name;
	}
	
	/**
	 * @return void
	 */
	public function redrawMessages(): void
	{
		/** @noinspection PhpUndefinedMethodInspection */
		/** @var \Nette\Application\UI\Presenter $presenter */
		$presenter = $presenter = $this->getPresenter();

		if (!$presenter->isAjax()) {
			return;
		}

		foreach ($this->messageControlNames as $messageControlName) {
			/** @noinspection PhpUndefinedMethodInspection */
			$this[$messageControlName]->redrawControl();
		}

		if ($this !== $presenter && is_callable([$presenter, 'redrawMessages'])) {
			/** @noinspection PhpUndefinedMethodInspection */
			$presenter->redrawMessages();
		}
	}
}
