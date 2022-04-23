<?php

namespace Szczyglis\ChainParser\Helper\Traits;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Core\DataBag;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Trait ToolsTrait
 * @package Szczyglis\ChainParser\Helper\Traits
 */
trait DatasetTrait
{
    /**
     * @param array $dataset
     * @param callable $callback
     * @return array
     */
    public function iterateDataset(array $dataset, callable $callback)
    {
        foreach ($dataset as $i => $rowset) {
            $rows = [];
            foreach ($rowset as $j => $row) {
                foreach ($row as $k => $col) {
                    $dataset[$i][$j][$k] = $callback($col);
                }
            }
        }
        return $dataset;
    }

    /**
     * @param string|null $input
     * @return array
     */
    public function unpack(?string $input)
    {
        if (is_null($input)) {
            $input = '';
        }

        $rowset = $this->getSeparator('input', 'rowset');
        $row = $this->getSeparator('input', 'row');
        $col = $this->getSeparator('input', 'col');

        return $this->makeDataset($input, $rowset, $row, $col);
    }

    /**
     * @param string $mode
     * @param string $type
     * @return string
     */
    public function getSeparator(string $mode, string $type)
    {
        $k = 'sep_' . $mode . '_' . $type;
        return (string)str_replace(['\n', '\r', '\t'], ["\n", "\r", "\t"], $this->getOption($k));
    }

    /**
     * @param string|null $input
     * @param string $sepRowset
     * @param string $sepRow
     * @param string $sepCol
     * @return array
     */
    public function makeDataset(?string $input, string $sepRowset, string $sepRow, string $sepCol)
    {
        $result = [];
        if (!empty($sepRowset)) {
            $rowsets = $this->explode($sepRowset, $input);
        } else {
            $rowsets = [0 => $input];
        }
        foreach ($rowsets as $i => $rowset) {
            if (!empty($sepRow)) {
                $rows = $this->explode($sepRow, $rowset);
            } else {
                $rows = [0 => $rowset];
            }
            foreach ($rows as $j => $row) {
                if (!empty($sepCol)) {
                    $cols = $this->explode($sepCol, $row);
                } else {
                    $cols = [0 => $row];
                }
                $result[$i][$j] = $cols;
            }
        }
        return $result;
    }

    /**
     * @param array $dataset
     * @return string
     */
    public function pack(array $dataset)
    {
        $rowset = $this->getSeparator('output', 'rowset');
        $row = $this->getSeparator('output', 'row');
        $col = $this->getSeparator('output', 'col');

        return $this->packDataset($dataset, $rowset, $row, $col);
    }

    /**
     * @param array $dataset
     * @param string $sepRowset
     * @param string $sepRow
     * @param string $sepCol
     * @return string
     */
    public function packDataset(array $dataset, string $sepRowset, string $sepRow, string $sepCol)
    {
        if (empty($sepRowset)) {
            $sepRowset = '';
        }
        if (empty($sepRow)) {
            $sepRow = '';
        }
        if (empty($sepCol)) {
            $sepCol = '';
        }
        $result = '';
        $sets = [];
        foreach ($dataset as $i => $rowset) {
            $rows = [];
            foreach ($rowset as $j => $row) {
                $cols = implode($sepCol, $row);
                $rows[] = $cols;
            }
            $sets[] = implode($sepRow, $rows);
        }
        $result = implode($sepRowset, $sets);
        return $result;
    }
}