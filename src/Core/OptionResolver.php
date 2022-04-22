<?php

namespace Szczyglis\ChainParser\Core;

use Szczyglis\ChainParser\Contract\OptionResolverInterface;

/**
 * Class OptionResolver
 * @package Szczyglis\ChainParser\Core
 */
class OptionResolver
{
    private $resolvers = [];

    /**
     * @param $options
     * @param array $map
     */
    public function resolve(&$options, array &$map)
    {
        foreach ($this->resolvers as $resolver) {
            if (!$resolver instanceof OptionResolverInterface) {
                throw new \RuntimeException('Option resolver must implement OptionResolverInterface.');
            }

            $name = $resolver->getName();
            if (!isset($map[$name])) {
                continue;
            }

            foreach ($options as $k => $v) {
                if (!in_array($k, $map[$name])) {
                    continue;
                }
                $options[$k] = $resolver->resolve($k, $v);
            }
        }
    }

    /**
     * @param array $resolvers
     * @return $this
     */
    public function setResolvers(array $resolvers): self
    {
        $this->resolvers = $resolvers;

        return $this;
    }
}