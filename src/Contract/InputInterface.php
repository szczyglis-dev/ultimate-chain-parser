<?php

namespace Szczyglis\ChainParser\Contract;

/**
 * Interface InputInterface
 * @package Szczyglis\ChainParser\Contract
 */
interface InputInterface
{
    public function getInput();

    public function getDataset();

    /**
     * @param $input
     * @return mixed
     */
    public function setInput($input);

    /**
     * @param $dataset
     * @return mixed
     */
    public function setDataset($dataset);

    public function read();
}