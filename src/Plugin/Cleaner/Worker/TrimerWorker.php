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
    public function trim(array &$data)
    {
        foreach ($data as $i => $block) {
            $data[$i] = TextTools::trim($block);
        }
    }
}