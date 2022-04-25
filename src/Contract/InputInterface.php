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

/**
 * Interface InputInterface
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
interface InputInterface
{
    public function getInput();

    public function getDataset();

    /**
     * @param $input
     * @return mixed
     */
    public function setInput($input);

    /**
     * @param $dataset
     * @return mixed
     */
    public function setDataset($dataset);

    public function read();
}