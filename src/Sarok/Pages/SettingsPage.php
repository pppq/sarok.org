<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\SettingsStatsAction;
use Sarok\Actions\SettingsSkinAction;
use Sarok\Actions\SettingsSkiMaskAction;
use Sarok\Actions\SettingsOtherAction;
use Sarok\Actions\SettingsMapAction;
use Sarok\Actions\SettingsMakeMagicAction;
use Sarok\Actions\SettingsMakeImportAction;
use Sarok\Actions\SettingsMagicAction;
use Sarok\Actions\SettingsInfoAction;
use Sarok\Actions\SettingsImportAction;
use Sarok\Actions\SettingsImagesAction;
use Sarok\Actions\SettingsFriendsAction;
use Sarok\Actions\SettingsBlogAction;

class SettingsPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        $this->logger->debug('Initializing SettingsPage');
        // parent::init() is called later
        
        $actionMap = array(
            'blog' => SettingsBlogAction::class,
            // 'uploads' => SettingsUploadsAction::class,
            'friends' => SettingsFriendsAction::class,
            'skin' => SettingsSkinAction::class,
            'ski' => SettingsSkiMaskAction::class,
            'images' => SettingsImagesAction::class,
            'magic' => SettingsMagicAction::class,
            'map' => SettingsMapAction::class,
            'other' => SettingsOtherAction::class,
            'stats' => SettingsStatsAction::class,
            'makeMagic' => SettingsMakeMagicAction::class,
            'import' => SettingsImportAction::class,
            'makeImport' => SettingsMakeImportAction::class,
        );

        $firstSegment = $this->context->getPathSegment(0);

        if (!isset($actionMap[$firstSegment])) {
            $action = SettingsInfoAction::class;
        } else {
            $action = $actionMap[$firstSegment];
        }

        if ($this->context->isPostRequest()) {
            // TODO: POST requests should update corresponding settings
            $this->setTemplateName('empty');
        } else {
            parent::init();
            
            $this->context->setLeftMenuItems(
                new MenuItem('Adatok',                  '/settings/'),
                new MenuItem('Blog',                    '/settings/blog/'),
                new MenuItem('Barátok',                 '/settings/friends/'),
                new MenuItem('Képek',                   '/settings/images/'),
                new MenuItem('Térkép',                  '/settings/map/'),
                new MenuItem('Külső',                   '/settings/skin/'),
                new MenuItem('Import/Export/Varázslat', '/settings/other/'),
                new MenuItem('Statisztika',             '/settings/stats/'),
                new MenuItem('Snowboardos arc',         '/settings/ski/'),
            );
        }

        $this->addAction('main', $action);
    }
}
