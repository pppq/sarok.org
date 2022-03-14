<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\RegistrationStep2Action;
use Sarok\Actions\RegistrationStep1Action;

class RegistrationPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function canExecute(): bool
    {
        return !$this->isLoggedIn();
    }

    public function init(): void
    {
        $this->logger->debug('Initializing RegistrationPage');
        parent::init();

        $actionMap = array(
            'step1' => RegistrationStep1Action::class,
            'step2' => RegistrationStep2Action::class,
        );

        $path = $this->getPathSegment(0);

        if (!isset($actionMap[$path])) {
            $action = RegistrationStep1Action::class;
        } else {
            $action = $actionMap[$path];
        }

        $this->addAction(self::TILE_MAIN, $action);
    }
}
