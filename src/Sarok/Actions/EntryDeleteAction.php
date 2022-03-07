<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\BlogService;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class EntryDeleteAction extends Action
{
    private BlogService $blogService;

    public function __construct(Logger $logger, Context $context, BlogService $blogService)
    {
        parent::__construct($logger, $context);
        $this->blogService = $blogService;
    }

    public function execute() : array
    {
        $this->log->debug('Running EntryDeleteAction');

        $user = $this->context->getUser();
        $blog = $this->context->getBlog();

        $userID = $user->getID();
        $blogID = $blog->getID();
        $blogLogin = $blog->getLogin();

        // Default redirect location after POST is the blog's main page
        $location = "/users/$blogLogin/";

        if (!$this->context->isPostRequest()) {
            return compact('location');
        }
        
        if ($this->context->hasEntryID()) {
            $entryID = $this->context->getEntryID();
            $thirdSegment = $this->context->getPathSegment(2);

            if (preg_match('/^[1-9][0-9]*$/', $thirdSegment)) {
                $commentID = (int) $thirdSegment;

                if ($this->blogService->canDeleteComment($userID, $entryID, $commentID)) {
                    $this->log->debug("Deleting comment #$commentID");
                    $this->blogService->removeComment($commentID);

                    // Go back to the entry after deleting the comment
                    $location = "/users/$blogLogin/m_$entryID";
                }
            } else {
                if ($this->blogService->canChangeEntry($userID, $entryID)) {
                    $this->log->debug("Deleting entry #$entryID");
                    $this->blogService->removeEntry($entryID);
                }
            }
        }

        return compact('location');
    }
}
