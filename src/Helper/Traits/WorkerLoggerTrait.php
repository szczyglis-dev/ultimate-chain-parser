<?php

namespace Szczyglis\ChainParser\Helper\Traits;

/**
 * Trait WorkerLoggerTrait
 * @package Szczyglis\ChainParser\Helper\Traits
 */
trait WorkerLoggerTrait
{
    protected $callback;

    /**
     * @param callable $callback
     */
    public function setLoggerCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param string $message
     */
    public function log(string $message)
    {
        $func = $this->callback;
        $func($message);
    }
}