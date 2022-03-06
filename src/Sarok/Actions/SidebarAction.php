<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\BlogService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class SidebarAction extends Action
{
    private BlogService $blogService;
    
    public function __construct(Logger $logger, Context $context, BlogService $blogService)
    {
        parent::__construct($logger, $context);
        $this->blogService = $blogService;
    }

    public function execute() : array
    {
        $this->log->debug('Running SidebarAction');

        $blog = $this->context->getBlog();
        $blogID = $blog->getID();
        $blogLogin = $blog->getLogin();
        $blogText = $blog->getUserData(User::KEY_BLOG_TEXT);

        $tags = $this->blogService->getTagCloud($blogID);

        return compact('blogLogin', 'blogText', 'tags');
    }
}
