<?php

namespace Szczyglis\ChainParser\Helper\Traits;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Core\DataBag;

/**
 * Trait DataTrait
 * @package Szczyglis\ChainParser\Helper\Traits
 */
trait DataTrait
{
    protected $data;

    public function getData()
    {
        return $this->data;
    }

    /**
     * @param DataBag $data
     * @return $this
     */
    public function setData(DataBag $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIteration()
    {
        return $this->data->getElement()->getIteration();
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->data->getElement()->getConfig();
    }

    /**
     * @return mixed
     */
    public function getElement()
    {
        return $this->data->getElement();
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function has(string $key)
    {
        return $this->data->has($key);
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setPrev(string $key, $value)
    {
        $this->data->setPrev($key, $value);

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getPrev(string $key)
    {
        return $this->data->getPrev($key);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function hasPrev(string $key)
    {
        return $this->data->hasPrev($key);
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setVar(string $key, $value)
    {
        $this->data->setVar($key, $value);

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getVar(string $key)
    {
        return $this->data->getVar($key);
    }

    /**
     * @return mixed
     */
    public function getVars()
    {
        return $this->data->getVars();
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function hasVar(string $key)
    {
        return $this->data->hasVar($key);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getOption(string $key)
    {
        return $this->data->getElement()->getOptions()->get($key);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function hasOption(string $key)
    {
        return $this->data->getElement()->getOptions()->has($key);
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->data->getElement()->getOptions()->all();
    }

    /**
     * @param array $log
     * @return $this
     */
    public function setLog(array $log)
    {
        $this->data->setLog($log);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLog()
    {
        return $this->data->getLog();
    }

    /**
     * @param string $id
     * @param WorkerInterface $worker
     * @return $this
     */
    public function addWorker(string $id, WorkerInterface $worker)
    {
        $this->data->addWorker($id, $worker);

        return $this;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function getWorker(string $id)
    {
        return $this->data->getWorker($id);
    }

    /**
     * @return mixed
     */
    public function getDataset()
    {
        return $this->get('dataset');
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->data->get($key);
    }

    /**
     * @param $dataset
     * @return DataTrait
     */
    public function setDataset($dataset)
    {
        return $this->set('dataset', $dataset);
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function set(string $key, $value)
    {
        $this->data->set($key, $value);

        return $this;
    }
}