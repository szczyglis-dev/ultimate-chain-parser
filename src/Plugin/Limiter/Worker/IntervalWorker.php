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
 * Class IntervalWorker
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class IntervalWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $data
     * @param int $interval
     * @param string $mode
     * @return array
     */
    public function limitAllowed(array &$data, int $interval, string $mode)
    {
        switch ($mode) {
            case 'rowset':
                return $this->allowRowsets($interval, $data);
                break;
            case 'row':
                return $this->allowRows($interval, $data);
                break;
            case 'column':
                return $this->allowCols($interval, $data);
                break;
        }
    }

    /**
     * @param int $interval
     * @return array
     */
    public function allowRowsets(int $interval)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        $n = 1;
        foreach ($dataset as $i => $rows) {
            if ($n % $interval != 0) {
                $this->log(sprintf('Removing not in interval rowset [%u]', $i));
                $n++;
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
            $n++;
        }
        return $tmp;
    }

    /**
     * @param int $interval
     * @return array
     */
    public function allowRows(int $interval)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            $n = 1;
            foreach ($rows as $j => $row) {
                if ($n % $interval != 0) {
                    $this->log(sprintf('Removing not in interval row [%u]', $j));
                    $n++;
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
                $n++;
            }
            if (!empty($tmpRows)) {
                $tmp[] = $tmpRows;
            }
        }
        return $tmp;
    }

    /**
     * @param int $interval
     * @return array
     */
    public function allowCols(int $interval)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            foreach ($rows as $j => $row) {
                $tmpRow = [];
                $n = 1;
                foreach ($row as $k => $col) {
                    if ($n % $interval != 0) {
                        $this->log(sprintf('Removing not in interval column [%u => %u => %u]', $i, $j, $k));
                    } else {
                        $tmpRow[] = $col;
                    }
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

    /**
     * @param array $data
     * @param int $interval
     * @param string $mode
     * @return array
     */
    public function limitDenied(array &$data, int $interval, string $mode)
    {
        switch ($mode) {
            case 'rowset':
                return $this->denyRowsets($interval, $data);
                break;
            case 'row':
                return $this->denyRows($interval, $data);
                break;
            case 'column':
                return $this->denyCols($interval, $data);
                break;
        }
    }

    /**
     * @param int $interval
     * @return array
     */
    public function denyRowsets(int $interval)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        $n = 1;
        foreach ($dataset as $i => $rows) {
            if ($n % $interval == 0) {
                $this->log(sprintf('Removing not in interval rowset [%u]', $i));
                $n++;
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
            $n++;
        }
        return $tmp;
    }

    /**
     * @param int $interval
     * @return array
     */
    public function denyRows(int $interval)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            $n = 1;
            foreach ($rows as $j => $row) {
                if ($n % $interval == 0) {
                    $this->log(sprintf('Removing not in interval row [%u]', $j));
                    $n++;
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
                $n++;
            }
            if (!empty($tmpRows)) {
                $tmp[] = $tmpRows;
            }
        }
        return $tmp;
    }

    /**
     * @param int $interval
     * @return array
     */
    public function denyCols(int $interval)
    {
        $dataset = $this->getDataset();

        $tmp = [];
        foreach ($dataset as $i => $rows) {
            $tmpRows = [];
            foreach ($rows as $j => $row) {
                $tmpRow = [];
                $n = 1;
                foreach ($row as $k => $col) {
                    if ($n % $interval == 0) {
                        $this->log(sprintf('Removing not in interval column [%u => %u => %u]', $i, $j, $k));
                    } else {
                        $tmpRow[] = $col;
                    }
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