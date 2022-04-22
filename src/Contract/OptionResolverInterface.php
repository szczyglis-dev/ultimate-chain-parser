<?php

namespace Szczyglis\ChainParser\Contract;

/**
 * Interface OptionResolverInterface
 * @package Szczyglis\ChainParser\Contract
 */
interface OptionResolverInterface
{
    /**
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function resolve(string $key, $value);

    public function getName();
}