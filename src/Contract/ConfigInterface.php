<?php

namespace Szczyglis\ChainParser\Contract;

/**
 * Interface ConfigInterface
 * @package Szczyglis\ChainParser\Contract
 */
interface ConfigInterface
{
    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value);

    /**
     * @param string $key
     * @return mixed
     */
    public function has(string $key);

    public function all();
}