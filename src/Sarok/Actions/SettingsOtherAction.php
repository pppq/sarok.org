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
        RssService $rssService)
    {
        parent::__construct($logger, $context);
        $this->userService = $userService;
        $this->rssService = $rssService;
    }

    public function execute() : array
    {
        $this->log->debug('Running SettingsOtherAction');
        
        $user = $this->context->getUser();
        
		$this->userService->populateUserData($user, 
			User::KEY_FRIEND_LIST_ONLY, 
			User::KEY_TO_MAIN_PAGE,
			User::KEY_TRACK_ME,
			User::KEY_RSS,
			User::KEY_WYSIWYG
		);

        if ($this->context->isPostRequest()) {
            return update($user);
        }
        
		$friendListOnly = $user->getUserData(User::KEY_FRIEND_LIST_ONLY);
		$toMainPage = $user->getUserData(User::KEY_TO_MAIN_PAGE);
		$trackMe = $user->getUserData(User::KEY_TRACK_ME);
		$rss = $user->getUserData(User::KEY_RSS);
		$wysiwyg = $user->getUserData(User::KEY_WYSIWYG);
        $login = $user->getLogin();
        $rssKey = $user->getRssKey();

        return compact('friendListOnly', 'toMainPage', 'trackMe', 'rss', 'wysiwyg', 'login', 'rssKey');
    }

    public function update(User $user) : array
    {
        $friendListOnly = $this->context->getPost(User::KEY_FRIEND_LIST_ONLY);
        if (isset($friendListOnly)) {
			$user->setUserData(User::KEY_FRIEND_LIST_ONLY, $friendListOnly);
		}
		
		$toMainPage = $this->context->getPost(User::KEY_TO_MAIN_PAGE);
        if (isset($toMainPage)) {
			$user->setUserData(User::KEY_TO_MAIN_PAGE, $toMainPage);
		}

        $wysiwyg = $this->context->getPost(User::KEY_WYSIWYG);
		if (isset($wysiwyg)) {
            $user->setUserData(User::KEY_WYSIWYG, $wysiwyg);
        }

        $rss = trim($this->context->getPost(User::KEY_RSS));
        if (isset($rss) && $user->getUserData(User::KEY_RSS) !== $rss && $this->rssService->isValidFeed($rss)) {
            $this->log->debug('RSS is valid, updating');

            $this->rssService->deleteFeed($user->getID());
            $this->rssService->addFeed($user->getID(), $rss);
            $user->setUserData(User::KEY_RSS, $rss);
        }

        $pass1 = $this->context->getPost('pass1');
        $pass2 = $this->context->getPost('pass2');
        if (isset($pass1) && $pass1 === $pass2 && strlen($pass1) > 3) {
            $user->setPass($pass1);
        }
        
        $this->userService->saveUser($user);
        
        $this->setTemplateName('empty');
        $location = $this->context->getPost('location', '/settings/other');
        return compact('location');
    }
}
