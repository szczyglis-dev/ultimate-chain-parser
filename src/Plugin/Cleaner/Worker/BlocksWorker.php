<?php

namespace Szczyglis\ChainParser\Plugin\Cleaner\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class BlocksWorker
 * @package Szczyglis\ChainParser\Plugin\Cleaner\Worker
 */
class BlocksWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function cleanEmpty(array &$data)
    {
        $result = [];
        $c = count($data) - 1;
        foreach ($data as $i => $block) {
            if (!empty(TextTools::trim($block))) {
                $result[] = $block;
            } else {
                $this->log(sprintf('Removing empty block [%u / %u]', $i, $c));
            }
        }
        return $result;
    }

    /**
     * @param string $data
     * @return string|string[]
     */
    public function fixNewLines(string &$data)
    {
        return TextTools::strReplace("\r\n", "\n", $data);
    }
}