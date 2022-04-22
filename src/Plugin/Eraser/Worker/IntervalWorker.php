<?php

namespace Szczyglis\ChainParser\Plugin\Eraser\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class IntervalWorker
 * @package Szczyglis\ChainParser\Plugin\Eraser\Worker
 */
class IntervalWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $data
     * @param int $interval
     * @return array
     */
    public function erase(array &$data, int $interval)
    {
        $result = [];
        $n = 1;
        $i = 0;
        $c = count($data);
        foreach ($data as $block) {
            if ($n % $interval == 0) {
                $this->log(sprintf('Erasing block with interval [%u / %u]', $n, $c));
                $i++;
            } else {
                $result[] = $block;
            }
            $n++;
        }
        return $result;
    }
}