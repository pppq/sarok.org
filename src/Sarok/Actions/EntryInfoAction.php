<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\UserService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

final class EntryInfoAction extends Action
{
    private UserService $userService;
    
    public function __construct(Logger $logger, Context $context, UserService $userService)
    {
        parent::__construct($logger, $context);
        $this->userService = $userService;
    }

    public function execute() : array
    {
        $this->log->debug('Executing EntryInfoAction');

        $user = $this->getUser();
        $blog = $this->getBlog();

        // Populate all properties we intend to display
        $this->userService->populateUserData($blog, 
            User::KEY_NAME,
            User::KEY_BLOG_NAME,
            User::KEY_OCCUPATION,
            User::KEY_HAIR_COLOR,
            User::KEY_EYE_COLOR,
            User::KEY_DESCRIPTION,
            User::KEY_SEX,
            User::KEY_DISTRICT,
            User::KEY_COUNTRY,
            User::KEY_CITY,
            User::KEY_EMAIL,
            User::KEY_WIW,
            User::KEY_MSN,
            User::KEY_ICQ,
            User::KEY_SKYPE,
            User::KEY_PHONE,
            User::KEY_BIRTH_YEAR,
            User::KEY_BIRTHDATE);
        
        $blogID = $blog->getID();
        $friends = $this->userService->getFriends($blogID);
        $friendOfs = $this->userService->getFriendOfs($blogID);

        // Friends in common with the current user are highlighted, so we need to get their IDs
        $userID = $user->getID();
        $myFriends = $this->userService->getFriends($userID);

        $friendIDs = array_unique(array_merge($friends, $friendOfs));
        $friendLogins = $this->userService->getLogins($friendIDs);

        return compact('blog', 'friends', 'friendOfs', 'myFriends', 'friendLogins');
    }
}
