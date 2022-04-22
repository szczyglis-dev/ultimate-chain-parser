<?php

namespace Szczyglis\ChainParser\OptionResolver;

use Szczyglis\ChainParser\Contract\OptionResolverInterface;

/**
 * Class RangeResolver
 * @package Szczyglis\ChainParser\OptionResolver
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
            if (empty($value)) {
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