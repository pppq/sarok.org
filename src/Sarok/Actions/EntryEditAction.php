<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\BlogService;
use Sarok\Models\User;
use Sarok\Models\AccessType;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Service\UserService;

final class EntryEditAction extends Action
{
    private UserService $userService;
    private BlogService $blogService;

    public function __construct(
        Logger $logger, 
        Context $context, 
        UserService $userService,
        BlogService $blogService
    ) {
        parent::__construct($logger, $context);
        $this->userService = $userService;
        $this->blogService = $blogService;
    }

    public function execute(): array
    {
        $this->log->debug('Executing EntryEditAction');

        $user = $this->getUser();
        $userID = $user->getID();

        if ($userID === User::ID_ANONYMOUS) {
            return Action::NO_DATA;
        }

        $entryID = $this->context->getEntryID();

        if ($this->blogService->canChangeEntry($userID, $entryID) === false) {
            return Action::NO_DATA;
        }

        $entry = $this->blogService->getEntryByID($entryID);
        
        $blog = $this->getBlog();
        $this->userService->populateUserData($blog, User::KEY_BLOG_NAME);
        $blogName = $blog->getUserData(User::KEY_BLOG_NAME);
        $blogID = $blog->getID();

        if ($entry->getAccess() === AccessType::LIST) {
            $entryAccessList = $this->blogService->getEntryAccessLogins($entryID);
        } else {
            $entryAccessList = array();
        }

        $tags = $this->blogService->getTags($entryID);

        return compact(
            'entry', 
            'blog', 
            'blogName', 
            'blogID', 
            'userID', 
            'entryAccessList', 
            'tags'
        );
    }
}
