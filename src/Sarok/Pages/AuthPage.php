<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\AuthAction;

class AuthPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        $this->logger->debug('Initializing AuthPage');
        // parent::init() is not needed

        $this->setTemplateName('empty');
        $this->addAction('main', AuthAction::class);
    }
}
