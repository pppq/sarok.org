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
    private const SETTINGS_MENU = [
        [ 'name' => 'Adatok',                  'url' => '/settings/'         ],
        [ 'name' => 'Blog',                    'url' => '/settings/blog/'    ],
        [ 'name' => 'Barátok',                 'url' => '/settings/friends/' ],
        [ 'name' => 'Képek',                   'url' => '/settings/images/'  ],
        [ 'name' => 'Térkép',                  'url' => '/settings/map/'     ],
        [ 'name' => 'Külső',                   'url' => '/settings/skin/'    ],
        [ 'name' => 'Import/Export/Varázslat', 'url' => '/settings/other/'   ],
        [ 'name' => 'Statisztika',             'url' => '/settings/stats/'   ],
        [ 'name' => 'Snowboardos arc',         'url' => '/settings/ski/'     ],
    ];

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

        if ($this->context->isPOST()) {
            // TODO: POST requests should update corresponding settings
            $this->setTemplateName('empty');
        } else {
            parent::init();
            $this->context->setProperty(Context::PROP_MENU_ITEMS, self::SETTINGS_MENU);
        }

        $this->addAction('main', $action);
    }
}
