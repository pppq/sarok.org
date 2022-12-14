<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\RegistrationStep1Action;
use Sarok\Actions\RegistrationStep2Action;

final class RegistrationPage extends Page
{
    private const ACTION_MAP = array(
        'step1' => RegistrationStep1Action::class,
        'step2' => RegistrationStep2Action::class,
    );

    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function canExecute() : bool
    {
        // Registration is only available for users who are not yet logged in
        return !$this->isLoggedIn();
    }

    public function init() : void
    {
        parent::init();

        $this->logger->debug('Initializing RegistrationPage');
        $path = $this->popFirstSegment();

        if (!isset(self::ACTION_MAP[$path])) {
            $action = RegistrationStep1Action::class;
        } else {
            $action = self::ACTION_MAP[$path];
        }

        $this->addAction(self::TILE_MAIN, $action);
    }
}
