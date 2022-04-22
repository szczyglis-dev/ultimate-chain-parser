<?php

namespace Szczyglis\ChainParser\Core;

/**
 * Class OutputResult
 * @package Szczyglis\ChainParser\Core
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