<?php

namespace Szczyglis\ChainParser\Helper\Traits;

use Szczyglis\ChainParser\Contract\ConfigInterface;

/**
 * Trait ConfigTrait
 * @package Szczyglis\ChainParser\Helper\Traits
 */
trait ConfigTrait
{
    protected $config;

    /**
     * @param ConfigInterface $config
     * @return $this
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;

        return $this;
    }
}