<?php

namespace Szczyglis\ChainParser\Core;

use Szczyglis\ChainParser\Contract\WorkerInterface;

/**
 * Class DataBag
 * @package Szczyglis\ChainParser\Core
 */
class DataBag
{
    private $element;
    private $prevData = [];
    private $data = [];
    private $log = [];
    private $vars = [];
    private $workers = [];

    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param ChainElement $element
     * @return $this
     */
    public function setElement(ChainElement $element)
    {
        $this->element = $element;

        return $this;
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function set(string $key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        if (array_key_exists($key, $this->data)) {
            return true;
        }
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setPrev(string $key, $value)
    {
        $this->prevData[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getPrev(string $key)
    {
        if (array_key_exists($key, $this->prevData)) {
            return $this->prevData[$key];
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasPrev(string $key)
    {
        if (array_key_exists($key, $this->prevData)) {
            return true;
        }
    }

    /**
     * @return array
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param array $log
     * @return $this
     */
    public function setLog(array $log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setVar(string $key, $value)
    {
        $this->vars[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getVar(string $key)
    {
        if (array_key_exists($key, $this->vars)) {
            return $this->vars[$key];
        }
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasVar(string $key)
    {
        if (array_key_exists($key, $this->vars)) {
            return true;
        }
    }

    /**
     * @param string $id
     * @param WorkerInterface $worker
     * @return $this
     */
    public function addWorker(string $id, WorkerInterface $worker)
    {
        $this->workers[$id] = $worker;

        return $this;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function getWorker(string $id)
    {
        if (array_key_exists($id, $this->workers)) {
            return $this->workers[$id];
        }
    }
}