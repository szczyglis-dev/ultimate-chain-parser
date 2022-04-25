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
 * Class RangeResolver
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class RangeResolver implements OptionResolverInterface
{
    const NAME = 'range';

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
            if (empty($value) && $value != 0) {
                continue;
            }
            $match = [];
            if (preg_match('/(.*)-(.*)/', $value, $match)) {
                $tmpFrom = trim($match[1]);
                $tmpTo = trim($match[2]);
                if (empty($tmpFrom) && $tmpFrom != '0') {
                    $tmpFrom = null;
                } else {
                    $tmpFrom = (int)$tmpFrom;
                }
                if (empty($tmpTo) && $tmpTo != '0') {
                    $tmpTo = null;
                } else {
                    $tmpTo = (int)$tmpTo;
                }
                $ary[$i] = [
                    'from' => $tmpFrom,
                    'to' => $tmpTo,
                ];
            } else {
                $ary[$i] = (int)$value;
            }

            $i++;
        }

        return $ary;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }
}