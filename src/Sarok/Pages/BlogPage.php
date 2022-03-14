<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Service\BlogService;
use Sarok\Pages\Page;
use Sarok\Models\MenuItem;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\SidebarAction;
use Sarok\Actions\NavigationAction;
use Sarok\Actions\MonthAction;
use Sarok\Actions\HeaderAction;
use Sarok\Actions\EntryUpdateAction; 
use Sarok\Actions\EntryReadAction;
use Sarok\Actions\EntryNewAction;
use Sarok\Actions\EntryMapAction;
use Sarok\Actions\EntryListAction;
use Sarok\Actions\EntryInfoAction;
use Sarok\Actions\EntryEditAction;
use Sarok\Actions\EntryDeleteAction; 
use Sarok\Actions\EntryAddCommentAction; 
use Sarok\Actions\CustomCssAction;
use Sarok\Models\User;
use Sarok\Service\UserService;

class BlogPage extends Page
{
    private UserService $userService;
    private BlogService $blogService;

    public function __construct(
        Logger $logger, 
        Context $context, 
        UserService $userService, 
        BlogService $blogService
    ) {
        parent::__construct($logger, $context);
        $this->userService = $userService;
        $this->blogService = $blogService;
    }

    private function setBlog(User $blog) : void
    {
        $this->context->setBlog($blog);
    }

    public function init() : void
    {
        $this->logger->debug('Initializing BlogPage');
        // parent::init() is called if $needsDefaultActions is not set to false later down
        $needsDefaultActions = true;

        $firstArg = $this->getPathSegment(0);
        if (strlen($firstArg) === 0 || $firstArg === 'rss' || $firstArg === 'map') {
            $this->setBlog($this->userService->getUserByLogin(User::LOGIN_ALL));
            // process this segment again, below
        } else {
            $this->setBlog($this->userService->getUserByLogin($firstArg));
            $this->removeFirstSegment(); // consume this path segment
        }
        
        $secondArg = $this->getPathSegment(0);

        $matches = array();
        if (preg_match('^m_([1-9][0-9]*)$', $secondArg, $matches)) {
            $entryID = (int) $matches[1];
            $this->context->setEntryID($entryID);
            
            $thirdArg = $this->getPathSegment(1);
            $action = EntryReadAction::class;

            if ($thirdArg === 'edit') {
                $action = EntryEditAction::class;
            } else if ($this->isPOST()) {
                switch ($thirdArg) {
                    case 'update': 
                        $needsDefaultActions = false;
                        $action = EntryUpdateAction::class; 
                        break;

                    case 'delete': 
                        $needsDefaultActions = false;
                        $action = EntryDeleteAction::class; 
                        break;

                    case 'insertcomment': 
                        $needsDefaultActions = false;
                        $action = EntryAddCommentAction::class; 
                        break;

                    default: 
                        // Fallback for POST requests (you don't get RSS or map output using this request method)
                        $action = EntryListAction::class; 
                        break;
                }
            }
        } else if ($secondArg === 'new') {
            $action = EntryNewAction::class;
        } else if ($secondArg === 'info') {
            $action = EntryInfoAction::class;
        } else if ($this->isPOST() && $secondArg === 'update') {
            $needsDefaultActions = false;
            $action = EntryUpdateAction::class;
        } else {
            $action = EntryListAction::class;

            $lastArg = $this->getPathSegment(-1);
            $beforeLastArg = $this->getPathSegment(-2);
    
            if ($lastArg === 'rss' || $beforeLastArg === 'rss') {
                // Render list of entries to RSS XML if requested
                $this->setTemplateName('rss');
            } else if ($lastArg === 'map') {
                // Show pins on the map for geotagged entries
                $action = EntryMapAction::class;
            }
        }

        if ($needsDefaultActions) {
            parent::init();

            $this->addAction(self::TILE_CALENDAR, MonthAction::class);
            $this->addAction(self::TILE_NAVIGATION, NavigationAction::class);
            $this->addAction(self::TILE_SIDEBAR, SidebarAction::class);
            $this->addAction(self::TILE_HEADER, HeaderAction::class);
            $this->addAction(self::TILE_HEADER, CustomCssAction::class);
    
            $user = $this->getUser();
            $blog = $this->getBlog();
            $userID = $user->getID();
            $blogID = $blog->getID();
            
            if ($this->blogService->canEdit($userID, $blogID)) {
                $newLogin = $blog->getLogin();
            } else {
                $newLogin = $user->getLogin();
            }
            
            $this->context->setLeftMenuItems(
                new MenuItem('Bejegyzés irása', "/users/$newLogin/new/"),
                new MenuItem('Level irasa', '/privates/new/'),
                new MenuItem('Beallitasok', '/settings/'),
                new MenuItem('Könyjelzők', '/favourites/'),
                new MenuItem('Páciensek listája', '/about/pacients/'),
            );

            $this->setTemplateName('blog');
        } else {
            $this->setTemplateName('empty');
        }

        $this->addAction(self::TILE_MAIN, $action);
    }
}
