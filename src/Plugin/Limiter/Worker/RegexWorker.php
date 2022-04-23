<?php

namespace Szczyglis\ChainParser\Plugin\Limiter\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class RegexWorker
 * @package Szczyglis\ChainParser\Plugin\Limiter\Worker
 */
class RegexWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $data
     * @param array $patterns
     * @param string $mode
     * @return array
     */
    public function limitAllowed(array &$data, array $patterns, string $mode)
    {
        $dataset = $this->getDataset();

        switch ($mode) {
            case 'rowset':
                return $this->allowRowsets($patterns, $data);
                break;
            case 'row':
                return $this->allowRows($patterns, $data);
                break;
            case 'column':
                return $this->allowCols($patterns, $data);
                break;
        }
    }

    /**
     * @param array $patterns
     * @return array
     */
    public function allowRowsets(array $patterns)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            $isMatched = false;
            foreach ($rows as $j => $row) {
                $tmpRow = [];
                foreach ($row as $k => $col) {
                    if (is_string($k)) {
                        $tmpRow[$k] = $col;
                    } else {
                        $tmpRow[] = $col;
                    }
                    if ($this->checkPatterns($patterns, $col)) {
                        $isMatched = true;
                    }
                }
                if (!empty($tmpRow)) {
                    $tmpRows[] = $tmpRow;
                }
            }
            if ($isMatched) {
                if (!empty($tmpRows)) {
                    $tmp[] = $tmpRows;
                }
            } else {
                $this->log(sprintf('Removing not matched rowset [%u]', $i));
            }
        }
        return $tmp;
    }

    /**
     * @param array $patterns
     * @return array
     */
    public function allowRows(array $patterns)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            foreach ($rows as $j => $row) {
                $isMatched = false;
                $tmpRow = [];
                foreach ($row as $k => $col) {
                    if (is_string($k)) {
                        $tmpRow[$k] = $col;
                    } else {
                        $tmpRow[] = $col;
                    }
                    if ($this->checkPatterns($patterns, $col)) {
                        $isMatched = true;
                    }
                }
                if ($isMatched) {
                    if (!empty($tmpRow)) {
                        $tmpRows[] = $tmpRow;
                    }
                } else {
                    $this->log(sprintf('Removing not matched row [%u => %u]', $i, $j));
                }
            }
            if (!empty($tmpRows)) {
                $tmp[] = $tmpRows;
            }
        }
        return $tmp;
    }

    /**
     * @param array $patterns
     * @return array
     */
    public function allowCols(array $patterns)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            foreach ($rows as $j => $row) {
                $tmpRow = [];
                foreach ($row as $k => $col) {
                    if ($this->checkPatterns($patterns, $col)) {
                        if (is_string($k)) {
                            $tmpRow[$k] = $col;
                        } else {
                            $tmpRow[] = $col;
                        }
                    } else {
                        $this->log(sprintf('Removing not matched column [%u => %u => %u]', $i, $j, $k));
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

    /**
     * @param array $data
     * @param array $patterns
     * @param string $mode
     * @return array
     */
    public function limitDenied(array &$data, array $patterns, string $mode)
    {
        $dataset = $this->getDataset();

        switch ($mode) {
            case 'rowset':
                return $this->denyRowsets($patterns, $data);
                break;
            case 'row':
                return $this->denyRows($patterns, $data);
                break;
            case 'column':
                return $this->denyCols($patterns, $data);
                break;
        }
    }

    /**
     * @param array $patterns
     * @return array
     */
    public function denyRowsets(array $patterns)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            $isMatched = false;
            foreach ($rows as $j => $row) {
                $tmpRow = [];
                foreach ($row as $k => $col) {
                    if (is_string($k)) {
                        $tmpRow[$k] = $col;
                    } else {
                        $tmpRow[] = $col;
                    }
                    if (!$this->checkPatterns($patterns, $col)) {
                        $isMatched = true;
                    }
                }
                if (!empty($tmpRow)) {
                    $tmpRows[] = $tmpRow;
                }
            }
            if ($isMatched) {
                if (!empty($tmpRows)) {
                    $tmp[] = $tmpRows;
                }
            } else {
                $this->log(sprintf('Removing not matched rowset [%u]', $i));
            }
        }
        return $tmp;
    }

    /**
     * @param array $patterns
     * @return array
     */
    public function denyRows(array $patterns)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            foreach ($rows as $j => $row) {
                $isMatched = false;
                $tmpRow = [];
                foreach ($row as $k => $col) {
                    if (is_string($k)) {
                        $tmpRow[$k] = $col;
                    } else {
                        $tmpRow[] = $col;
                    }
                    if (!$this->checkPatterns($patterns, $col)) {
                        $isMatched = true;
                    }
                }
                if ($isMatched) {
                    if (!empty($tmpRow)) {
                        $tmpRows[] = $tmpRow;
                    }
                } else {
                    $this->log(sprintf('Removing not matched row [%u => %u]', $i, $j));
                }
            }
            if (!empty($tmpRows)) {
                $tmp[] = $tmpRows;
            }
        }
        return $tmp;
    }

    /**
     * @param array $patterns
     * @return array
     */
    public function denyCols(array $patterns)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            foreach ($rows as $j => $row) {
                $tmpRow = [];
                foreach ($row as $k => $col) {
                    if (!$this->checkPatterns($patterns, $col)) {
                        if (is_string($k)) {
                            $tmpRow[$k] = $col;
                        } else {
                            $tmpRow[] = $col;
                        }
                    } else {
                        $this->log(sprintf('Removing not matched column [%u => %u => %u]', $i, $j, $k));
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
}