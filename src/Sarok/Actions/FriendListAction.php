<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\UserService;
use Sarok\Service\SessionService;
use Sarok\Actions\Action;
use Sarok\Context;
use Sarok\Logger;
use Sarok\Models\User;
use Sarok\Util;

class FriendListAction extends Action
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct(
        Logger $logger,
        Context $context,
        UserService $userService,
        SessionService $sessionService
    ) {
        parent::__construct($logger, $context);
        $this->userService = $userService;
        $this->sessionService = $sessionService;
    }

    public function execute() : array
    {
        $this->log->debug("Running FriendListAction");

        $user = $this->getUser();
        $userID = $user->getID();
        $userLogin= $user->getLogin();

        $lastActive = Util::utcDateTimeFromString();
        $lastActive->modify('-1 hour');

        $friends = $this->userService->getFriendsActivity($userID);
        $onlineFriends = array_filter($friends, function(User $f) use (&$lastActive) { 
            return $f->getActivationDate() >= $lastActive; 
        });

        return compact('userLogin', 'friends', 'onlineFriends');
    }
}
