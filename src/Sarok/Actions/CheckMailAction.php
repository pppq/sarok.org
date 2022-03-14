<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\MailService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class CheckMailAction extends Action
{
    private MailService $mailService;

    public function __construct(Logger $logger, Context $context, MailService $mailService)
    {
        parent::__construct($logger, $context);
        $this->mailService = $mailService;
    }

    public function execute() : array
    {
        $this->log->debug("Running CheckMailAction");
        
        $user = $this->getUser();
        $userID = $user->getID();
        $userLogin = $user->getLogin();
        $newMail = (int) $user->getUserData(User::KEY_NEW_MAIL);

        $lastUnread = null;
        $firstUnread = null;

        if ($newMail > 0) {
            $this->log->debug("User $userID has $newMail unread message(s)");
            $lastUnread = $this->mailService->getLastUnread($userID);
            if ($newMail > 1) {
                $firstUnread = $this->mailService->getFirstUnread($userID);
            }
        } else {
            $this->log->debug("User $userID has no unread messages");
        }

        return compact('newMail', 'lastUnread', 'firstUnread');
    }
}
