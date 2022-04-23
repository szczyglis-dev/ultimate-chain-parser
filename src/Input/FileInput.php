<?php

namespace Szczyglis\ChainParser\Input;

use Szczyglis\ChainParser\Contract\InputInterface;

/**
 * Class FileInput
 * @package Szczyglis\ChainParser\Input
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