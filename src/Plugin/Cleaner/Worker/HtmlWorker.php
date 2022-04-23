<?php

namespace Szczyglis\ChainParser\Plugin\Cleaner\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class HtmlWorker
 * @package Szczyglis\ChainParser\Plugin\Cleaner\Worker
 */
class HtmlWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $dataset
     * @return array
     */
    public function applyStripTags(array $dataset)
    {
        $callback = function ($data) {
            return $this->stripTags($data);
        };

        return $this->iterateDataset($dataset, $callback);
    }
}