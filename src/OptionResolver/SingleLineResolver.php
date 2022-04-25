<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\OptionResolver;

use Szczyglis\ChainParser\Contract\OptionResolverInterface;

/**
 * Class SingleLineResolver
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class SingleLineResolver implements OptionResolverInterface
{
    const NAME = 'singleline';

    /**
     * @param string $key
     * @param $value
     * @return array
     */
    public function resolve(string $key, $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $ary = [];
        $value = trim($value);
        $fields = explode(",", $value);

        $i = 0;
        foreach ($fields as $field) {
            $value = trim($field);
            if (empty($value)) {
                continue;
            }

            $ary[$i] = $this->matchAssignment($value);
            $i++;
        }

        return $ary;
    }

    /**
     * @param string $value
     * @return array|string|string
     */
    private function matchAssignment(string $value)
    {
        $result = '';
        $match = [];
        if (preg_match('/^([^=>]+)=>([^=>]+)/u', $value, $match)) {
            $tmpFrom = trim($match[1]);
            $tmpTo = trim($match[2]);
            if (preg_match('/^"(.*)"$/u', $tmpTo, $match)) {
                $tmpTo = $match[1];
            }
            $result = [
                'pattern' => $tmpFrom,
                'replacement' => $tmpTo,
            ];
        } else {
            $result = $value;
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }
}