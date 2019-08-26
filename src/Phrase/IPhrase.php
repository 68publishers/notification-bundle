<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Phrase;

use Nette;

interface IPhrase
{
	/**
	 * @param \Nette\Localization\ITranslator $translator
	 *
	 * @return string
	 */
	public function translate(Nette\Localization\ITranslator $translator): string;

	/**
	 * @return string
	 */
	public function getMessage(): string;

	/**
	 * @param string $message
	 *
	 * @return void
	 */
	public function setMessage(string $message): void;

	/**
	 * @param int|array|NULL $parameters
	 *
	 * @return void
	 */
	public function setParameters($parameters = NULL): void;
}
