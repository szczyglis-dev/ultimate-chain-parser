<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Core;

/**
 * Class OutputResult
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class OutputResult
{
    private $output;
    private $data;
    private $log;

    public function getResult()
    {

    }

    public function getData()
    {

    }

    /**
     * @param mixed $data
     *
     * @return self
     */
    public function setData(OutputData $data)
    {
        $this->data = $data;

        return $this;
    }

    public function getLog()
    {

    }

    /**
     * @param mixed $log
     *
     * @return self
     */
    public function setLog(OutputData $log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * @param mixed $output
     *
     * @return self
     */
    public function setOutput(OutputData $output)
    {
        $this->output = $output;

        return $this;
    }
}