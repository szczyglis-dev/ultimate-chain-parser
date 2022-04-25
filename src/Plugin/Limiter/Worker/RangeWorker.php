<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Plugin\Limiter\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class RangeWorker
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class RangeWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $data
     * @param array $ranges
     * @param string $mode
     * @return array
     */
    public function limitAllowed(array &$data, array $ranges, string $mode)
    {
        switch ($mode) {
            case 'rowset':
                return $this->allowRowsets($ranges, $data);
                break;
            case 'row':
                return $this->allowRows($ranges, $data);
                break;
            case 'column':
                return $this->allowCols($ranges, $data);
                break;
        }
    }

    /**
     * @param array $ranges
     * @return array
     */
    public function allowRowsets(array $ranges)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            if (!$this->inRange($ranges, $i)) {
                $this->log(sprintf('Removing not in range rowset [%u]', $i));
                continue;
            }
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
     * @param array $ranges
     * @return array
     */
    public function allowRows(array $ranges)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            foreach ($rows as $j => $row) {
                if (!$this->inRange($ranges, $j)) {
                    $this->log(sprintf('Removing not in range row [%u]', $j));
                    continue;
                }
                $tmpRow = [];
                foreach ($row as $k => $col) {
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
        }
        return $tmp;
    }

    /**
     * @param array $ranges
     * @return array
     */
    public function allowCols(array $ranges)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            foreach ($rows as $j => $row) {
                $tmpRow = [];
                foreach ($row as $k => $col) {
                    if ($this->inRange($ranges, $k)) {
                        $tmpRow[] = $col;
                    } else {
                        $this->log(sprintf('Removing not in range column [%u => %u => %u]', $i, $j, $k));
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
     * @param array $ranges
     * @param string $mode
     * @return array
     */
    public function limitDenied(array &$data, array $ranges, string $mode)
    {
        switch ($mode) {
            case 'rowset':
                return $this->denyRowsets($ranges, $data);
                break;
            case 'row':
                return $this->denyRows($ranges, $data);
                break;
            case 'column':
                return $this->denyCols($ranges, $data);
                break;
        }
    }

    /**
     * @param array $ranges
     * @return array
     */
    public function denyRowsets(array $ranges)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            if ($this->inRange($ranges, $i)) {
                $this->log(sprintf('Removing not in range rowset [%u]', $i));
                continue;
            }
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
     * @param array $ranges
     * @return array
     */
    public function denyRows(array $ranges)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            foreach ($rows as $j => $row) {
                if ($this->inRange($ranges, $j)) {
                    $this->log(sprintf('Removing not in range row [%u]', $j));
                    continue;
                }
                $tmpRow = [];
                foreach ($row as $k => $col) {
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
        }
        return $tmp;
    }

    /**
     * @param array $ranges
     * @return array
     */
    public function denyCols(array $ranges)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            foreach ($rows as $j => $row) {
                $tmpRow = [];
                foreach ($row as $k => $col) {
                    if (!$this->inRange($ranges, $k)) {
                        $tmpRow[] = $col;
                    } else {
                        $this->log(sprintf('Removing not in range column [%u => %u => %u]', $i, $j, $k));
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