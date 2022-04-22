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
        $tmp = [];
        foreach ($dataset as $i => $rowset) {            
            $tmpRows = [];
            foreach ($rowset as $j => $row) {
                $tmpRow = [];
                foreach ($row as $k => $col) {
                    if (!empty(TextTools::trim($col))) {
                        $tmpRow[] = $col;
                    } else {
                        $this->log(sprintf('Removing empty column [%u => %u => %u]', $i, $j, $k));
                    }                   
                }
                if (!empty($tmpRow)) {
                    $tmpRows[] = $tmpRow;
                }            
            }
            if (!empty($tmpRows)) {
                $tmp[] = $tmpRows;
            }
        }
        return $tmp;
    }

    public function fixLines(array $dataset)
    {
        $callback = function($data) {
            return TextTools::strReplace("\r", "", $data);
        };

        return $this->onDataset($dataset, $callback);
    }    
}