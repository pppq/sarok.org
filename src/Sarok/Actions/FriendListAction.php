<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\UserService;
use Sarok\Service\SessionService;
use Sarok\Actions\Action;

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

        $user = $this->context->getUser();
        $userID = $user->getID();
        $userLogin= $user->getLogin();

        $friends = $this->userService->getFriendsActivity($userID);
        $onlineFriends = $this->sessionService->getOnlineFriends($userID);

        return compact('userLogin', 'friends', 'onlineFriends');
    }
}
