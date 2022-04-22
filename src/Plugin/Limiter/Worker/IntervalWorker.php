<?php

namespace Szczyglis\ChainParser\Plugin\Limiter\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class IntervalWorker
 * @package Szczyglis\ChainParser\Plugin\Limiter\Worker
 */
class IntervalWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $data
     * @param int $interval
     * @return array
     */
    public function limitToAllowed(array &$data, int $interval)
    {
        $result = [];
        $n = 1;
        $c = count($data);
        foreach ($data as $block) {
            if ($n % $interval == 0) {
                $this->log(sprintf('Matched interval [%u / %u] >>%s<<', $n, $c, $block));
                $result[] = $block;
            }
            $n++;
        }
        return $result;
    }
}