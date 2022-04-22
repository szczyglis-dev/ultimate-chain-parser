<?php

namespace Szczyglis\ChainParser\OptionResolver;

use Szczyglis\ChainParser\Contract\OptionResolverInterface;

/**
 * Class SingleLineResolver
 * @package Szczyglis\ChainParser\OptionResolver
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