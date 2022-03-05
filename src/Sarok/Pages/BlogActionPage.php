<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\ActionPage;
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

class BlogActionPage extends ActionPage
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        $this->setTemplateName("blog");
        $firstSegment = $this->context->getPathSegment(0);

        if (preg_match('/^m_[0-9]+$', $firstSegment)) {
            $secondSegment = $this->context->getPathSegment(1);
            $action = EntryReadAction::class;

            if ($secondSegment === 'edit') {
                $action = EntryEditAction::class;
            } else if ($this->context->isPOST()) {
                switch ($secondSegment) {
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
                        // This is just a fallback for POST requests (you don't get RSS or map output this way)
                        $action = EntryListAction::class; 
                        break;
                }
            }
        } else if ($firstSegment === 'new') {
            $action = EntryNewAction::class;
        } else if ($firstSegment === 'info') {
            $action = EntryInfoAction::class;
        } else if ($this->context->isPOST() && $firstSegment === 'update') {
            $action = EntryUpdateAction::class;
        } else {
            $action = EntryListAction::class;

            $lastSegment = $this->context->getLastPathSegment(0);
            $beforeLastSegment = $this->context->getLastPathSegment(1);
    
            if ($lastSegment === 'rss' || $beforeLastSegment === 'rss') {
                // Render list of entries to RSS XML if requested
                $this->setTemplateName("rss");
            } else if ($lastSegment === 'map') {
                // Show pins on the map for geotagged entries
                $action = EntryMapAction::class;
            }
        }

        $this->addAction("main", $action);
        $this->addAction("calendar", MonthAction::class);
        $this->addAction("navigation", NavigationAction::class);
        $this->addAction("sidebar", SidebarAction::class);
        $this->addAction("header", HeaderAction::class);
        $this->addAction("header", CustomCssAction::class);
    }
}
