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
use Szczyglis\ChainParser\Contract\WorkerInterface;

/**
 * Interface PluginInterface
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
interface PluginInterface
{
    /**
     * @param DataBag $data
     * @return mixed
     */
    public function setData(DataBag $data);

    public function getData();

    public function getName();

    public function init();

    public function run();

    public function end();

    public function getIteration();

    public function getConfig();

    /**
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value);

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param string $key
     * @return mixed
     */
    public function has(string $key);

    /**
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function setPrev(string $key, $value);

    /**
     * @param string $key
     * @return mixed
     */
    public function getPrev(string $key);

    /**
     * @param string $key
     * @return mixed
     */
    public function hasPrev(string $key);

    /**
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function setVar(string $key, $value);

    /**
     * @param string $key
     * @return mixed
     */
    public function getVar(string $key);

    public function getVars();

    /**
     * @param string $key
     * @return mixed
     */
    public function hasVar(string $key);

    public function getOptions();

    /**
     * @param string $key
     * @return mixed
     */
    public function getOption(string $key);

    /**
     * @param string $key
     * @return mixed
     */
    public function hasOption(string $key);

    /**
     * @param string $name
     * @param \Szczyglis\ChainParser\Contract\WorkerInterface $worker
     * @return mixed
     */
    public function addWorker(string $name, WorkerInterface $worker);

    /**
     * @param string $name
     * @return mixed
     */
    public function getWorker(string $name);
}