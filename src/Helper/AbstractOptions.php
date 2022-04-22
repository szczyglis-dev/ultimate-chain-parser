<?php

namespace Szczyglis\ChainParser\Helper;

/**
 * Class AbstractOptions
 * @package Szczyglis\ChainParser\Helper
 */
abstract class AbstractOptions
{
    protected $resolvers = [];
    protected $resolveMap = [];

    /**
     * @param array $resolvers
     */
    public function setResolvers(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * @param array $map
     */
    public function setResolveMap(array $map)
    {
        $this->resolveMap = $map;
    }
}