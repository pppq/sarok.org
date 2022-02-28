<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\ActionPage;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\NewFavouritesAction;
use Sarok\Actions\LeftMenuAction;
use Sarok\Actions\IndexAction;

class ImageBrowserActionPage extends ActionPage
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function canExecute() : bool
    {
        return $this->context->getProperty(Context::PROP_IS_LOGGED_IN);
    }

    public function init() : void
    {
        $this->log->debug("Initializing ImageBrowserActionPage");
        $this->addAction("main", ImageBrowserAction::class);
    }
}
