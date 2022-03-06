<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;

abstract class Action
{
    protected const NO_DATA = array();

    protected Logger $logger;
    protected Context $context;

    public function __construct(Logger $logger, Context $context)
    {
        $this->logger = $logger;
        $this->context = $context;
    }

    abstract public function execute() : array;
}
