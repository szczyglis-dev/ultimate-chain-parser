<?php

namespace Szczyglis\ChainParser\Input;

use Szczyglis\ChainParser\Contract\InputInterface;

/**
 * Class TextInput
 * @package Szczyglis\ChainParser\Input
 */
class TextInput implements InputInterface
{
    private $input;
    private $dataset = [];

    /**
     * TextInput constructor.
     * @param string $input
     * @param array $dataset
     */
    public function __construct(string $input, array $dataset = [])
    {
        $this->input = $input;
        $this->dataset = $dataset;
    }

    /**
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param $input
     * @return string
     */
    public function setInput($input)
    {
        $this->input = $input;
    }

    /**
     * @return string
     */
    public function read()
    {
        return $this->input;
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
}