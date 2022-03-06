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

class MailPage extends Page
{
    private const MAIL_MENU_ITEMS = [
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
        $this->logger->debug('Initializing MailPage');
        parent::init();
        
        $firstSegment = $this->context->getPathSegment(0);
        $action = MailListAction::class;
        
        switch ($firstSegment) {
            case 'compose':
            case 'new':
                $action = MailComposeAction::class;
                break;

            case 'send':
                if ($this->context->isPostRequest()) {
                    $action = MailSendAction::class;
                }
                break;
                
            default:
                if (preg_match('/^[0-9]+$/', $firstSegment)) {
                    $secondSegment = $this->context->getPathSegment(1);
                    
                    if ($secondSegment === 'reply') {
                        $action = MailComposeAction::class;
                    } else {
                        $action = MailReadAction::class;
                    }
                }
                break;
        }

        $user = $this->context->getUser();
        $userLogin = $user->getLogin();
        
        $menu = self::MAIL_MENU_ITEMS;
        $menu[0]['url'] = "/users/$userLogin/new/";
        $this->context->setProperty(Context::PROP_MENU_ITEMS, $menu);        

        $this->addAction('main', $action);
        $this->addAction('leftMenu', MailPartnerListAction::class);
    }
}
