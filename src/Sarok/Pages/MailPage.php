<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\MailSendAction;
use Sarok\Actions\MailReadAction;
use Sarok\Actions\MailPartnerListAction;
use Sarok\Actions\MailListAction;
use Sarok\Actions\MailComposeAction;
use Sarok\Models\MenuItem;

final class MailPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        parent::init();
        
        $this->logger->debug('Initializing MailPage');
        $firstSegment = $this->popFirstSegment();
        $action = MailListAction::class;
        
        switch ($firstSegment) {
            case 'compose': // fall-through
            case 'new':
                $action = MailComposeAction::class;
                break;

            case 'send':
                if ($this->isPOST()) {
                    $action = MailSendAction::class;
                }
                break;
                
            default:
                if (preg_match('/^[0-9]+$/', $firstSegment)) {
                    $secondSegment = $this->popFirstSegment();
                    
                    if ($secondSegment === 'reply') {
                        $action = MailComposeAction::class;
                    } else {
                        $action = MailReadAction::class;
                    }
                }
                break;
        }

        $user = $this->getUser();
        $userLogin = $user->getLogin();
        
        $this->setLeftMenuItems(
            new MenuItem('Bejegyzés irása',   "/users/${userLogin}/new/"),
            new MenuItem('Level irasa',       '/privates/new/'),
            new MenuItem('Beallitasok',       '/settings/'),
            new MenuItem('Könyjelzők',        '/favourites/'),
            new MenuItem('Páciensek listája', '/about/pacients/'),
        );

        $this->addAction(self::TILE_MAIN, $action);
        $this->addAction(self::TILE_LEFT_MENU, MailPartnerListAction::class);
    }
}
