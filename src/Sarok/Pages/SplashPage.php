<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\EmptyAction;

class SplashPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function canExecute() : bool
    {
        return true;
    }

    public function init() : void
    {
        $this->logger->debug('Initializing SplashPage');
        // parent::init() is not needed

        $this->setTemplateName('splash');
        $this->addAction('main', EmptyAction::class);
    }
}
