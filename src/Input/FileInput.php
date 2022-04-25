<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Input;

use Szczyglis\ChainParser\Contract\InputInterface;

/**
 * Class FileInput
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class FileInput implements InputInterface
{
    private $path;
    private $dataset = [];

    /**
     * FileInput constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return false|string
     */
    public function read()
    {
        return file_get_contents($this->path);
    }

    /**
     * @return string
     */
    public function getInput()
    {
        return file_get_contents($this->path);
    }

    /**
     * @return array
     */
    public function getDataset()
    {
        return $this->dataset;
    }

    /**
     * @param $dataset
     * @return mixed|void
     */
    public function setDataset($dataset)
    {
        $this->dataset = $dataset;
    }

    /**
     * @param $input
     * @return string
     */
    public function setInput($input)
    {
        $this->input = $input;
    }
}