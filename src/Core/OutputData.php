<?php

namespace Szczyglis\ChainParser\Core;

/**
 * Class OutputData
 * @package Szczyglis\ChainParser\Core
 */
class OutputData
{
    private $iteration;
    private $name;
    private $data;

    /**
     * OutputData constructor.
     * @param int|null $iteration
     * @param string|null $name
     * @param null $data
     */
    public function __construct(?int $iteration = null, ?string $name = null, $data = null)
    {
        if (!is_null($iteration) && !is_null($name)) {
            $this->iteration = $iteration;
            $this->name = $name;
            $this->data = $data;
        }
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIteration()
    {
        return $this->iteration;
    }

    /**
     * @param mixed $iteration
     *
     * @return self
     */
    public function setIteration($iteration)
    {
        $this->iteration = $iteration;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}