<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class SettingsSkiAction extends Action
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function execute() : array
    {
        $this->log->debug('Running SettingsSkiAction');
        // A photo of a person in a ski mask. Nothing to do here!
        return Action::NO_DATA;
    }
}
