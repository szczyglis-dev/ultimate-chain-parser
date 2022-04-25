<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Helper\Traits;

use Szczyglis\ChainParser\Contract\LoggerInterface;

/**
 * Trait LoggerTrait
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
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