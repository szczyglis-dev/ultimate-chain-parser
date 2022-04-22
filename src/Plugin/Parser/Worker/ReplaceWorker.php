<?php

namespace Szczyglis\ChainParser\Plugin\Parser\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class ReplaceWorker
 * @package Szczyglis\ChainParser\Plugin\Parser\Worker
 */
class ReplaceWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param $block
     * @param $field
     * @return string|string[]|null
     */
    public function applyRegexBefore($block, $field)
    {
        $block = $this->applyBlockRegex('regex_block_before', $block);
        return $this->applyFieldRegex('regex_field_before', $block, $field);
    }

    /**
     * @param $type
     * @param $string
     * @return string|string[]|null
     */
    public function applyBlockRegex($type, $string)
    {
        $patterns = $this->getOption($type);
        if (empty($patterns)) {
            return $string;
        }
        foreach ($patterns as $pattern) {
            if (!isset($pattern['pattern']) || !TextTools::isPattern($pattern['pattern'])) {
                $this->log(sprintf('Warning: Invalid pattern: %s. Aborting!', $pattern['pattern']));
                continue;
            }
            $string = preg_replace($pattern['pattern'], $pattern['replacement'], $string);
        }
        return $string;
    }

    /**
     * @param $type
     * @param $string
     * @param $field
     * @return string|string[]|null
     */
    public function applyFieldRegex($type, $string, $field)
    {
        $patterns = $this->getOption($type);
        if (!isset($patterns[$field])) {
            return $string;
        }
        foreach ($patterns[$field] as $pattern) {
            if (!isset($pattern['pattern']) || !TextTools::isPattern($pattern['pattern'])) {
                $this->log(sprintf('Warning: Invalid pattern: %s. Aborting!', $pattern['pattern']));
                continue;
            }
            $string = preg_replace($pattern['pattern'], $pattern['replacement'], $string);
        }
        return $string;
    }

    /**
     * @param $block
     * @param $field
     * @return string|string[]|null
     */
    public function applyRegexAfter($block, $field)
    {
        $block = $this->applyFieldRegex('regex_field_after', $block, $field);
        return $this->applyBlockRegex('regex_block_after', $block);
    }
}