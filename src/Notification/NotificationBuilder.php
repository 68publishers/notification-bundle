<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Notification;

use Nette;
use SixtyEightPublishers;

class NotificationBuilder
{
	use Nette\SmartObject;

	/** @var \DateTime  */
	private $now;

	/** @var \SixtyEightPublishers\NotificationBundle\Storage\IExtendedStorage  */
	private $storage;

	/** @var string  */
	private $name;

	/** @var \SixtyEightPublishers\NotificationBundle\Phrase\IPhrase */
	private $message;

	/** @var \SixtyEightPublishers\NotificationBundle\Phrase\IPhrase|NULL */
	private $title;

	/** @var string  */
	private $type;

	/** @var \DateTime|NULL */
	private $from;

	/** @var \DateTime|NULL  */
	private $expire;

	/** @var bool  */
	private $persistent = FALSE;

	/** @var int|NULL */
	private $maxNumberOfView = 1;

	/** @var string|NULL  */
	private $messageDomain;

	/**
	 * @param \SixtyEightPublishers\NotificationBundle\Storage\IExtendedStorage $storage
	 * @param string                                                            $name
	 * @param string|\SixtyEightPublishers\NotificationBundle\Phrase\Phrase     $message
	 *
	 * @throws \Exception
	 */
	public function __construct(SixtyEightPublishers\NotificationBundle\Storage\IExtendedStorage $storage, string $name, $message)
	{
		$this->storage = $storage;
		$this->now = new \DateTime('now', new \DateTimeZone('UTC'));
		$this->name = $name;
		$this->message = $message instanceof SixtyEightPublishers\NotificationBundle\Phrase\IPhrase ? $message : new SixtyEightPublishers\NotificationBundle\Phrase\Phrase((string) $message);
		$this->type = Notification::TYPE_INFO;
	}

	/**
	 * @param string|\SixtyEightPublishers\NotificationBundle\Phrase\IPhrase $title
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function title($title): self
	{
		$this->title = $title instanceof SixtyEightPublishers\NotificationBundle\Phrase\IPhrase ? $title : new SixtyEightPublishers\NotificationBundle\Phrase\Phrase((string) $title);

		return $this;
	}

	/**
	 * @param string $modifier
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 * @throws \SixtyEightPublishers\NotificationBundle\Exception\NotificationCreationException
	 */
	public function from(string $modifier): self
	{
		return $this->setFromDateTime((clone $this->now)->modify($modifier));
	}

	/**
	 * @param string $modifier
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 * @throws \SixtyEightPublishers\NotificationBundle\Exception\NotificationCreationException
	 */
	public function expire(string $modifier): self
	{
		return $this->setExpireDateTime((clone $this->now)->modify($modifier));
	}

	/**
	 * @param bool $persistent
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function persistent(bool $persistent = TRUE): self
	{
		$this->persistent = $persistent;
		if (TRUE === $persistent) {
			$this->maxNumberOfView = NULL;
		}

		return $this;
	}

	/**
	 * @param int $maxNumberOfView
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function maxNumberOfView(int $maxNumberOfView): self
	{
		$this->maxNumberOfView = $maxNumberOfView;
		$this->persistent(FALSE);

		return $this;
	}

	/**
	 * @param string $type
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function setType(string $type): self
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function info(): self
	{
		return $this->setType(Notification::TYPE_INFO);
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function success(): self
	{
		return $this->setType(Notification::TYPE_SUCCESS);
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function error(): self
	{
		return $this->setType(Notification::TYPE_ERROR);
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function warning(): self
	{
		return $this->setType(Notification::TYPE_WARNING);
	}

	/**
	 * @internal
	 *
	 * @param string|NULL $messageDomain
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 */
	public function setMessageDomain(?string $messageDomain): self
	{
		$this->messageDomain = $messageDomain;

		return $this;
	}

	/**
	 * @param \DateTime $from
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 * @throws \SixtyEightPublishers\NotificationBundle\Exception\NotificationCreationException
	 */
	public function setFromDateTime(\DateTime $from): self
	{
		if ($from < $this->now) {
			throw SixtyEightPublishers\NotificationBundle\Exception\NotificationCreationException::timeInPast($from);
		}

		if (NULL !== $this->expire && $from > $this->expire) {
			throw SixtyEightPublishers\NotificationBundle\Exception\NotificationCreationException::chronologicalError($from, $this->expire);
		}

		$this->from = $from;

		return $this;
	}

	/**
	 * @param \DateTime $expire
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\NotificationBuilder
	 * @throws \SixtyEightPublishers\NotificationBundle\Exception\NotificationCreationException
	 */
	public function setExpireDateTime(\DateTime $expire): self
	{
		if ($expire < $this->now) {
			throw SixtyEightPublishers\NotificationBundle\Exception\NotificationCreationException::timeInPast($expire);
		}

		if (NULL !== $this->from && $this->from > $expire) {
			throw SixtyEightPublishers\NotificationBundle\Exception\NotificationCreationException::chronologicalError($this->from, $expire);
		}

		$this->expire = $expire;

		return $this;
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\Notification
	 */
	public function scheduleToastr(): Notification
	{
		return $this->schedule(Notification::ENDPOINT_TOASTR);
	}

	/**
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\Notification
	 */
	public function scheduleFlashMessage(): Notification
	{
		return $this->schedule(Notification::ENDPOINT_FLASH_MESSAGE);
	}

	/**
	 * @param string $endpoint
	 *
	 * @return \SixtyEightPublishers\NotificationBundle\Notification\Notification
	 */
	public function schedule(string $endpoint): Notification
	{
		if (NULL !== $this->messageDomain) {
			$this->message->setMessage(sprintf('%s.%s', $this->messageDomain, $this->message->getMessage()));
			if (NULL !== $this->title) {
				$this->title->setMessage(sprintf('%s.%s', $this->messageDomain, $this->title->getMessage()));
			}
		}

		$notification = new Notification(
			$this->name,
			$this->message,
			$this->type,
			$endpoint,
			$this->title,
			$this->from,
			$this->expire,
			$this->persistent,
			$this->maxNumberOfView
		);
		$this->storage->add($notification);

		return $notification;
	}
}
