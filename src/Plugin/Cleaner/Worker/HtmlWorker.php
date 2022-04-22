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
    public function stripTags(array $dataset)
    {
        $callback = function($data) {
            return TextTools::stripTags($data);
        };

        return $this->onDataset($dataset, $callback);
    }
}