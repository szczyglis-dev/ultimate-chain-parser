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
    public function isNextRecordMatch(int $index)
    {
        $blocks = $this->getVar('blocks');
        $fields = $this->getOption('fields');
        $replacer = $this->getWorker('replace');

        $res = false;
        $j = $index + 1;

        if (isset($blocks[$j]) && isset($fields[0])) {
            if (empty($blocks[$j])) {
                $this->log(sprintf('Next block is empty, marking as not matched.'));
                return false;
            }
            $checkField = $fields[0];
            $patterns = $this->getOption('regex_match');
            $block = $replacer->applyRegexBefore(trim($blocks[$j]), $checkField);
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

    /**
     * @param int $index
     * @return bool
     */
    public function isNextRowsetMatch(int $index)
    {
        $rowsets = $this->getVar('rowsets');
        $fields = $this->getOption('fields');
        $replacer = $this->getWorker('replace');

        $res = false;
        $m = $index + 1;
        $sepInput = TextTools::prepareSeparator($this->getOption('input_block_separator'));

        if (!isset($rowsets[$m]) || empty($rowsets[$m])) {
            return false;
        }

        $blocks = TextTools::explode($sepInput, $rowsets[$m]);
        if (isset($blocks[0]) && isset($fields[0])) {
            if (empty($blocks[0])) {
                $this->log(sprintf('First block in next rowset is empty, marking as not matched.'));
                return false;
            }
            $checkField = $fields[0];
            $patterns = $this->getOption('regex_match');
            $block = $replacer->applyRegexBefore(trim($blocks[0]), $checkField);
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
                    $this->log(sprintf('Next rowset [%s] matched in next block: >>%s<< with regex: %s', $checkField, $block, $pattern));
                    $res = true;
                    break;
                } else {
                    $this->log(sprintf('Pattern not match at field[%s] >>%s<< with regex: %s', $checkField, $block, $pattern));
                }
            }
        } else {
            $this->log(sprintf('First block in next rowset not found.'));
        }
        return $res;
    }

    /**
     * @param int $fieldIndex
     * @param int $i
     * @return bool
     */
    public function isNextFieldMatch(int $fieldIndex, int $i)
    {
        $blocks = $this->getVar('blocks');
        $fields = $this->getOption('fields');
        $replacer = $this->getWorker('replace');

        $res = false;
        $m = $fieldIndex + 1;
        $j = $i + 1;

        if (isset($blocks[$j]) && isset($fields[$m])) {
            if (empty($blocks[$j])) {
                $this->log(sprintf('Next block is empty, marking as not matched.'));
                return false;
            }

            $checkField = $fields[$m];
            $patterns = $this->getOption('regex_match');
            $block = $replacer->applyRegexBefore(trim($blocks[$j]), $checkField);
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
                    $this->log(sprintf('Next field [%s] matched in next block: >>%s<< with regex: %s', $checkField, $block, $pattern));
                    $res = true;
                    break;
                } else {
                    $this->log(sprintf('Pattern not match at field[%s] >>%s<< with regex: %s', $checkField, $block, $pattern));
                }
            }
        } else {
            $this->log(sprintf('Next block not found.'));
        }
        return $res;
    }
}