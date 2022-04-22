<?php

namespace Szczyglis\ChainParser\Helper;

/**
 * Class DebugOutput
 * @package Szczyglis\ChainParser\Helper
 */
class DebugOutput
{
    /**
     * @param bool $value
     * @return string
     */
    public static function bool(bool $value): string
    {
        return $value ? 'TRUE' : 'FALSE';
    }
}