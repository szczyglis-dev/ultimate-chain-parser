<?php

namespace Szczyglis\ChainParser\Contract;

/**
 * Interface RendererInterface
 * @package Szczyglis\ChainParser\Contract
 */
interface RendererInterface
{
    /**
     * @param array $output
     * @return mixed
     */
    public function setOutput(array $output);

    /**
     * @param ConfigInterface $config
     * @return mixed
     */
    public function setConfig(ConfigInterface $config);

    public function renderOutput();

    public function renderData();

    public function renderLog();
}