<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

final class EmptyAction extends Action
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function execute() : array
    {
        $this->log->debug('Executing EmptyAction');
        return Action::NO_DATA;
    }
}
