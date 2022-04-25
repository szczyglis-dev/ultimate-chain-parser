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

/**
 * Trait WorkerLoggerTrait
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
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