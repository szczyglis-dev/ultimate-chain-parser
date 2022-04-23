<?php

namespace Szczyglis\ChainParser\Plugin\Parser\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class MatchWorker
 * @package Szczyglis\ChainParser\Plugin\Parser\Worker
 */
class MatchWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param string $field
     * @param string $block
     * @return bool
     */
    public function isMatch(string $field, string $block)
    {
        $res = false;
        $patterns = $this->getOption('regex_match');
        if (!isset($patterns[$field])) {
            $this->log(sprintf('[CURRENT] No match pattern for field [%s] in block: >>%s<<, marking as matched.', $field, $block));
            return true;
        }

        return $this->checkPatterns($patterns[$field], $block);
    }

    /**
     * @param $idxRowset
     * @param $idxRow
     * @param $idxBlock
     * @param $idxField
     * @return bool
     */
    public function isNextBlockMatch($idxRowset, $idxRow, $idxBlock, $idxField)
    {
        $dataset = $this->getDataset();

        $idxNextField = $idxField + 1;
        $idxNextBlock = $idxBlock + 1;

        $this->log(sprintf('[MATCHER] Checking is next field [%u] in next block [%u] in row [%u] in set [%u]...', $idxNextField, $idxNextBlock, $idxRow, $idxRowset));

        if (!isset($dataset[$idxRowset][$idxRow][$idxBlock])) {
            $this->log(sprintf('[MATCHER] Next block [%u] in current rowset [%u] and current row [%u] not found', $idxNextBlock, $idxRowset, $idxRow));
            return false;
        }

        $block = $dataset[$idxRowset][$idxRow][$idxBlock];
        if (empty($block)) {
            $this->log(sprintf('[MATCHER] Next block is empty, marking as not matched.'));
            return false;
        }
        return $this->check($idxNextField, $block);
    }

    /**
     * @param int $idxField
     * @param string $block
     * @return bool
     */
    public function check(int $idxField, string $block)
    {
        $replacer = $this->getWorker('replace');
        $fields = $this->getOption('fields');

        if (!isset($fields[$idxField])) {
            $this->log(sprintf('[CHECK] Field with index [%u] not found. Aborting check...', $idxField));
            return false;
        }

        $field = $fields[$idxField];
        $patterns = $this->getOption('regex_match');
        $block = $replacer->applyRegexBefore($block, $field);
        if (!isset($patterns[$field])) {
            $this->log(sprintf('[CHECK] No match pattern for field [%s] in block: >>%s<<, marking as matched.', $field, $block));
            return true;
        }

        return $this->checkPatterns($patterns[$field], $block);
    }

    /**
     * @param $idxRowset
     * @param $idxRow
     * @param $idxField
     * @return bool
     */
    public function isNextRowMatch($idxRowset, $idxRow, $idxField)
    {
        $dataset = $this->getDataset();

        $idxNextField = $idxField + 1;
        $idxNextRow = $idxRow + 1;

        $this->log(sprintf('[MATCHER] Checking is next field [%u] in next row [%u] in set [%u] at first col...', $idxNextField, $idxNextRow, $idxRowset));

        if (!isset($dataset[$idxRowset][$idxNextRow]) || empty($dataset[$idxRowset][$idxNextRow])) {
            $this->log(sprintf('[MATCHER] Next row not found'));
            return false;
        }
        $k = array_key_first($dataset[$idxRowset][$idxNextRow]);
        $block = $dataset[$idxRowset][$idxNextRow][$k];
        if (empty($block)) {
            $this->log(sprintf('[MATCHER] First block in next record is empty, marking as not matched.'));
            return false;
        }
        return $this->check($idxNextField, $block);
    }

    /**
     * @param $idxRowset
     * @param $idxRow
     * @return bool
     */
    public function isNextRecordMatch($idxRowset, $idxRow)
    {
        $this->log('[MATCHER] Checking is next record match...');

        $dataset = $this->getDataset();

        $idxNextField = 0;
        $idxNextRow = $idxRow + 1;

        $this->log(sprintf('[MATCHER] Checking is first field in next record in row [%u] for field [%u] in set [%u]...', $idxNextRow, $idxNextField, $idxRowset));

        if (!isset($dataset[$idxRowset][$idxNextRow]) || empty($dataset[$idxRowset][$idxNextRow])) {
            $this->log(sprintf('[MATCHER] Next record row not found'));
            return false;
        }
        $k = array_key_first($dataset[$idxRowset][$idxNextRow]);
        $block = $dataset[$idxRowset][$idxNextRow][$k];
        if (empty($block)) {
            $this->log(sprintf('[MATCHER] First block in next record is empty, marking as not matched.'));
            return false;
        }
        return $this->check($idxNextField, $block);
    }

    /**
     * @param $idxRowset
     * @param $idxRow
     * @return bool
     */
    public function isNextRowsetMatch($idxRowset, $idxRow)
    {
        $dataset = $this->getDataset();

        $idxNextField = 0;
        $idxNextRow = 0;
        $idxNextRowset = $idxRowset + 1;

        $this->log(sprintf('[MATCHER] Checking is first field [0] in first record [0] in next rowset [%u]...', $idxNextRowset));

        if (!isset($dataset[$idxNextRowset]) || empty($dataset[$idxNextRowset])) {
            $this->log(sprintf('[MATCHER] Next rowset [%u] not found', $idxNextRowset));
            return false;
        }

        $k = array_key_first($dataset[$idxNextRowset]);
        if (!isset($dataset[$idxNextRowset][$k]) || empty($dataset[$idxNextRowset][$k])) {
            $this->log(sprintf('[MATCHER] Next row [%u] in next rowset [%u] not found', $k, $idxNextRowset));
            return false;
        }

        $j = array_key_first($dataset[$idxNextRowset][$k]);
        $block = $dataset[$idxNextRowset][$k][$j];
        if (empty($block)) {
            $this->log(sprintf('[MATCHER] First block [%u] in next rowset [%u] first record [%u] is empty, marking as not matched.', $j, $idxNextRowset, $k));
            return false;
        }
        return $this->check($idxNextField, $block);
    }

    /**
     * @param int $idxRowset
     * @param int $idxRow
     * @return bool
     */
    public function isLastRecord(int $idxRowset, int $idxRow)
    {
        $this->log(sprintf('[MATCHER] Checking if last row [%u] in rowset [%u]...', $idxRow, $idxRowset));

        $dataset = $this->getDataset();

        $last = array_key_last($dataset[$idxRowset]);
        if ($idxRow == $last) {
            return true;
        }
        return false;
    }
}