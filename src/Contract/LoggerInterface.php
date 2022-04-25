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

use Szczyglis\ChainParser\Core\DataBag;

/**
 * Interface LoggerInterface
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
interface LoggerInterface
{
    /**
     * @param string $message
     * @param array $additionalData
     * @return mixed
     */
    public function addMessage(string $message, array $additionalData = []);

    /**
     * @return array
     */
    public function getMessages(): array;

    public function onBegin();

    public function onEnd();

    public function onIterationBegin();

    public function onIterationEnd();

    /**
     * @param DataBag $data
     * @return mixed
     */
    public function setData(DataBag $data);

    /**
     * @return string
     */
    public function getName(): string;
}