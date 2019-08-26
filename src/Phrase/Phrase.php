<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Phrase;

use Nette;

final class Phrase implements IPhrase
{
	use Nette\SmartObject;

	/** @var string */
	private $message;

	/** @var int|array|NULL */
	private $parameters;

	/**
	 * @param string         $message
	 * @param int|array|NULL $parameters
	 */
	public function __construct(string $message, $parameters = NULL)
	{
		$this->setMessage($message);
		$this->setParameters($parameters);
	}

	/*********** interface \SixtyEightPublishers\NotificationBundle\Phrase\IPhrase ***********/

	/**
	 * {@inheritdoc}
	 */
	public function translate(Nette\Localization\ITranslator $translator): string
	{
		return $translator->translate($this->message, $this->parameters);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setMessage(string $message): void
	{
		$this->message = $message;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setParameters($parameters = null): void
	{
		$this->parameters = $parameters;
	}
}
