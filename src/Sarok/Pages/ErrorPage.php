<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\ErrorAction;

class ErrorPage extends Page
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
        $this->logger->debug('Initializing ErrorPage');
        parent::init();
        
        $this->addAction(self::TILE_MAIN, ErrorAction::class);
    }
}
