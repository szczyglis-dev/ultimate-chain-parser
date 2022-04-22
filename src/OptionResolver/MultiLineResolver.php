<?php

namespace Szczyglis\ChainParser\OptionResolver;

use Szczyglis\ChainParser\Contract\OptionResolverInterface;

/**
 * Class MultiLineResolver
 * @package Szczyglis\ChainParser\OptionResolver
 */
class MultiLineResolver implements OptionResolverInterface
{
    const NAME = 'multiline';

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
        $value = str_replace("\r\n", "\n", $value);
        $lines = explode("\n", $value);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $match = [];
            if (preg_match('/^([a-zA-Z0-9]+):(.+)/u', $line, $match)) {
                $key = trim($match[1]);
                $value = trim($match[2]);

                if (!isset($ary[$key])) {
                    $ary[$key] = [];
                }
                $ary[$key][] = $this->matchAssignment($value);
            } else {
                $ary[] = $this->matchAssignment($line);
            }
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