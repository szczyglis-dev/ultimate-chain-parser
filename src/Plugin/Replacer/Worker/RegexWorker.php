<?php

namespace Szczyglis\ChainParser\Plugin\Replacer\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class RegexWorker
 * @package Szczyglis\ChainParser\Plugin\Replacer\Worker
 */
class RegexWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $data
     * @param array $patterns
     * @param int $interval
     * @param array $ranges
     * @param string $mode
     * @return array
     */
    public function replace(array &$data, array &$patterns, int $interval, array &$ranges, string $mode)
    {
        switch ($mode) {
            case 'rowset':
                return $this->replaceRowsets($data, $patterns, $interval, $ranges, $mode);
                break;
            case 'row':
                return $this->replaceRows($data, $patterns, $interval, $ranges, $mode);
                break;
            case 'column':
                return $this->replaceCols($data, $patterns, $interval, $ranges, $mode);
                break;
        }
    }

    /**
     * @param array $data
     * @param array $patterns
     * @param int $interval
     * @param array $ranges
     * @param string $mode
     * @return array
     */
    public function replaceRowsets(array &$data, array &$patterns, int $interval, array &$ranges, string $mode)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        $n = 1;
        $isReplace = true;
        foreach ($dataset as $i => $rows) {
            if (!empty($ranges)) {
                if (!$this->inRange($ranges, $i)) {
                    $this->log(sprintf('Ignoring rowset not in range: [%u]', $i));
                    $isReplace = false;
                }
            }
            if ($interval != 1) {
                if ($n % $interval != 0) {
                    $this->log(sprintf('Ignoring rowset not in interval: [%u]', $i));
                    $isReplace = false;
                }
            }

            $tmpRows = [];
            foreach ($rows as $j => $row) {
                $tmpRow = [];
                foreach ($row as $k => $col) {
                    if ($isReplace) {
                        $col = $this->applyPatterns($patterns, $col);
                    }
                    if (is_string($k)) {
                        $tmpRow[$k] = $col;
                    } else {
                        $tmpRow[] = $col;
                    }
                }
                if (!empty($tmpRow)) {
                    $tmpRows[] = $tmpRow;
                }

            }
            if (!empty($tmpRows)) {
                $tmp[] = $tmpRows;
            }
            $n++;
        }
        return $tmp;
    }

    /**
     * @param array $data
     * @param array $patterns
     * @param int $interval
     * @param array $ranges
     * @param string $mode
     * @return array
     */
    public function replaceRows(array &$data, array &$patterns, int $interval, array &$ranges, string $mode)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            $n = 1;
            $isReplace = true;
            foreach ($rows as $j => $row) {
                if (!empty($ranges)) {
                    if (!$this->inRange($ranges, $j)) {
                        $this->log(sprintf('Ignoring row not in range: [%u][%u]', $i, $j));
                        $isReplace = false;
                    }
                }
                if ($interval != 1) {
                    if ($n % $interval != 0) {
                        $this->log(sprintf('Ignoring row not in interval: [%u][%u]', $i, $j));
                        $isReplace = false;
                    }
                }
                $tmpRow = [];
                foreach ($row as $k => $col) {
                    if ($isReplace) {
                        $col = $this->applyPatterns($patterns, $col);
                    }
                    $tmpRow[] = $col;
                }
                if (!empty($tmpRow)) {
                    $tmpRows[] = $tmpRow;
                }
                $n++;
            }
            if (!empty($tmpRows)) {
                $tmp[] = $tmpRows;
            }
        }
        return $tmp;
    }

    /**
     * @param array $data
     * @param array $patterns
     * @param int $interval
     * @param array $ranges
     * @param string $mode
     * @return array
     */
    public function replaceCols(array &$data, array &$patterns, int $interval, array &$ranges, string $mode)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            foreach ($rows as $j => $row) {
                $tmpRow = [];
                $n = 1;
                foreach ($row as $k => $col) {
                    $isReplace = true;
                    if (!empty($ranges)) {
                        if (!$this->inRange($ranges, $k)) {
                            $this->log(sprintf('Ignoring column not in range: [%u][%u][%u]', $i, $j, $k));
                            $isReplace = false;
                        }
                    }
                    if ($interval != 1) {
                        if ($n % $interval != 0) {
                            $this->log(sprintf('Ignoring column not in interval: [%u][%u][%u]', $i, $j, $k));
                            $isReplace = false;
                        }
                    }
                    if ($isReplace) {
                        $col = $this->applyPatterns($patterns, $col);
                    }
                    $tmpRow[] = $col;
                    $n++;
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
}