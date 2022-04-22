<?php

namespace Szczyglis\ChainParser\Helper;

/**
 * Class TextTools
 * @package Szczyglis\ChainParser\Helper
 */
class TextTools
{
    /**
     * @param string $separator
     * @param $input
     * @return array
     * @throws \Exception
     */
    public static function explode(string $separator, $input)
    {
        $isRegex = false;
        $check = '/.+/';
        if (preg_match('~^' . $check . '$~', trim($separator))) {
            $isRegex = true;
        }

        if (!$isRegex) {
            return explode($separator, (string)$input);
        } else {
            if (!self::isPattern($separator)) {
                throw new \InvalidArgumentException('Separator pattern is invalid.');
            }
            $splitter = 'x' . md5(random_bytes(32));
            $data = preg_replace($separator, '$1' . $splitter, (string)$input);
            return explode($splitter, $data);
        }
    }

    /**
     * @param $pattern
     * @return bool
     */
    public static function isPattern($pattern)
    {
        if (preg_match('~^/[^\/]+/$~', trim($pattern))) {
            return true;
        }
    }

    /**
     * @param string $joiner
     * @param array $ary
     * @return string
     */
    public static function implode(string $joiner, array &$ary)
    {
        return implode($joiner, $ary);
    }

    /**
     * @param $separator
     * @return string
     */
    public static function prepareSeparator($separator)
    {
        return (string)str_replace('\n', "\n", $separator);
    }

    /**
     * @param $from
     * @param $to
     * @param $data
     * @return string|string[]
     */
    public static function strReplace($from, $to, $data)
    {
        return str_replace($from, $to, $data);
    }

    /**
     * @param $data
     * @param null $tags
     * @return string
     */
    public static function stripTags($data, $tags = null)
    {
        return strip_tags($data);
    }

    /**
     * @param $input
     * @return string
     */
    public static function prepareInput($input)
    {
        return (string)$input;
    }

    /**
     * @param $input
     * @return string
     */
    public static function trim($input)
    {
        return trim($input);
    }

    public static function makeDataset($input, $sepRowset, $sepRow, $sepCol)
    {
        $result = [];
        if (!empty($sepRowset)) {
            $rowsets = self::explode($sepRowset, $input);
        } else {
            $rowsets = [0 => $input];
        }        
        foreach ($rowsets as $i => $rowset) {
            if (!empty($sepRow)) {
                $rows = self::explode($sepRow, $rowset);
            } else {
                $rows = [0 => $rowset];
            }            
            foreach ($rows as $j => $row) {
                if (!empty($sepCol)) {
                    $cols = self::explode($sepCol, $row);
                } else {
                    $cols = [0 => $row];
                }                
                $result[$i][$j] = $cols;
            }
        }
        return $result;
    }

    public static function packDataset($dataset, $sepRowset, $sepRow, $sepCol)
    {
        if (empty($sepRowset)) {
            $sepRowset = '';
        }
        if (empty($sepRow)) {
            $sepRow = '';
        }
        if (empty($sepCol)) {
            $sepCol = '';
        }
        $result = '';
        $sets = [];
        foreach ($dataset as $i => $rowset) {
            $rows = [];
            foreach ($rowset as $j => $row) {
                $cols = implode($sepCol, $row);
                $rows[] = $cols;
            }
            $sets[] = implode($sepRow, $rows);
        }
        $result = implode($sepRowset, $sets);
        return $result;
    }

    public static function onDataset($dataset, $callback)
    {
        foreach ($dataset as $i => $rowset) {
            $rows = [];
            foreach ($rowset as $j => $row) {
                foreach ($row as $k => $col) {
                    $dataset[$i][$j][$k] = $callback($col);
                }
            }
        }
        return $dataset;
    }
}