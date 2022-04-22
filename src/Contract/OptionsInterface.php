<?php

namespace Szczyglis\ChainParser\Contract;

/**
 * Interface OptionsInterface
 * @package Szczyglis\ChainParser\Contract
 */
interface OptionsInterface
{
    /**
     * @param array $resolvers
     * @return mixed
     */
    public function setResolvers(array $resolvers);

    /**
     * @param array $map
     * @return mixed
     */
    public function setResolveMap(array $map);

    public function getName();
}