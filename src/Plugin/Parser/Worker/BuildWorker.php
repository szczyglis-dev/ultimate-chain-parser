<?php

namespace Szczyglis\ChainParser\Plugin\Parser\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class BuildWorker
 * @package Szczyglis\ChainParser\Plugin\Parser\Worker
 */
class BuildWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $rowsets
     * @return string
     */
    public function build(array &$rowsets): string
    {
        $outputFields = $this->getVar('output_fields');

        $sepRecord = TextTools::prepareSeparator($this->getOption('output_record_separator'));
        $sepField = TextTools::prepareSeparator($this->getOption('output_field_separator'));
        $placeholder = $this->getOption('empty_field_placeholder');
        if (!(bool)$this->getOption('is_empty_field_placeholder')) {
            $placeholder = '';
        }
        if (empty($sepRecord) && $sepRecord != " ") {
            $this->log('Warning: no output record separator specified!');
        }
        if (empty($sepField) && $sepField != " ") {
            $this->log('Warning: no output field separator specified!');
        }

        $result = [];
        foreach ($rowsets as $rowset) {
            foreach ($rowset as $record) {
                $cols = [];
                foreach ($outputFields as $field) {
                    $cols[$field] = '';
                    if (isset($record[$field])) {
                        $cols[$field] = $record[$field];
                    }
                    if (empty($cols[$field]) && !empty($placeholder)) {
                        $cols[$field] = $placeholder;
                    }
                }
                $result[] = TextTools::implode($sepField, $cols);
            }
        }

        return TextTools::implode($sepRecord, $result);
    }
}