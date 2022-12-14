<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\NewFavouritesAction;
use Sarok\Actions\IndexAction;
use Sarok\Models\MenuItem;

final class IndexPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        parent::init();
        
        $this->logger->debug('Initializing IndexPage');
        $user = $this->getUser();
        $userLogin = $user->getLogin();
        
        $this->setLeftMenuItems(
            new MenuItem('Bejegyzés irása',   "/users/${userLogin}/new/"),
            new MenuItem('Level irasa',       '/privates/new/'),
            new MenuItem('Beallitasok',       '/settings/'),
            new MenuItem('Könyjelzők',        '/favourites/'),
            new MenuItem('Páciensek listája', '/about/pacients/'),
        );
        
        $this->addAction(self::TILE_LEFT_MENU, NewFavouritesAction::class);
        $this->addAction(self::TILE_MAIN, IndexAction::class);
    }
}
