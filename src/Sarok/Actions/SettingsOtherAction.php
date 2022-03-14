<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\UserService;
use Sarok\Service\RssService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class SettingsOtherAction extends Action
{
    private UserService $userService;
    private RssService $rssService;

    public function __construct(
        Logger $logger,
        Context $context,
        UserService $userService,
        RssService $rssService
    ) {
        parent::__construct($logger, $context);
        $this->userService = $userService;
        $this->rssService = $rssService;
    }

    public function execute() : array
    {
        $this->log->debug('Running SettingsOtherAction');
        
        $user = $this->getUser();

        if ($this->isPOST()) {
            return $this->update($user);
        }

        list($friendListOnly, $toMainPage, $trackMe, $rss, $wysiwyg, $rssSecret) = $this->userService->populateUserData($user,
            User::KEY_FRIEND_LIST_ONLY,
            User::KEY_TO_MAIN_PAGE,
            User::KEY_TRACK_ME,
            User::KEY_RSS,
            User::KEY_WYSIWYG,
            User::KEY_RSS_SECRET);

        $login = $user->getLogin();
    
        return compact('friendListOnly', 'toMainPage', 'trackMe', 'rss', 'wysiwyg', 'login', 'rssSecret');
    }

    private function update(User $user) : array
    {
        $friendListOnly = $this->getPOST(User::KEY_FRIEND_LIST_ONLY);
        if (isset($friendListOnly)) {
            $user->setUserData(User::KEY_FRIEND_LIST_ONLY, $friendListOnly);
        }
        
        $toMainPage = $this->getPOST(User::KEY_TO_MAIN_PAGE);
        if (isset($toMainPage)) {
            $user->setUserData(User::KEY_TO_MAIN_PAGE, $toMainPage);
        }

        $wysiwyg = $this->getPOST(User::KEY_WYSIWYG);
        if (isset($wysiwyg)) {
            $user->setUserData(User::KEY_WYSIWYG, $wysiwyg);
        }

        $rss = trim($this->getPOST(User::KEY_RSS));
        list($existingRss) = $this->userService->populateUserData($user, User::KEY_RSS);
        if (isset($rss) && $existingRss !== $rss && $this->rssService->isValidFeed($rss)) {
            $this->log->debug('RSS is valid, updating');

            $this->rssService->deleteFeed($user->getID());
            $this->rssService->addFeed($user->getID(), $rss);
            $user->setUserData(User::KEY_RSS, $rss);
        }

        $pass1 = $this->getPOST('pass1');
        $pass2 = $this->getPOST('pass2');
        if (isset($pass1) && $pass1 === $pass2 && strlen($pass1) > 3) {
            $user->setPass($pass1);
        }
        
        $this->userService->saveUser($user);
        
        $this->setTemplateName('empty');
        $location = $this->getPOST('location', '/settings/other');
        return compact('location');
    }
}
