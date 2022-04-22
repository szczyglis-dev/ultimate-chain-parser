<?php

namespace Szczyglis\ChainParser\Contract;

use Szczyglis\ChainParser\Contract\LoggerInterface;

/**
 * Interface LoggableWorkerInterface
 * @package Szczyglis\ChainParser\Contract
 */
interface LoggableWorkerInterface
{
    /**
     * @param callable $callback
     * @return mixed
     */
    public function setLoggerCallback(callable $callback);
}