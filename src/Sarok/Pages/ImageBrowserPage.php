<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\ImageBrowserAction;

class ImageBrowserPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        $this->log->debug('Initializing ImageBrowserPage');
        // parent::init() is not needed
        
        $this->addAction('main', ImageBrowserAction::class);
    }
}
