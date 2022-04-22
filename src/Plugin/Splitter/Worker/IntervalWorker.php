<?php

namespace Szczyglis\ChainParser\Plugin\Splitter\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class IntervalWorker
 * @package Szczyglis\ChainParser\Plugin\Splitter\Worker
 */
class IntervalWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $data
     * @param int $interval
     * @param string $separator
     * @return array
     */
    public function split(array $data, int $interval, string $separator)
    {
        $result = [];
        $tmp = [];
        $n = 1;
        $i = 0;
        foreach ($data as $line) {
            $tmp[$i][] = $line;
            if ($n % $interval == 0) {
                $this->log(sprintf('Split step [%u]', $i));
                $i++;
            }
            $n++;
        }

        $this->log(sprintf('Merging blocks...'));
        foreach ($tmp as $i => $lines) {
            $result[] = implode("\n", $lines);
            $this->log(sprintf('Merged block [%u]', $i));
        }

        return $result;
    }
}