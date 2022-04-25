<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Contract;

use Szczyglis\ChainParser\Contract\LoggerInterface;

/**
 * Interface LoggableInterface
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
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