<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class HeaderAction extends Action
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function execute() : array
    {
        $this->log->debug('Running HeaderAction');

		$blog = $this->getBlog();
		$login = $blog->getLogin();
		$blogName = $blog->getUserData(User::KEY_BLOG_NAME, $login);
		$entriesPerPage = (int) $blog->getUserData(User::KEY_ENTRIES_PER_PAGE, '10');
        
        // $google = $blog->getUserData(User::KEY_GOOGLE, 'Y');
        // if ($google === 'N') {
            $robots = '';
        // }

        $title = "$blogName - Sarok.org";
        $rss = $this->context->getPath();

        if ($rss[strlen($rss) - 1] !== '/') {
            $rss .= '/rss/';
        } else {
            $rss .= 'rss/';
        }

        $entries = $this->context->getBlogEntries();
        $params = $this->context->getBlogParams();
        return compact('entriesPerPage', 'robots', 'title', 'rss', 'entries', 'params');
    }
}
