<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\TextProcessor;
use Sarok\Service\SessionService;
use Sarok\Service\BlogService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class EntryAddCommentAction extends Action
{
    private SessionService $sessionService;
    private BlogService $blogService;
    private TextProcessor $textProcessor;

    public function __construct(
        Logger $logger,
        Context $context,
        SessionService $sessionService,
        BlogService $blogService,
        TextProcessor $textProcessor
    ) {
        parent::__construct($logger, $context);
        $this->sessionService = $sessionService;
        $this->blogService = $blogService;
        $this->textProcessor = $textProcessor;
    }

    public function execute() : array
    {
        $this->log->debug('Running EntryAddCommentAction');

        $user = $this->context->getUser();
        $redirectToMain = $user->getUserData(User::KEY_TO_MAIN_PAGE, 'N');
        
        if ($redirectToMain === 'Y') {
            $location = '/';
        } else {
            // TODO: add anchor to jump to the newly added comment?
            $location = "/users/$blogLogin/m_$entryID/";
        }

        if (!$this->context->isPostRequest()) {
            return compact('location');
        }
        
        // FIXME: input filtering!
        $body = $this->context->getPost('body');
        $submitterName = $this->context->getPost('your_name');
        $submitterLink = $this->context->getPost('your_web');

        if (strlen($submitterName) > 0) {
            $signature = $submitterName;
            $this->sessionService->setCookie('your_name', $submitterName);

            if (strlen($submitterLink) > 8) {
                $this->sessionService->setCookie('your_web', $submitterLink);

                if (strpos($submitterLink, '@') >= 0) {
                    $signature = "<a href='mailto:$submitterLink'>$submitterName</a>";
                } elseif (strpos($submitterLink, '://') >= 0) {
                    $signature = "<a href='$submitterLink'>$submitterName</a>";
                } else {
                    $signature = "<a href='http://$submitterLink'>$submitterName</a>";
                }
            }
            
            $body .= "<br><br>$signature";
        }

        $userID = $user->getID();
        $entryID = $this->context->getEntryID();

        if ($this->blogService->canComment($entryID, $userID)) {
            $this->blogService->addComment(0, $entryID, $userID, $this->format($body));
        }
        
        return compact('location');
    }
     
    private function format(string $body) : string
    {
        $body = $this->textProcessor->preFormat($body);
        $body = $this->textProcessor->postFormat($body);
        $body = $this->textProcessor->tidy($body);
        return $body;
    }
}
