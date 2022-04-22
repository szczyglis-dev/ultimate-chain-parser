<?php

namespace Szczyglis\ChainParser\Contract;

use Szczyglis\ChainParser\Core\DataBag;

/**
 * Interface WorkerInterface
 * @package Szczyglis\ChainParser\Contract
 */
interface WorkerInterface
{
    /**
     * @param DataBag $data
     * @return mixed
     */
    public function setData(DataBag $data);

    public function getData();

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
     * @return mixed
     */
    public function getWorker(string $name);
}