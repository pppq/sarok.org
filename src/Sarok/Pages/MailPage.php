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

class MailPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        $this->logger->debug('Initializing MailPage');
        parent::init();
        
        $firstSegment = $this->getPathSegment(0);
        $action = MailListAction::class;
        
        switch ($firstSegment) {
            case 'compose':
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
                    $secondSegment = $this->getPathSegment(1);
                    
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
        
        $this->context->setLeftMenuItems(
            new MenuItem('Bejegyzés irása',   "/users/$userLogin/new/"),
            new MenuItem('Level irasa',       '/privates/new/'),
            new MenuItem('Beallitasok',       '/settings/'),
            new MenuItem('Könyjelzők',        '/favourites/'),
            new MenuItem('Páciensek listája', '/about/pacients/'),
        );

        $this->addAction(self::TILE_MAIN, $action);
        $this->addAction(self::TILE_LEFT_MENU, MailPartnerListAction::class);
    }
}
