<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\BlogService;
use Sarok\Models\User;
use Sarok\Models\AccessType;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class EntryEditAction extends Action
{
    private BlogService $blogService;

    public function __construct(Logger $logger, Context $context, BlogService $blogService)
    {
        parent::__construct($logger, $context);
        $this->blogService = $blogService;
    }

    public function execute(): array
    {
        $this->log->debug('Running EntryEditAction');

        $user = $this->getUser();

        if ($user->getID() === User::ID_ANONYMOUS) {
            return Action::NO_DATA;
        }

        $entryID = $this->context->getEntryID();

        if (!$this->blogService->canChangeEntry($user->getID(), $entryID)) {
            return Action::NO_DATA;
        }

        $entry = $this->blogService->getEntryByID($entryID);
        $blog = $this->getBlog();
        $blogName = $blog->getUserData(User::KEY_BLOG_NAME);
        $blogID = $blog->getID();
        $userID = $user->getID();

        if ($entry->getAccess() === AccessType::LIST) {
            $entryAccessList = $this->blogService->getEntryAccessLogins($entryID);
        } else {
            $entryAccessList = array();
        }

        $tags = $this->blogService->getTags($entryID);

        return compact('entry', 'blog', 'blogName', 'blogID', 'userID', 'entryAccessList', 'tags');
    }
}
