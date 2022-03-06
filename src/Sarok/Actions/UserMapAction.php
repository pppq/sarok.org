<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\SettingsMapAction;
use Sarok\Actions\Action;

class UserMapAction extends Action
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function execute(): array
    {
        $this->log->debug('Running UserMapAction');

        // Delegate to SettingsMapAction (but don't expose any controls, just the map for viewing)
        $settingsMapAction = $this->context->getAction(SettingsMapAction::class);
        return $settingsMapAction->execute();
    }
}
