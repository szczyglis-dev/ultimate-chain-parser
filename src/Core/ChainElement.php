<?php

namespace Szczyglis\ChainParser\Core;

use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\ConfigInterface;
use Szczyglis\ChainParser\Contract\OptionsInterface;

/**
 * Class ChainElement
 * @package Szczyglis\ChainParser\Core
 */
class ChainElement
{
    private $name;
    private $iteration;
    private $instance;
    private $config;
    private $options;

    /**
     * ChainElement constructor.
     * @param string|null $name
     * @param OptionsInterface|null $options
     */
    public function __construct(?string $name = null, ?OptionsInterface $options = null)
    {
        if (!is_null($name) && !is_null($options)) {
            $this->name = $name;
            $this->options = $options;
        }
    }

    /**
     * @return mixed
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getIteration()
    {
        return $this->iteration;
    }

    /**
     * @param int $iteration
     * @return $this
     */
    public function setIteration(int $iteration)
    {
        $this->iteration = $iteration;

        return $this;
    }

    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param PluginInterface $instance
     * @return $this
     */
    public function setInstance(PluginInterface $instance)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfig(): ?ConfigInterface
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     *
     * @return self
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptions(): ?OptionsInterface
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     *
     * @return self
     */
    public function setOptions(OptionsInterface $options)
    {
        $this->options = $options;

        return $this;
    }
}