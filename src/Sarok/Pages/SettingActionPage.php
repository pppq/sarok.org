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

class SettingsAP extends ActionPage
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function canExecute() : bool
    {
        return $this->context->getProperty(Context::PROP_IS_LOGGED_IN);
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

        $path = $this->context->getPathSegment(0);

        if (!isset($actionMap[$path])) {
            $action = SettingsInfoAction::class;
        } else {
            $action = $actionMap[$path];
        }

        if ($this->context->isPOST()) {
            // TODO: POST requests should update corresponding settings
            $this->setTemplateName("empty");
        }
    }
}
