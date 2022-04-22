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
    public function removeEmpty(array $dataset)
    {
        $result = [];
        foreach ($dataset as $i => $rowset) {
            $rows = [];
            foreach ($rowset as $j => $row) {
                foreach ($row as $k => $col) {
                    if (!empty(TextTools::trim($col))) {
                        $result[$i][$j][$k] = $col;
                    } else {
                        $this->log(sprintf('Removing empty column [%u => %u => %u]', $i, $j, $k));
                    }                   
                }
                if (empty($result[$i][$j])) {
                    $this->log(sprintf('Removing empty row [%u => %u]', $i, $j));
                    unset($result[$i][$j]);
                }
            }
        }        
        return $result;
    }

    public function fixLines(array $dataset)
    {
        $callback = function($data) {
            return TextTools::strReplace("\r", "", $data);
        };

        return $this->onDataset($dataset, $callback);
    }    
}