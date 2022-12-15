<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Service\UserService;

final class HeaderAction extends Action
{
    private UserService $userService;

    public function __construct(Logger $logger, Context $context, UserService $userService)
    {
        parent::__construct($logger, $context);
        $this->userService = $userService;
    }

    public function execute() : array
    {
        $this->log->debug('Executing HeaderAction');

		$blog = $this->getBlog();
		$login = $blog->getLogin();
        
        $this->userService->populateUserData($blog, 
            User::KEY_BLOG_NAME, 
            User::KEY_ENTRIES_PER_PAGE,
            User::KEY_GOOGLE
        );

		$blogName = $blog->getUserData(User::KEY_BLOG_NAME, $login);
		$entriesPerPage = (int) $blog->getUserData(User::KEY_ENTRIES_PER_PAGE, '10');
        
        // $google = $blog->getUserData(User::KEY_GOOGLE, 'Y');
        // if ($google === 'N') {
            $robots = '';
        // }

        $title = "${blogName} - Sarok.org";
        $rss = $this->getPath();

        if ($rss[strlen($rss) - 1] !== '/') {
            $rss .= '/rss/';
        } else {
            $rss .= 'rss/';
        }

        $params = $this->getPathParams();

        return compact(
            'entriesPerPage', 
            'robots', 
            'title', 
            'rss', 
            'params'
        );
    }
}
