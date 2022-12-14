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
    private const DEFAULT_SKIP = 20;

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

    public function init() : void
    {
        // parent::init() is called if $templateName is still set to 'blog' later down
        
        $this->logger->debug('Initializing BlogPage');
        $templateName = 'blog';

        // Step 1: Figure out if the first path segment refers to a blog owner
        $firstArg = $this->getPathSegment(0);
        
        if ($firstArg === '' || $firstArg === 'rss' || $firstArg === 'map') {
            // process this segment again (the blog in this context belongs to "all")
            $blogLogin = User::LOGIN_ALL;
        } else {
            // consume this path segment
            $blogLogin = $this->popFirstSegment();
        }

        $blog = $this->userService->getUserByLogin($blogLogin);
        $this->setBlog($blog);
        
        // Step 2: Determine if we are working with a single entry or a list of entries from this blog
        $secondArg = $this->getPathSegment(0);

        $matches = array();
        if (preg_match('^m_([1-9][0-9]*)$', $secondArg, $matches)) {
            $entryID = (int) $matches[1];
            $this->setEntryID($entryID);
            
            // Step 2a: Single entry. Now what do we do with it? The default is "read it":
            $thirdArg = $this->getPathSegment(1);
            $action = EntryReadAction::class;

            if ($thirdArg === 'edit') {
                // Show an editor that can be used to update this entry
                $action = EntryEditAction::class;
            } else if ($this->isPOST()) {
                // Some changes are being submitted that will modify this entry, then redirect
                $templateName = 'empty';

                switch ($thirdArg) {
                    case 'update': 
                        $action = EntryUpdateAction::class; 
                        break;

                    case 'delete': 
                        $action = EntryDeleteAction::class; 
                        break;

                    case 'insertcomment': 
                        $action = EntryAddCommentAction::class; 
                        break;

                    default: 
                        // The change kind was not recognized; we will abandon single entry mode as a fallback
                        $templateName = 'blog';
                        $action = EntryListAction::class; 
                        break;
                }
            }
        } else if ($secondArg === 'new') {
            // Show an editor that can be used to create a new entry
            $action = EntryNewAction::class;
        } else if ($secondArg === 'info') {
            // Show some information about this blog
            $action = EntryInfoAction::class;
        } else if ($this->isPOST() && $secondArg === 'update') {
            // A new entry was submitted for addition
            $templateName = 'empty';
            $action = EntryUpdateAction::class;
        } else {
            // Display a list of entries, unless...
            $action = EntryListAction::class;

            $lastArg = $this->getPathSegment(-1);
            $beforeLastArg = $this->getPathSegment(-2);
    
            if ($lastArg === 'rss' || $beforeLastArg === 'rss') {
                // ...we need to render the page to XML
                $templateName = 'rss';
            } else if ($lastArg === 'map') {
                // ...we need to show pins on the map for geotagged matches
                $action = EntryMapAction::class;
            }
        }

        // Step 2b: It's a list of entries; gather remaining information from the request path
        if ($action === EntryListAction::class) {
            $this->parseBlogParams();
        }

        // Step 3: We need extra actions if the page will generate user-facing content
        if ($templateName === 'blog') {
            parent::init();

            $this->addAction(self::TILE_CALENDAR, MonthAction::class);
            $this->addAction(self::TILE_NAVIGATION, NavigationAction::class);
            $this->addAction(self::TILE_SIDEBAR, SidebarAction::class);
            $this->addAction(self::TILE_HEADER, HeaderAction::class);
            $this->addAction(self::TILE_HEADER, CustomCssAction::class);
    
            /* 
             * Figure out which blog to file a new entry under (this depends on whether the user has write 
             * access to the current blog)
             */
            $user = $this->getUser();
            $userID = $user->getID();
            $blogID = $blog->getID();
            
            if ($this->blogService->canEdit($userID, $blogID)) {
                $newEntryLogin = $blog->getLogin();
            } else {
                $newEntryLogin = $user->getLogin();
            }
            
            $this->setLeftMenuItems(
                new MenuItem('Bejegyzés irása', "/users/${newEntryLogin}/new/"),
                new MenuItem('Level irasa', '/privates/new/'),
                new MenuItem('Beallitasok', '/settings/'),
                new MenuItem('Könyjelzők', '/favourites/'),
                new MenuItem('Páciensek listája', '/about/pacients/'),
            );
        }

        $this->setTemplateName($templateName);
        $this->addAction(self::TILE_MAIN, $action);
    }

    private function parseBlogParams() : void
    {
        // Parse remaining path parameters
        $pathParams = array();

        /* 
         * Display entries from diaries of the user's friends:
         * 
         * - /users/all (everyone is a friend of "all" by default)
         * - /users/xyz/friends
         */
        if ($this->getBlog()->getLogin() === User::LOGIN_ALL || $this->getPathSegment(0) === 'friends') {
            $pathParams['friends'] = true;
            if ($this->getPathSegment(0) === 'friends') {
                $this->popFirstSegment();
            }
        }

        /*
         * Display entries from the selected year/month/day:
         * 
         * - /users/xyz/2022
         * - /users/xyz/2022/03
         * - /users/xyz/2022/03/18
         */
        if (ctype_digit($this->getPathSegment(0))) {
            $pathParams['year'] = (int) $this->popFirstSegment();

            if (ctype_digit($this->getPathSegment(0))) {
                $pathParams['month'] = (int) $this->popFirstSegment();

                if (ctype_digit($this->getPathSegment(0))) {
                    $pathParams['day'] = (int) $this->popFirstSegment();
                }
            }
        }

        /*
         * Display entries matching the search expression with match highlighting:
         * 
         * - /users/xyz/search (keyword is received via POST data)
         * - /users/xyz/search/algernon (keyword is the next path parameter)
         */
        if ($this->getPathSegment(0) === 'search') {
            $pathParams['search'] = true;
            $this->popFirstSegment();

            if ($this->getPathSegment(0) !== '') {
                $pathParams['keyword'] = $this->popFirstSegment();
            } else {
                // Will be empty if not a POST request or the parameter is missing
                $pathParams['keyword'] = $this->getPOST('keyword');
            }
        }

        /*
         * Display entries with matching tag
         * 
         * - /users/xyz/tags (tag is received via POST data)
         * - /users/xyz/tags/howto (tag is the next path parameter)
         */
        if ($this->getPathSegment(0) === 'tags') {
            $pathParams['tags'] = true;
            $this->popFirstSegment();

            if ($this->getPathSegment(0) !== '') {
                $pathParams['tagword'] = $this->popFirstSegment();
            } else {
                // Will be empty if not a POST request or the parameter is missing
                $pathParams['tagword'] = $this->getPOST('tagword');
            }
        }

        /*
         * Skip first "N" matching entries
         * 
         * - /users/xyz/skip/300
         */
        if ($this->popFirstSegment() === 'skip') {
            $skip = $this->popFirstSegment();
            
            if (ctype_digit($skip)) {
                $pathParams['skip'] = (int) $skip;
            } else {
                $pathParams['skip'] = self::DEFAULT_SKIP;
            }
        }

        /* 
         * ps. Combinations are alloved, but only in the order listed above.
         * 
         * - /users/xyz/friend/2022/02/search/hairbrush/skip/50
         */
        $this->setPathParams($pathParams);
    }
}
