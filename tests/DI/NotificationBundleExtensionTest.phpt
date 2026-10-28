<?php

declare(strict_types=1);

namespace SixtyEightPublishers\NotificationBundle\Tests\DI;

use Tester\Assert;
use Tester\Helpers;
use Tester\TestCase;
use Nette\DI\Container;
use Nette\Bootstrap\Configurator;
use Nette\Application\Application;
use Nette\Localization\Translator;
use Nette\Application\UI\Presenter;
use SixtyEightPublishers\NotificationBundle\INotifierFactory;
use SixtyEightPublishers\NotificationBundle\UI\TToastrControl;
use SixtyEightPublishers\NotificationBundle\UI\TRedrawMessages;
use SixtyEightPublishers\NotificationBundle\UI\TFlashMessageControl;
use SixtyEightPublishers\TranslationBridge\PrefixedTranslatorFactoryInterface;
use SixtyEightPublishers\NotificationBundle\Control\AbstractNotificationControl;
use SixtyEightPublishers\NotificationBundle\Event\NotificationExpirationHandler;

require __DIR__ . '/../bootstrap.php';

final class TestPresenter extends Presenter
{
	use TRedrawMessages;
	use TFlashMessageControl;
	use TToastrControl;
}

final class NotificationBundleExtensionTest extends TestCase
{
	public function testExpirationHandlerShouldBeRegisteredIntoApplication(): void
	{
		$container = $this->createContainer();
		$application = $container->getByType(Application::class);

		Assert::count(1, $application->onResponse);
		Assert::type(NotificationExpirationHandler::class, $application->onResponse[0][0]);
	}

	public function testFlashMessageWithTitleShouldBeRendered(): void
	{
		$container = $this->createContainer();
		$notifier = $container->getByType(INotifierFactory::class)->create();

		$notifier->success('message_text')->title('title_text')->scheduleFlashMessage();

		$html = $this->renderControl($container, 'flashMessage');

		Assert::contains('alert alert-success', $html);
		Assert::contains('<strong>T:title_text</strong> T:message_text', $html);
	}

	public function testToastrWithTitleShouldBeRendered(): void
	{
		$container = $this->createContainer();
		$notifier = $container->getByType(INotifierFactory::class)->create();

		$notifier->error('message_text')->title('title_text')->scheduleToastr();

		$html = $this->renderControl($container, 'toastr');

		Assert::contains('toastr.error("T:message_text", "T:title_text");', $html);
	}

	private function renderControl(Container $container, string $name): string
	{
		$presenter = $container->createInstance(TestPresenter::class);
		$container->callInjects($presenter);

		$control = $presenter[$name];
		assert($control instanceof AbstractNotificationControl);

		$translator = new class implements Translator {
			public function translate($message, ...$parameters): string
			{
				return 'T:' . $message;
			}
		};

		$control->setTranslator($translator, new class($translator) implements PrefixedTranslatorFactoryInterface {
			public function __construct(
				private Translator $translator,
			) {
			}

			public function create(string $prefix): Translator
			{
				return $this->translator;
			}
		});

		ob_start();
		$control->render();

		return (string) ob_get_clean();
	}

	private function createContainer(): Container
	{
		$tempDir = sys_get_temp_dir() . '/68publishers.notification-bundle.' . uniqid('', TRUE);
		Helpers::purge($tempDir);

		$configurator = new Configurator();
		$configurator->setTempDirectory($tempDir);
		$configurator->addConfig(__DIR__ . '/config.neon');

		return $configurator->createContainer();
	}
}

(new NotificationBundleExtensionTest())->run();
