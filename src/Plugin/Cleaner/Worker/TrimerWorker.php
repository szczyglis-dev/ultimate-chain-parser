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
     * @param array $data
     */
    public function trim(array $dataset)
    {
        $callback = function($data) {
            return TextTools::trim($data);
        };

        return $this->onDataset($dataset, $callback);
    }
}