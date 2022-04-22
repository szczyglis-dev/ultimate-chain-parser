<?php

namespace Szczyglis\ChainParser\Config;

use Szczyglis\ChainParser\Contract\ConfigInterface;

/**
 * Class ArrayConfig
 * @package Szczyglis\ChainParser\Config
 */
class ArrayConfig implements ConfigInterface
{
    private $raw;
    private $parsed;
    private $isInitialized = false;

    /**
     * ArrayConfig constructor.
     * @param array $raw
     */
    public function __construct(array $raw)
    {
        $this->raw = $raw;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $this->parse();

        if (array_key_exists($key, $this->parsed)) {
            return $this->parsed[$key];
        }
    }

    public function parse()
    {
        if ($this->isInitialized) {
            return;
        }

        $this->parsed = $this->raw;

        $this->isInitialized = true;
    }

    /**
     * @param string $key
     * @param $value
     * @return mixed|void
     */
    public function set(string $key, $value)
    {
        $this->parse();

        $this->parsed[$key] = $value;
    }

    /**
     * @param string $key
     * @return bool|mixed
     */
    public function has(string $key)
    {
        $this->parse();

        if (array_key_exists($key, $this->parsed)) {
            return true;
        }
    }

    public function all()
    {
        $this->parse();

        return $this->parsed;
    }
}