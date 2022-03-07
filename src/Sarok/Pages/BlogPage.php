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

class BlogPage extends Page
{
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
            $entryID = (int) $matches[1];
            $this->context->setEntryID($entryID);
            
            $secondSegment = $this->context->getPathSegment(1);
            $action = EntryReadAction::class;

            if ($secondSegment === 'edit') {
                $action = EntryEditAction::class;
            } else if ($this->context->isPostRequest()) {
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
        } else if ($this->context->isPostRequest() && $firstSegment === 'update') {
            $needsDefaultActions = false;
            $action = EntryUpdateAction::class;
        } else {
            $action = EntryListAction::class;

            $lastSegment = $this->context->getPathSegment(-1);
            $beforeLastSegment = $this->context->getPathSegment(-2);
    
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
                new MenuItem('Bejegyzés irása',   "/users/$newLogin/new/"),
                new MenuItem('Level irasa',       '/privates/new/'),
                new MenuItem('Beallitasok',       '/settings/'),
                new MenuItem('Könyjelzők',        '/favourites/'),
                new MenuItem('Páciensek listája', '/about/pacients/'),
            );

            $this->setTemplateName('blog');
        } else {
            $this->setTemplateName('empty');
        }

        $this->addAction('main', $action);
    }
}
