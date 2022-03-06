<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\NewFavouritesAction;
use Sarok\Actions\LeftMenuAction;
use Sarok\Actions\IndexAction;

class IndexPage extends Page
{
    private const INDEX_MENU_ITEMS = [
        [ 'name' => 'Bejegyzés irása',   'url' => '/'                ],
        [ 'name' => 'Level irasa',       'url' => '/privates/new/'   ],
        [ 'name' => 'Beallitasok',       'url' => '/settings/'       ],
        [ 'name' => 'Könyjelzők',        'url' => '/favourites/'     ],
        [ 'name' => 'Páciensek listája', 'url' => '/about/pacients/' ],
    ];

    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        $this->logger->debug('Initializing IndexPage');
        parent::init();
        
        $user = $this->context->getUser();
        $userLogin = $user->getLogin();
        
        $menu = self::INDEX_MENU_ITEMS;
        $menu[0]['url'] = "/users/$userLogin/new/";
        $this->context->setProperty(Context::PROP_MENU_ITEMS, $menu);

        $this->addAction('leftMenu', NewFavouritesAction::class);
        $this->addAction('main', IndexAction::class);
    }
}
