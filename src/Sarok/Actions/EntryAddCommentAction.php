<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\TextProcessor;
use Sarok\Service\SessionService;
use Sarok\Service\BlogService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Service\UserService;

final class EntryAddCommentAction extends Action
{
    private UserService $userService;
    private SessionService $sessionService;
    private BlogService $blogService;
    private TextProcessor $textProcessor;

    public function __construct(
        Logger $logger,
        Context $context,
        UserService $userService,
        SessionService $sessionService,
        BlogService $blogService,
        TextProcessor $textProcessor
    ) {
        parent::__construct($logger, $context);
        $this->userService = $userService;
        $this->sessionService = $sessionService;
        $this->blogService = $blogService;
        $this->textProcessor = $textProcessor;
    }

    public function execute() : array
    {
        $this->log->debug('Running EntryAddCommentAction');

        $user = $this->getUser();
        $userID = $user->getID();
        $this->userService->populateUserData($user, User::KEY_TO_MAIN_PAGE);
        $redirectToMain = $user->getUserData(User::KEY_TO_MAIN_PAGE, 'N');

        $blog = $this->getBlog();
        $blogLogin = $blog->getLogin();

        $entryID = $this->getEntryID();

        if ($redirectToMain === 'Y') {
            $location = '/';
        } else {
            // TODO: add anchor to jump to the newly added comment?
            $location = "/users/$blogLogin/m_$entryID/";
        }

        if (!$this->isPOST()) {
            return compact('location');
        }
        
        // FIXME: input filtering!
        $body = $this->getPOST('body');
        $submitterName = $this->getPOST('your_name');
        $submitterLink = $this->getPOST('your_web');

        if ($submitterName !== '') {
            $signature = $submitterName;
            $this->sessionService->setCookie('your_name', $submitterName);

            if (strlen($submitterLink) > 8) {
                $this->sessionService->setCookie('your_web', $submitterLink);

                if (strpos($submitterLink, '@') >= 0) {
                    $signature = "<a href='mailto:${submitterLink}'>${submitterName}</a>";
                } elseif (strpos($submitterLink, '://') >= 0) {
                    $signature = "<a href='${submitterLink}'>${submitterName}</a>";
                } else {
                    // Assume HTTPS if protocol is not given. Hello 2022!
                    $signature = "<a href='https://${submitterLink}'>${submitterName}</a>";
                }
            }
            
            $body .= "<br><br>${signature}";
        }

        if ($this->blogService->canAddComment($userID, $entryID)) {
            $this->blogService->addComment(0, $userID, $entryID, $this->format($body));
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
