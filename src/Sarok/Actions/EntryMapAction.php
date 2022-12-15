<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\BlogService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Service\UserService;

final class EntryMapAction extends Action
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

    public function execute() : array
    {
        $this->log->debug('Executing EntryMapAction');

        $user = $this->getUser();
        $userID = $user->getID();

        $blog = $this->getBlog();
        $blogID = $blog->getID();
        $blogLogin = $blog->getLogin();
        $this->userService->populateUserData($blog, User::KEY_BLOG_NAME);
        $blogName = $blog->getUserData(User::KEY_BLOG_NAME);

        if ($this->context->hasEntryID()) {
            $entryID = $this->context->getEntryID();
            $pushPins = $this->blogService->getPushPinForEntry($userID, $blogID, $entryID);
        } else {
            $pushPins = $this->blogService->getPushPinsForBlog($userID, $blogID);
        }

        return compact('blogLogin', 'blogName', 'pushPins');
    }
}
