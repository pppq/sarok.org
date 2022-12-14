<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\FavouritesAction;

final class FavouritesPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        /* 
         * TODO: POST requests should update favourites, but this is only available through 
         * quick actions for now.
         */
        parent::init();

        $this->logger->debug('Initializing FavouritesPage');
        $this->addAction(self::TILE_MAIN, FavouritesAction::class);
    }
}
