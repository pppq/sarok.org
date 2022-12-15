<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Service\UserService;

final class CustomCssAction extends Action
{
    private UserService $userService;

    public function __construct(Logger $logger, Context $context, UserService $userService)
    {
        parent::__construct($logger, $context);
        $this->userService = $userService;
    }

    public function execute() : array
    {
        $this->log->debug('Executing CustomCssAction');
        
        $user = $this->getUser();
        $blog = $this->getBlog();
        $userID = $user->getID();
        $blogID = $blog->getID();
        
        if ($userID === User::ID_ANONYMOUS || $userID === $blogID) {
            $this->userService->populateUserData($blog, User::KEY_CSS);
            $css = $blog->getUserData(User::KEY_CSS);
        } else {
            $css = '';
        }

        return compact('css');
    }
}
