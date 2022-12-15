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
use Sarok\Models\MenuItem;

final class SettingsPage extends Page
{
    private const ACTION_MAP = array(
        'blog'       => SettingsBlogAction::class,
        // 'uploads'    => SettingsUploadsAction::class,
        'friends'    => SettingsFriendsAction::class,
        'images'     => SettingsImagesAction::class,
        'map'        => SettingsMapAction::class,
        'other'      => SettingsOtherAction::class,
        'ski'        => SettingsSkiMaskAction::class,
        'skin'       => SettingsSkinAction::class,
        'stats'      => SettingsStatsAction::class,
        // We land on these pages using POST requests, but we still need a UI
        'import'     => SettingsImportAction::class,
        'magic'      => SettingsMagicAction::class,
        // These perform the update with POST + redirect (no UI)
        'makeMagic'  => SettingsMakeMagicAction::class,
        'makeImport' => SettingsMakeImportAction::class,
    );

    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        // parent::init() is called below

        $this->logger->debug('Initializing SettingsPage');
        $firstSegment = $this->popFirstSegment();

        if (!isset(self::ACTION_MAP[$firstSegment])) {
            $action = SettingsInfoAction::class;
        } else {
            $action = self::ACTION_MAP[$firstSegment];
        }

        if ($firstSegment === 'makeMagic' || $firstSegment === 'makeImport') {
            $this->setTemplateName('empty');
        } else {
            parent::init();
            
            $this->setLeftMenuItems(
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

        $this->addAction(self::TILE_MAIN, $action);
    }
}
