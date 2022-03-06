<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class ErrorAction extends Action
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function execute() : array
    {
        $this->log->debug("Running ErrorAction");
        // We render a static template on error that uses no variables
        return Action::NO_DATA;
    }
}
