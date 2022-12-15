<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\MailService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Service\UserService;

final class CheckMailAction extends Action
{
    private UserService $userService;
    private MailService $mailService;

    public function __construct(
        Logger $logger, 
        Context $context, 
        UserService $userService,
        MailService $mailService
    ) {
        parent::__construct($logger, $context);
        $this->userService = $userService;
        $this->mailService = $mailService;
    }

    public function execute() : array
    {
        $this->log->debug('Executing CheckMailAction');
        
        $user = $this->getUser();
        $userID = $user->getID();
        
        $this->userService->populateUserData($user, User::KEY_NEW_MAIL);

        $newMail = (int) $user->getUserData(User::KEY_NEW_MAIL, '0');
        $lastUnread = null;
        $firstUnread = null;

        if ($newMail > 0) {
            $this->log->debug("User ${userID} has ${newMail} unread message(s)");
            $lastUnread = $this->mailService->getLastUnread($userID);
            if ($newMail > 1) {
                $firstUnread = $this->mailService->getFirstUnread($userID);
            }
        } else {
            $this->log->debug("User ${userID} has no unread messages");
        }

        return compact('newMail', 'lastUnread', 'firstUnread');
    }
}
