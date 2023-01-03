<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Control;

use Nette;
use SixtyEightPublishers;

abstract class AbstractNotificationControl extends Nette\Application\UI\Control implements SixtyEightPublishers\TranslationBridge\TranslatorAwareInterface
{
	use SixtyEightPublishers\TranslationBridge\TranslatorAwareTrait;
	use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\TemplateResolverTrait;

	/** @var \SixtyEightPublishers\NotificationBundle\Notification\ActiveNotificationProvider  */
	protected $provider;

	/** @var \Nette\Security\User  */
	protected $user;

	/** @var NULL|string  */
	protected $endpoint;

	/**
	 * @param \SixtyEightPublishers\NotificationBundle\Notification\ActiveNotificationProvider $provider
	 * @param \Nette\Security\User                                                             $user
	 */
	public function __construct(SixtyEightPublishers\NotificationBundle\Notification\ActiveNotificationProvider $provider, Nette\Security\User $user)
	{
		$this->provider = $provider;
		$this->user = $user;
	}

	/**
	 * @return void
	 */
	public function render(): void
	{
		$template = $this->template;
		$template->setTranslator($this->getPrefixedTranslator());

		$template->translator = $this->getTranslator();
		$template->class = SixtyEightPublishers\NotificationBundle\Notification\Notification::class;
		$template->notifications = $this->provider->provide(
			$this->endpoint,
			$this->user->loggedIn ? (string) $this->user->id : NULL
		);

		$this->doRender();
	}
}
