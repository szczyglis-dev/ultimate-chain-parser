<?php

namespace Szczyglis\ChainParser\Plugin\Cleaner\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class TrimerWorker
 * @package Szczyglis\ChainParser\Plugin\Cleaner\Worker
 */
class TrimerWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $dataset
     * @return array
     */
    public function applyTrim(array $dataset)
    {
        $callback = function ($data) {
            return $this->trim($data);
        };

        return $this->iterateDataset($dataset, $callback);
    }
}