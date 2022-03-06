<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\ActionPage;
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

class SettingsActionPage extends ActionPage
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        $actionMap = array(
            "blog" => SettingsBlogAction::class,
            // "uploads" => SettingsUploadsAction::class,
            "friends" => SettingsFriendsAction::class,
            "skin" => SettingsSkinAction::class,
            "ski" => SettingsSkiMaskAction::class,
            "images" => SettingsImagesAction::class,
            "magic" => SettingsMagicAction::class,
            "map" => SettingsMapAction::class,
            "other" => SettingsOtherAction::class,
            "stats" => SettingsStatsAction::class,
            "makeMagic" => SettingsMakeMagicAction::class,
            "import" => SettingsImportAction::class,
            "makeImport" => SettingsMakeImportAction::class,
        );

        $firstSegment = $this->context->getPathSegment(0);

        if (!isset($actionMap[$firstSegment])) {
            $action = SettingsInfoAction::class;
        } else {
            $action = $actionMap[$firstSegment];
        }

        if ($this->context->isPOST()) {
            // TODO: POST requests should update corresponding settings
            $this->setTemplateName("empty");
        } else {
            $menu = array(
                array('name' => 'Adatok', 'url' => '/settings/' ),
                array('name' => 'Blog', 'url' => '/settings/blog/' ),
                array('name' => 'Barátok', 'url' => '/settings/friends/' ),
                array('name' => 'Képek', 'url' => '/settings/images/' ),
                array('name' => 'Térkép', 'url' => '/settings/map/' ),
                array('name' => 'Külső', 'url' => '/settings/skin/' ),
                array('name' => 'Import/Export/Varázslat', 'url' => '/settings/other/' ),
                array('name' => 'Statisztika', 'url' => '/settings/stats/' ),
                array('name' => 'Snowboardos arc', 'url' => '/settings/ski/' ),
            );

            $this->context->setProperty(Context::PROP_MENU_ITEMS, $menu);
        }
    }
}
