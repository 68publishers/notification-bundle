<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Phrase;

use Kdyby;
use Nette;
use SixtyEightPublishers;

final class KdybyPhrase implements IPhrase
{
	use Nette\SmartObject;

	/** @var \Kdyby\Translation\Phrase  */
	private $phrase;

	/**
	 * @param \Kdyby\Translation\Phrase $phrase
	 */
	public function __construct(Kdyby\Translation\Phrase $phrase)
	{
		$this->phrase = $phrase;
	}

	/**
	 * @param string            $message
	 * @param int|array|NULL    $count
	 * @param string|array|NULL $parameters
	 * @param string|NULL       $domain
	 * @param string|NULL       $locale
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Phrase\KdybyPhrase
	 */
	public static function create(string $message, $count = NULL, $parameters = NULL, ?string $domain = NULL, ?string $locale = NULL): self
	{
		return new static(new Kdyby\Translation\Phrase($message, $count, $parameters, $domain, $locale));
	}

	/**
	 * @return \Kdyby\Translation\Phrase
	 */
	public function getPhrase(): Kdyby\Translation\Phrase
	{
		return $this->phrase;
	}

	/*********** interface \SixtyEightPublishers\NotificationBundle\Phrase\IPhrase ***********/

	/**
	 * {@inheritdoc}
	 */
	public function translate(Nette\Localization\ITranslator $translator): string
	{
		if (!$translator instanceof Kdyby\Translation\Translator) {
			throw new SixtyEightPublishers\NotificationBundle\Exception\InvalidArgumentException(sprintf(
				'Passed translator must be instance of %s, instance of %s given.',
				Kdyby\Translation\Translator::class,
				get_class($translator)
			));
		}

		return $this->phrase->translate($translator);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMessage(): string
	{
		return $this->phrase->message;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setMessage(string $message): void
	{
		$this->phrase->message = $message;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setParameters($parameters = NULL): void
	{
		if (NULL === $parameters) {
			$this->phrase->count = $this->phrase->parameters = NULL;

			return;
		}

		if (is_numeric($parameters)) {
			$this->phrase->count = (int) $parameters;

			return;
		}

		$this->phrase->parameters = (array) $parameters;
	}
}
