<?php

namespace Szczyglis\ChainParser\Contract;

use Szczyglis\ChainParser\Contract\LoggerInterface;

/**
 * Interface LoggableInterface
 * @package Szczyglis\ChainParser\Contract
 */
interface LoggableInterface
{
    /**
     * @param \Szczyglis\ChainParser\Contract\LoggerInterface $logger
     * @return mixed
     */
    public function addLogger(LoggerInterface $logger);

    /**
     * @param string $message
     * @return mixed
     */
    public function log(string $message);

    public function getLogs();
}