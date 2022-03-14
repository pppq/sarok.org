<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\BlogService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class EntryMapAction extends Action
{
    private BlogService $blogService;
    
    public function __construct(Logger $logger, Context $context, BlogService $blogService)
    {
        parent::__construct($logger, $context);
        $this->blogService = $blogService;
    }

    public function execute() : array
    {
        $this->log->debug("Running EntryMapAction");

        $reader = $this->getUser();
        $readerID = $reader->getID();

        $blog = $this->getBlog();
        $blogID = $blog->getID();
        $blogLogin = $blog->getLogin();
        $blogName = $blog->getUserData(User::KEY_BLOG_NAME);

        if ($this->context->hasEntryID()) {
            $entryID = $this->context->getEntryID();
            $pushPins = $this->blogService->getPushPinForEntry($readerID, $blogID, $entryID);
        } else {
            $pushPins = $this->blogService->getPushPinsForBlog($readerID, $blogID);
        }

        return compact('blogLogin', 'blogName', 'pushPins');
    }
}
