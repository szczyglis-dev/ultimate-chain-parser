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
            $this->log(sprintf('No match pattern for field [%s] in block: >>%s<<, marking as matched.', $field, $block));
            return true;
        }

        foreach ($patterns[$field] as $pattern) {
            if (!TextTools::isPattern($pattern)) {
                $this->log(sprintf('Warning: Invalid pattern: %s. Aborting!', $pattern));
                continue;
            }
            if (preg_match($pattern, $block)) {
                $this->log(sprintf('Field [%s] matched in block: >>%s<< with regex: %s', $field, $block, $pattern));
                $res = true;
                break;
            } else {
                $this->log(sprintf('Field [%s] not matched in block: >>%s<< with regex: %s', $field, $block, $pattern));
            }
        }

        return $res;
    }

    /**
     * @param int $index
     * @return bool
     */
    /*
    public function isNextRecordMatch(int $idxRowset, int $idxRow)
    {
        $dataset = $this->get('dataset');
        $blocks = $this->getVar('blocks');
        $fields = $this->getOption('fields');
        $replacer = $this->getWorker('replace');

        $res = false;
        $idxNextRow = $idxRow + 1;

        if (isset($dataset[$idxRowset][$idxNextRow]) && isset($fields[0])) {
            $this->log('jest next row');
            if (empty($dataset[$idxRowset][$idxNextRow])) {
                $this->log(sprintf('Next block is empty, marking as not matched.'));
                return false;
            }
            $checkField = $fields[0];
            $patterns = $this->getOption('regex_match');
            $k = array_key_first($dataset[$idxRowset][$idxNextRow]);
            $this->log('next key:'.$k);
            $block = $dataset[$idxRowset][$idxNextRow][$k];
            $this->log('next bbbbbb:'.$block);
            $block = $replacer->applyRegexBefore(trim($block), $checkField);
            if (!isset($patterns[$checkField])) {
                $this->log(sprintf('No match pattern for field [%s] in block: >>%s<<, marking as matched.', $checkField, $block));
                return true;
            }
            $res = false;
            foreach ($patterns[$checkField] as $pattern) {
                if (!TextTools::isPattern($pattern)) {
                    $this->log(sprintf('Warning: Invalid pattern: %s. Aborting!', $pattern));
                    continue;
                }
                if (preg_match($pattern, $block)) {
                    $this->log(sprintf('Next record [%s] matched in next block: >>%s<< with regex: %s', $checkField, $block, $pattern));
                    $res = true;
                    break;
                } else {
                    $this->log(sprintf('Pattern not match at field[%s] >>%s<< with regex: %s', $checkField, $block, $pattern));
                }
            }
        } else {
            $this->log(sprintf('First field in next block not found.'));
        }
        return $res;
    }
    */

    public function check(int $idxField, string $block)
    {
        $replacer = $this->getWorker('replace');
        $fields = $this->getOption('fields');

        if (!isset($fields[$idxField])) {
            $this->log(sprintf('Field with index [%u] not found.', $idxField));
            return false;
        }

        $field = $fields[$idxField];
        $patterns = $this->getOption('regex_match');
        $block = $replacer->applyRegexBefore($block, $field);
        if (!isset($patterns[$field])) {
            $this->log(sprintf('No match pattern for field [%s] in block: >>%s<<, marking as matched.', $field, $block));
            return true;
        }

        $res = false;
        foreach ($patterns[$field] as $pattern) {
            if (!TextTools::isPattern($pattern)) {
                $this->log(sprintf('Warning: Invalid pattern: %s. Aborting!', $pattern));
                continue;
            }
            if (preg_match($pattern, $block)) {
                $this->log(sprintf('Field [%s] matched in block: >>%s<< with regex: %s', $field, $block, $pattern));
                $res = true;
                break;
            } else {
                $this->log(sprintf('Pattern not match at field[%s] >>%s<< with regex: %s', $field, $block, $pattern));
            }
        }
        return $res;
    }


    /**
     * @param int $fieldIndex
     * @param int $i
     * @return bool
     */
    public function isNextBlockMatch($idxRowset, $idxRow, $idxBlock, $idxField)
    {
        $this->log('[MATCHER] Checking is next block...');

        $dataset = $this->get('dataset');
        $blocks = $this->getVar('blocks');
        $fields = $this->getOption('fields');        

        $idxNextField = $idxField + 1;
        $idxNextBlock = $idxBlock + 1;

        if (!isset($dataset[$idxRowset][$idxRow][$idxBlock])) {
            $this->log(sprintf('Next block [%u] in current rowset [%u] and current row [%u] not found', $idxNextBlock, $idxRowset, $idxRow));
            return false;
        }

        $block = $dataset[$idxRowset][$idxRow][$idxBlock];
        if (empty($block)) {
            $this->log(sprintf('Next block is empty, marking as not matched.'));
            return false;
        }
        return $this->check($idxNextField, $block);
    }

    /**
     * @param int $fieldIndex
     * @param int $i
     * @return bool
     */
    public function isNextRowMatch($idxRowset, $idxRow, $idxField)
    {
        $this->log('[MATCHER] Checking is next row match...');

        $dataset = $this->get('dataset');
        $blocks = $this->getVar('blocks');
        $fields = $this->getOption('fields');

        $idxNextField = $idxField + 1;
        $idxNextRow = $idxRow + 1;

        if (!isset($dataset[$idxRowset][$idxNextRow]) || empty($dataset[$idxRowset][$idxNextRow])) {
            $this->log(sprintf('Next row not found'));
            return false;
        }
        $k = array_key_first($dataset[$idxRowset][$idxNextRow]);
        $block = $dataset[$idxRowset][$idxNextRow][$k];
        if (empty($block)) {
            $this->log(sprintf('First block in next record is empty, marking as not matched.'));
            return false;
        }
        return $this->check($idxNextField, $block);
    }

    /**
     * @param int $fieldIndex
     * @param int $i
     * @return bool
     */
    public function isNextRecordMatch($idxRowset, $idxRow)
    {
        $this->log('[MATCHER] Checking is next record match...');

        $dataset = $this->get('dataset');
        $blocks = $this->getVar('blocks');
        $fields = $this->getOption('fields');

        $idxNextField = 0; // first field
        $idxNextRow = $idxRow + 1;

        $this->log(sprintf('xxxxxxxx check first field [%u] in next record [%u]', $idxNextField, $idxNextRow));

        if (!isset($dataset[$idxRowset][$idxNextRow]) || empty($dataset[$idxRowset][$idxNextRow])) {
            $this->log(sprintf('Next record row not found'));
            return false;
        }
        $k = array_key_first($dataset[$idxRowset][$idxNextRow]);
        $block = $dataset[$idxRowset][$idxNextRow][$k];
        if (empty($block)) {
            $this->log(sprintf('First block in next record is empty, marking as not matched.'));
            return false;
        }
        return $this->check($idxNextField, $block);
    }

    /**
     * @param int $fieldIndex
     * @param int $i
     * @return bool
     */
    public function isNextRowsetMatch($idxRowset, $idxRow)
    {
        $this->log('[MATCHER] Checking is next rowset match...');

        $dataset = $this->get('dataset');
        $blocks = $this->getVar('blocks');

        $idxNextField = 0; // first field
        $idxNextRow = 0;
        $idxNextRowset = $idxRowset + 1;

        $this->log(sprintf('xxxxxxxx check first field [%u] in next rowset [%u]', $idxNextField, $idxNextRowset));
        if (!isset($dataset[$idxNextRowset]) || empty($dataset[$idxNextRowset])) {
            $this->log(sprintf('Next rowset [%u] not found', $idxNextRowset));
            return false;
        }

        $k = array_key_first($dataset[$idxNextRowset]);  
        if (!isset($dataset[$idxNextRowset][$k]) || empty($dataset[$idxNextRowset][$k])) {
            $this->log(sprintf('Next row [%u] in next rowset [%u] not found', $k, $idxNextRowset));
            return false;
        } 

        $j = array_key_first($dataset[$idxNextRowset][$k]);
        $block = $dataset[$idxNextRowset][$k][$j];
        if (empty($block)) {
            $this->log(sprintf('First block [%u] in next rowset [%u] first record [%u] is empty, marking as not matched.', $j, $idxNextRowset, $k));
            return false;
        }
        return $this->check($idxNextField, $block);
    }

    /**
     * @param int $fieldIndex
     * @param int $i
     * @return bool
     */
    public function isLastRecord(int $idxRowset, int $idxRow)
    {
        $this->log('[MATCHER] Checking is next rowset match...');

        $dataset = $this->get('dataset');
        $blocks = $this->getVar('blocks');

        $last = array_key_last($dataset[$idxRowset]);
        if ($idxRow == $last) {
            return true;
        }
        return false;
    }
}