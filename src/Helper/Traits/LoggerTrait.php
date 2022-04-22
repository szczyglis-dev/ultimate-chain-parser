<?php

namespace Szczyglis\ChainParser\Helper\Traits;

use Szczyglis\ChainParser\Contract\LoggerInterface;

/**
 * Trait LoggerTrait
 * @package Szczyglis\ChainParser\Helper\Traits
 */
trait LoggerTrait
{
    protected $loggers = [];
    protected $log;

    /**
     * @param LoggerInterface $logger
     */
    public function addLogger(LoggerInterface $logger)
    {
        $name = $logger->getName();
        $this->loggers[$name] = $logger;
    }

    /**
     * @param string $message
     */
    public function log(string $message)
    {
        foreach ($this->loggers as $logger) {
            $logger->addMessage($message);
        }
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        $logs = [];
        foreach ($this->loggers as $name => $logger) {
            $logs[$name] = $logger->getMessages();
        }
        return $logs;
    }

    /**
     * @param callable $callback
     */
    public function setLoggerCallback(callable $callback)
    {
        $this->log = $callback;
    }
}