<?php

namespace Szczyglis\ChainParser\Plugin\Replacer\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class RegexWorker
 * @package Szczyglis\ChainParser\Plugin\Replacer\Worker
 */
class RegexWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $data
     * @param array $patterns
     * @param int $interval
     * @param array $ranges
     * @return void
     */
    public function replaceBlocks(array &$data, array &$patterns, int $interval, array &$ranges)
    {
        $n = 1;
        foreach ($data as $i => $block) {
            if (!empty($ranges) && !in_array($i, $ranges)) {
                $this->log(sprintf('Ignoring. Range not match [%u]', $i));
                continue;
            }

            if ($interval != 1 && $n % $interval != 0) {
                $this->log(sprintf('Ignoring. Interval not match [%u]', $i));
                $n++;
                continue;
            }
            foreach ($patterns as $pattern) {
                if (count($pattern) != 2) {
                    $this->log(sprintf('Invalid pattern option format!'));
                    continue;
                }
                $block = preg_replace($pattern['pattern'], $pattern['replacement'], $block);
                $this->log(sprintf('Executed pattern: %s => %s', $pattern['pattern'], $pattern['replacement']));
            }
            $data[$i] = $block;
            $n++;
        }
    }

    /**
     * @param string $input
     * @param array $patterns
     */
    public function replaceAll(string &$input, array &$patterns)
    {
        foreach ($patterns as $pattern) {
            if (count($pattern) != 2) {
                $this->log(sprintf('Invalid pattern option'));
                continue;
            }
            if (!TextTools::isPattern($pattern['pattern'])) {
                $this->log(sprintf('Warning: Invalid pattern: %s. Aborting!', $pattern['pattern']));
                continue;
            }
            $input = preg_replace($pattern['pattern'], $pattern['replacement'], $input);
            $this->log(sprintf('Executed pattern: %s => %s', $pattern['pattern'], $pattern['replacement']));
        }
    }
}