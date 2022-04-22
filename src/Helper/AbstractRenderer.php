<?php

namespace Szczyglis\ChainParser\Helper;

use Szczyglis\ChainParser\Contract\ConfigInterface;

/**
 * Class AbstractRenderer
 * @package Szczyglis\ChainParser\Helper
 */
abstract class AbstractRenderer
{
    protected $output = [];
    protected $config;

    /**
     * @param mixed $output
     *
     * @return self
     */
    public function setOutput(array $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @param ConfigInterface $config
     *
     * @return self
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;

        return $this;
    }
}