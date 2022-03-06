<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\FavouritesAction;

class FavouritesPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        $this->logger->debug('Initializing FavouritesPage');
        
        if ($this->context->isPOST()) {
            // TODO: POST requests should update favourites
            $this->setTemplateName('empty');
        } else {
            parent::init();
        }
        
        $this->addAction('main', FavouritesAction::class);
    }
}
