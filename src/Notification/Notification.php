<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Notification;

use Nette;
use SixtyEightPublishers;

/**
 * @method void onRender(Notification $notification);
 */
class Notification
{
	use Nette\SmartObject;

	public const 	TYPE_INFO = 'info',
					TYPE_SUCCESS = 'success',
					TYPE_ERROR = 'error',
					TYPE_WARNING = 'warning';

	public const 	ENDPOINT_TOASTR = 'toastr',
					ENDPOINT_FLASH_MESSAGE = 'flash_message';

	/** @var string  */
	private $name;

	/** @var \SixtyEightPublishers\NotificationBundle\Phrase\IPhrase  */
	private $message;

	/** @var string  */
	private $type;

	/** @var string  */
	private $endpoint;

	/** @var \SixtyEightPublishers\NotificationBundle\Phrase\IPhrase|NULL  */
	private $title;

	/** @var \DateTime|NULL  */
	private $from;

	/** @var \DateTime|NULL  */
	private $expire;

	/** @var bool  */
	private $persistent;

	/** @var int|NULL  */
	private $maxNumberOfView;

	/** @var int  */
	private $remainingHiddenRequests = 0;
	
	/** @var int  */
	private $viewed = 0;

	/** @var bool  */
	private $isRendered = FALSE;

	/** @var callable[] */
	public $onRender = [];

	/**
	 * @param string                                                       $name
	 * @param \SixtyEightPublishers\NotificationBundle\Phrase\IPhrase      $message
	 * @param string                                                       $type
	 * @param string                                                       $endpoint
	 * @param \SixtyEightPublishers\NotificationBundle\Phrase\IPhrase|NULL $title
	 * @param \DateTime|NULL                                               $from
	 * @param \DateTime|NULL                                               $expire
	 * @param bool                                                         $persistent
	 * @param int|NULL                                                     $maxNumberOfView
	 */
	public function __construct(
		string $name,
		SixtyEightPublishers\NotificationBundle\Phrase\IPhrase $message,
		string $type,
		string $endpoint,
		?SixtyEightPublishers\NotificationBundle\Phrase\IPhrase $title,
		?\DateTime $from,
		?\DateTime $expire,
		bool $persistent,
		?int $maxNumberOfView
	) {
		$this->name = $name;
		$this->message = $message;
		$this->type = $type;
		$this->endpoint = $endpoint;
		$this->title = $title;
		$this->from = $from;
		$this->expire = $expire;
		$this->persistent = $persistent;
		$this->maxNumberOfView = $maxNumberOfView;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Phrase\IPhrase
	 */
	public function getMessage(): SixtyEightPublishers\NotificationBundle\Phrase\IPhrase
	{
		return $this->message;
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Phrase\IPhrase|NULL
	 */
	public function getTitle(): ?SixtyEightPublishers\NotificationBundle\Phrase\IPhrase
	{
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getEndpoint(): string
	{
		return $this->endpoint;
	}

	/**
	 * @param int $numberOfRequests
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\Notification
	 */
	public function hide(int $numberOfRequests): self
	{
		$this->remainingHiddenRequests = $numberOfRequests;
		
		return $this;
	}

	/**
	 * @return int
	 */
	public function getNumberOfRemainingHiddenRequests(): int
	{
		return $this->remainingHiddenRequests;
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function canShow(): bool
	{
		return TRUE === $this->isRendered
			|| (($this->getNumberOfRemainingHiddenRequests() <= 0)
			&& (
				!$this->isExpired()
				&& (NULL === $this->from
					|| new \DateTime('now', new \DateTimeZone('UTC')) >= $this->from)
			));
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function isExpired(): bool
	{
		if (TRUE === $this->persistent) { # is persistent
			return FALSE;
		}

		if (NULL !== $this->expire && $this->expire <= new \DateTime('now', new \DateTimeZone('UTC'))) { # is expired
			return TRUE;
		}

		if (NULL !== $this->maxNumberOfView && $this->viewed >= $this->maxNumberOfView) { # max number of view reached
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @param \Nette\Localization\ITranslator|NULL $translator
	 *
	 * @return string
	 * @throws \SixtyEightPublishers\NotificationBundle\Exception\NotificationExpiredException
	 */
	public function renderMessage(?Nette\Localization\ITranslator $translator = NULL): string
	{
		$message = $this->getMessage();

		$this->beforeRender();

		return NULL !== $translator ? $message->translate($translator) : $message->getMessage();
	}

	/**
	 * @param \Nette\Localization\ITranslator|NULL $translator
	 *
	 * @return string
	 * @throws \SixtyEightPublishers\NotificationBundle\Exception\NotificationExpiredException
	 */
	public function renderTitle(?Nette\Localization\ITranslator $translator = NULL): string
	{
		$title = $this->getTitle();

		$this->beforeRender();

		if (NULL === $title) {
			return '';
		}

		return NULL !== $translator ? $title->translate($translator) : $title->getMessage();
	}

	/**
	 * @return array
	 */
	public function __sleep(): array
	{
		$this->isRendered = FALSE;
		$this->onRender = [];

		return [
			'name',
			'message',
			'type',
			'endpoint',
			'from',
			'expire',
			'persistent',
			'maxNumberOfView',
			'viewed',
			'remainingHiddenRequests',
		];
	}

	/**
	 * @return void
	 * @throws \SixtyEightPublishers\NotificationBundle\Exception\NotificationExpiredException
	 * @throws \Exception
	 */
	private function beforeRender(): void
	{
		if (FALSE === $this->isRendered) {
			if (!$this->canShow()) {
				throw SixtyEightPublishers\NotificationBundle\Exception\NotificationExpiredException::error($this->getName());
			}
			$this->viewed++;
			$this->isRendered = TRUE;
			$this->onRender($this);
		}
	}
}
