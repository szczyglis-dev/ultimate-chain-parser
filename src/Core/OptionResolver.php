<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Core;

use Szczyglis\ChainParser\Contract\OptionResolverInterface;

/**
 * Class OptionResolver
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
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