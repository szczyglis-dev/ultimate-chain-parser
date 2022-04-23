<?php

namespace Szczyglis\ChainParser\Helper\Traits;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Core\DataBag;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Trait ToolsTrait
 * @package Szczyglis\ChainParser\Helper\Traits
 */
trait TextTrait
{
    /**
     * @param string $separator
     * @param $input
     * @return array
     * @throws \Exception
     */
    public function explode(string $separator, ?string $input)
    {
        $isRegex = false;
        $check = '/.+/';
        if (preg_match('~^' . $check . '$~', trim($separator))) {
            $isRegex = true;
        }

        if (!$isRegex) {
            return explode($separator, (string)$input);
        } else {
            if (!$this->isPattern($separator)) {
                throw new \InvalidArgumentException('Separator pattern is invalid.');
            }
            $splitter = 'x' . md5(random_bytes(32));
            $data = preg_replace($separator, '$1' . $splitter, (string)$input);
            return explode($splitter, $data);
        }
    }

    /**
     * @param string $joiner
     * @param array $ary
     * @return string
     */
    public function implode(string $joiner, array &$ary)
    {
        return implode($joiner, $ary);
    }

    /**
     * @param $from
     * @param $to
     * @param $data
     * @return string|string[]
     */
    public function strReplace($from, $to, $data)
    {
        return str_replace($from, $to, $data);
    }

    /**
     * @param $data
     * @param null $tags
     * @return string
     */
    public function stripTags($data, $tags = null)
    {
        return strip_tags($data);
    }

    /**
     * @param $input
     * @return string
     */
    public function trim($input)
    {
        return trim($input);
    }
}