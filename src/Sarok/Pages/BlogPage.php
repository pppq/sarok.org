<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Service\BlogService;
use Sarok\Pages\Page;
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

class BlogPage extends Page
{
    private const BLOG_MENU_ITEMS = [
        [ 'name' => 'Bejegyzés irása',   'url' => '/'                ],
        [ 'name' => 'Level irasa',       'url' => '/privates/new/'   ],
        [ 'name' => 'Beallitasok',       'url' => '/settings/'       ],
        [ 'name' => 'Könyjelzők',        'url' => '/favourites/'     ],
        [ 'name' => 'Páciensek listája', 'url' => '/about/pacients/' ],
    ];

    private BlogService $blogService;

    public function __construct(Logger $logger, Context $context, BlogService $blogService)
    {
        parent::__construct($logger, $context);
        $this->blogService = $blogService;
    }

    public function init() : void
    {
        $this->logger->debug('Initializing BlogPage');
        // parent::init() is called if $needsDefaultActions is not set to false later down
        $needsDefaultActions = true;
        
        $firstSegment = $this->context->getPathSegment(0);
        $matches = array();
        if (preg_match('/^m_([0-9]+)$', $firstSegment, $matches)) {
            $this->context->setProperty(Context::PROP_ENTRY_ID, $matches[1]);
            
            $secondSegment = $this->context->getPathSegment(1);
            $action = EntryReadAction::class;

            if ($secondSegment === 'edit') {
                $action = EntryEditAction::class;
            } else if ($this->context->isPOST()) {
                switch ($secondSegment) {
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
        } else if ($firstSegment === 'new') {
            $action = EntryNewAction::class;
        } else if ($firstSegment === 'info') {
            $action = EntryInfoAction::class;
        } else if ($this->context->isPOST() && $firstSegment === 'update') {
            $needsDefaultActions = false;
            $action = EntryUpdateAction::class;
        } else {
            $action = EntryListAction::class;

            $lastSegment = $this->context->getLastPathSegment(0);
            $beforeLastSegment = $this->context->getLastPathSegment(1);
    
            if ($lastSegment === 'rss' || $beforeLastSegment === 'rss') {
                // Render list of entries to RSS XML if requested
                $this->setTemplateName('rss');
            } else if ($lastSegment === 'map') {
                // Show pins on the map for geotagged entries
                $action = EntryMapAction::class;
            }
        }

        if ($needsDefaultActions) {
            parent::init();

            $this->addAction('calendar', MonthAction::class);
            $this->addAction('navigation', NavigationAction::class);
            $this->addAction('sidebar', SidebarAction::class);
            $this->addAction('header', HeaderAction::class);
            $this->addAction('header', CustomCssAction::class);
    
            $user = $this->context->getUser();
            $blog = $this->context->getBlog();
            $userID = $user->getID();
            $blogID = $blog->getID();
            
            if ($this->blogService->canEdit($userID, $blogID)) {
                $newLogin = $blog->getLogin();
            } else {
                $newLogin = $user->getLogin();
            }
            
            $menu = self::BLOG_MENU_ITEMS;
            $menu[0]['url'] = "/users/$newLogin/new/";
            $this->context->setProperty(Context::PROP_MENU_ITEMS, $menu);  
            
            $this->setTemplateName('blog');
        } else {
            $this->setTemplateName('empty');
        }

        $this->addAction('main', $action);
    }
}
