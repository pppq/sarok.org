<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\ErrorAction;

final class ErrorPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function canExecute() : bool
    {
        // Error pages can be rendered to any user
        return true;
    }

    public function init() : void
    {
        parent::init();
      
        $this->logger->debug('Initializing ErrorPage');
        $this->addAction(self::TILE_MAIN, ErrorAction::class);
    }
}
