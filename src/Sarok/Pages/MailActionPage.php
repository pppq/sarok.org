<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\ActionPage;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\MailSendAction;
use Sarok\Actions\MailReadAction;
use Sarok\Actions\MailPartnerListAction;
use Sarok\Actions\MailListAction;
use Sarok\Actions\MailComposeAction;

class MailActionPage extends ActionPage
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        $firstSegment = $this->context->getPathSegment(0);
        $action = MailListAction::class;
        
        switch ($firstSegment) {
            case 'compose':
            case 'new':
                $action = MailComposeAction::class;
                break;

            case 'send':
                if ($this->context->isPOST()) {
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

        $this->addAction("main", $action);
        $this->addAction("leftMenu", MailPartnerListAction::class);
    }
}
