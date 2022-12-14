<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\LogoutAction;

final class LogoutPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        // parent::init() is not needed
        
        $this->logger->debug('Initializing LogoutPage');
        $this->setTemplateName('empty');
        $this->addAction(self::TILE_MAIN, LogoutAction::class);
    }
}
