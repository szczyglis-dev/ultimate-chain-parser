<?php

namespace Szczyglis\ChainParser\Plugin\Replacer\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class RangeWorker
 * @package Szczyglis\ChainParser\Plugin\Replacer\Worker
 */
class RangeWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $data
     * @param array $ranges
     * @return array
     */
    public function calc(array &$data, array &$ranges)
    {
        $ary = [];
        foreach ($ranges as $k) {
            // single
            if (!is_array($k)) {
                if (isset($data[$k])) {
                    $ary[] = $k;
                }
            } else {
                // range
                if (!is_null($k['from']) && !is_null($k['to'])) {
                    if ($k['from'] <= $k['to']) {
                        for ($i = $k['from']; $i <= $k['to']; $i++) {
                            $ary[] = $i;
                        }
                    }
                } else if (!is_null($k['from'])) {
                    $max = count($data);
                    if ($k['from'] < $max) {
                        for ($i = $k['from']; $i < $max; $i++) {
                            $ary[] = $i;
                        }
                    }
                } else if (!is_null($k['to'])) {
                    $max = count($data);
                    if ($k['to'] < $max) {
                        for ($i = 0; $i < $k['to']; $i++) {
                            $ary[] = $i;
                        }
                    }
                }
            }
        }
        return $ary;
    }
}