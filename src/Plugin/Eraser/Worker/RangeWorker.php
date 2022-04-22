<?php

namespace Szczyglis\ChainParser\Plugin\Eraser\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class RangeWorker
 * @package Szczyglis\ChainParser\Plugin\Eraser\Worker
 */
class RangeWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $data
     * @param array $ranges
     * @return array
     */
    public function erase(array &$data, array &$ranges)
    {
        $output = [];
        $tmp = [];
        foreach ($ranges as $k) {
            // single
            if (!is_array($k)) {
                if (isset($data[$k])) {
                    $tmp[$k] = $data[$k];
                    $this->log(sprintf('Range matched: %s', $k));
                }
            } else {
                // range
                if (!is_null($k['from']) && !is_null($k['to'])) {
                    if ($k['from'] <= $k['to']) {
                        for ($i = $k['from']; $i <= $kt['to']; $i++) {
                            $tmp[$i] = $data[$i];
                            $this->log(sprintf('Range matched [%u-%u] : %u', $k['from'], $k['to'], $i));
                        }
                    }
                } else if (!is_null($k['from'])) {
                    $max = count($data);
                    if ($k['from'] < $max) {
                        for ($i = $k['from']; $i < $max; $i++) {
                            $tmp[$i] = $data[$i];
                            $this->log(sprintf('Range matched: [%u>] : %u', $k['from'], $i));
                        }
                    }
                } else if (!is_null($k['to'])) {
                    $max = count($data);
                    if ($k['to'] < $max) {
                        for ($i = 0; $i < $k['to']; $i++) {
                            $tmp[$i] = $data[$i];
                            $this->log(sprintf('Range matched: [<%u] : %u', $k['to'], $i));
                        }
                    }
                }
            }
        }
        $this->log(sprintf('Merging ranges...'));
        $i = 1;
        foreach ($tmp as $item) {
            $output[] = $item;
            $this->log(sprintf('Creating block [%u]', $i));
            $i++;
        }
        return $output;
    }
}