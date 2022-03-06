<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\BlogService;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class ShowArticleAction extends Action
{
    private const DEFAULT_ENTRY_ID = 87287;

    private BlogService $blogService;
    
    public function __construct(Logger $logger, Context $context, BlogService $blogService)
    {
        parent::__construct($logger, $context);
        $this->blogService = $blogService;
    }

    public function execute() : array
    {
        $this->log->debug('Running ShowArticleAction');
        
        $reader = $this->context->getUser();
        $readerID = $reader->getID();
        $entryID = $this->context->getProperty(Context::PROP_ENTRY_ID);

        if ($this->blogService->isEntryVisible($readerID, $entryID)) {
            $entry = $this->blogService->getEntryByID($entryID);
        } else {
            $entry = $this->blogService->getEntryByID(self::DEFAULT_ENTRY_ID);
        }

        $body = $entry->getBody();
        $body_2 = $entry->getBody2();
        
        return compact('body', 'body_2');
    }
}
