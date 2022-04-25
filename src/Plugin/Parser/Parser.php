<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Plugin\Parser;

use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\LoggableInterface;
use Szczyglis\ChainParser\Helper\AbstractPlugin;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class Parser
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class Parser extends AbstractPlugin implements PluginInterface, LoggableInterface
{
    const NAME = 'parser';

    private $fields = [];
    private $output = [];
    private $numFields;
    private $idxRowset;
    private $idxRow;
    private $idxBlock;
    private $idxRecord;
    private $idxField;
    private $field;

    /**
     * @return bool
     */
    public function run(): bool
    {
        $matchWorker = $this->getWorker('match');
        $replaceWorker = $this->getWorker('replace');
        $buildWorker = $this->getWorker('build');
        $filterWorker = $this->getWorker('filter');

        $this->postInit();

        $dataset = $this->getDataset();

        foreach ($dataset as $i => $rows) {
            $this->log('IN ROWSET:' . $i);
            $this->idxRowset = $i;
            $this->idxRecord = 0;

            foreach ($rows as $j => $row) {
                $this->log('IN ROW:' . $j);
                $this->idxRow = $j;

                if (empty($row)) {
                    continue;
                }

                foreach ($row as $j => $block) {
                    if (empty(trim($block))) {
                        continue;
                    }
                    $this->idxBlock = $j;
                    if (!isset($this->fields[$this->idxField])) {
                        continue;
                        $this->log(sprintf('Warning: field [%u] is not defined!', $this->idxField));
                    }

                    $this->log('IN COL:' . $j);

                    $this->field = $this->fields[$this->idxField];

                    if ($filterWorker->isIgnored($block, $this->field, 'before')) {
                        continue;
                    }
                    $block = $replaceWorker->applyRegexBefore(trim($block), $this->field);
                    if ($filterWorker->isIgnored($block, $this->field, 'after')) {
                        continue;
                    }

                    if ($matchWorker->isMatch($this->field, $block)) {

                        $block = $replaceWorker->applyRegexAfter($block, $this->field);
                        $this->append($this->idxRowset, $this->idxRecord, $this->field, $block);

                        if ($matchWorker->isNextBlockMatch($this->idxRowset, $this->idxRow, $this->idxBlock, $this->idxField)) {
                            $this->log('[MAIN][-OK-] Is next block match # idxField++');
                            $this->idxField++;
                            $this->field = $this->fields[$this->idxField];
                        } else if (!$matchWorker->isLastRecord($this->idxRowset, $this->idxRow) && $matchWorker->isNextRowMatch($this->idxRowset, $this->idxRow, $this->idxField)) {
                            $this->log('[MAIN][-OK-] Is next row match # idxField++');
                            $this->idxField++;
                            $this->field = $this->fields[$this->idxField];
                        } else if (!$matchWorker->isLastRecord($this->idxRowset, $this->idxRow) && $matchWorker->isNextRecordMatch($this->idxRowset, $this->idxRow)) {
                            $this->log('[MAIN][-OK-] Is next record match # idxRecord++, idxField = 0');
                            $this->idxField = 0;
                            $this->idxRecord++;
                            $this->field = $this->fields[$this->idxField];
                        } else if ($matchWorker->isLastRecord($this->idxRowset, $this->idxRow) && $matchWorker->isNextRowsetMatch($this->idxRowset, $this->idxRow)) {
                            $this->log('[MAIN][-OK-] Is next rowset match # idxField = 0, idxRecord = 0, idxRowsetRecord++');
                            $this->idxField = 0;
                            $this->idxRecord = 0;
                            $this->field = $this->fields[$this->idxField];
                        } else {
                            $this->log('[MAIN] Nothing matched...');
                        }
                    }

                    if ($this->idxField >= $this->numFields) {
                        $this->log('Fields limit reached, switching to next record...');
                        $this->idxBlock++;
                        $this->idxRecord++;
                        $this->idxField = 0;
                    }
                }
            }
        }

        $this->output = $buildWorker->postProcess($this->output);

        $this->setDataset($this->output);

        return true;
    }

    public function postInit()
    {
        $this->idxRowset = 0;
        $this->idxRow = 0;
        $this->idxRecord = 0;
        $this->idxBlock = 0;
        $this->idxField = 0;
        $this->output = [];
        $this->fields = $this->getOption('fields');

        if (isset($this->fields[0])) {
            $this->field = $this->fields[0];
        } else {
            $this->field = null;
            $this->log('Warning: fields are not defined!');
        }

        $this->numFields = count($this->fields);
    }

    /**
     * @param string $log
     * @return mixed|void
     */
    public function log(string $log)
    {
        parent::log($this->buildDebugPrefix() . $log);
    }

    /**
     * @return string
     */
    public function buildDebugPrefix()
    {
        return sprintf('(set:%u | row:%u | blo:%u) [rec:%u] [fie:%u] [%s] ', $this->idxRowset, $this->idxRow, $this->idxBlock, $this->idxRecord, $this->idxField, $this->field);
    }

    /**
     * @param int $idxRowset
     * @param int $idxRecord
     * @param string $field
     * @param string $data
     */
    public function append(int $idxRowset, int $idxRecord, string &$field, string &$data)
    {
        if (!isset($this->output[$idxRowset][$idxRecord])) {
            $this->output[$idxRowset][$idxRecord] = [];
        }
        if (isset($this->output[$idxRowset][$idxRecord][$field])) {
            $this->output[$idxRowset][$idxRecord][$field] .= ' ' . $this->appendDebug() . $data;
            $this->log(sprintf('Appended block >>%s<< to field [%s]', $data, $field));
        } else {
            $this->output[$idxRowset][$idxRecord][$field] = $this->appendDebug() . $data;
            $this->log(sprintf('Added block >>%s<< to field [%s]', $data, $field));
        }
    }

    /**
     * @return string
     */
    public function appendDebug(): string
    {
        if (!(bool)$this->getOption('is_debug')) {
            return '';
        }
        return $this->buildDebugPrefix();
    }

    /**
     * @return array
     */
    public function registerWorkers(): array
    {
        return [
            'match' => new Worker\MatchWorker(),
            'replace' => new Worker\ReplaceWorker(),
            'build' => new Worker\BuildWorker(),
            'filter' => new Worker\FilterWorker(),
        ];
    }

    /**
     * @return array
     */
    public function registerOptions(): array
    {
        return [
            'multiline' => [
                'regex_match',
                'regex_ignore_before',
                'regex_ignore_after',
                'replace_block_before',
                'replace_block_after',
                'replace_field_before',
                'replace_field_after',
            ],
            'singleline' => [
                'fields',
                'output_fields',
            ],
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }
}