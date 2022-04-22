<?php

namespace Szczyglis\ChainParser\Plugin\Parser;

use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\LoggableInterface;
use Szczyglis\ChainParser\Helper\AbstractPlugin;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class Parser
 * @package Szczyglis\ChainParser\Plugin\Parser
 */
class Parser extends AbstractPlugin implements PluginInterface, LoggableInterface
{
    const NAME = 'parser';

    private $result;
    private $input;
    private $output = [];
    private $blocks = [];
    private $fields = [];
    private $outputFields = [];
    private $rowsets = [];
    private $numFields;
    private $idxRowset;
    private $idxRowsetRecord;
    private $idxBlock;
    private $idxRecord;
    private $idxField;
    private $field;

    /**
     * @return bool
     */
    public function run(): bool
    {
        $this->input = TextTools::prepareInput($this->getPrev('input'));

        $sepInput = TextTools::prepareSeparator($this->getOption('input_block_separator'));
        $sepRowset = TextTools::prepareSeparator($this->getOption('rowset_separator'));
        $isRowset = false;

        $matchWorker = $this->getWorker('match');
        $replaceWorker = $this->getWorker('replace');
        $buildWorker = $this->getWorker('build');
        $filterWorker = $this->getWorker('filter');

        $this->init();

        if (!empty($sepRowset)) {
            $isRowset = true;
            $this->rowsets = TextTools::explode($sepRowset, $this->input);
        } else {
            $this->rowsets = [0 => $this->input];
        }

        $this->setVar('rowsets', $this->rowsets);
        $this->setVar('output_fields', $this->outputFields);

        foreach ($this->rowsets as $i => $rowset) {

            $this->idxRowset = $i;
            $this->idxRecord = 0;
            $this->blocks = TextTools::explode($sepInput, $rowset);
            $this->setVar('blocks', $this->blocks);

            if (empty($this->blocks)) {
                continue;
            }

            foreach ($this->blocks as $j => $block) {
                if (empty(trim($block))) {
                    continue;
                }
                $this->idxBlock = $j;
                if (!isset($this->fields[$this->idxField])) {
                    continue;
                    $this->log(sprintf('Warning: field [%u] is not defined!', $this->idxField));
                }

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
                    $this->append($this->idxRowsetRecord, $this->idxRecord, $this->field, $block);
                    if ($matchWorker->isNextFieldMatch($this->idxField, $this->idxBlock)) {
                        $this->idxField++;
                    } else {
                        $isNext = false;
                        if ($isRowset) {
                            $isNext = $matchWorker->isNextRowsetMatch($this->idxRowset);
                        } else {
                            $isNext = $matchWorker->isNextRecordMatch($this->idxBlock);
                        }
                        if ($isNext) {
                            $this->log('Next block matched, switching to next record...');
                            $this->idxField = 0;
                            $this->idxBlock++;
                            $this->idxRecord++;
                            continue;
                        }
                    }
                }

                if ($this->idxField >= $this->numFields) {
                    $this->log('Fields limit reached, switching to next record...');
                    $this->idxBlock++;
                    $this->idxRecord++;
                    $this->idxField = 0;
                }
            }
            $this->idxRowsetRecord++;
        }

        $this->result = $buildWorker->build($this->output);

        $this->end();

        return true;
    }

    public function init()
    {
        $this->idxRowset = 0;
        $this->idxRowsetRecord = 0;
        $this->idxRecord = 0;
        $this->idxBlock = 0;
        $this->idxField = 0;

        $this->output = [];
        $this->rowsets = [];
        $this->blocks = [];

        $this->fields = $this->getOption('fields');
        if (isset($this->fields[0])) {
            $this->field = $this->fields[0];
        } else {
            $this->field = null;
            $this->log('Warning: fields are not defined!');
        }

        $this->numFields = count($this->fields);
        $this->outputFields = $this->getOption('output_fields');

        $this->result = $this->input;

        $this->log('Starting: parser..');
        $this->log('Begin.');
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
        return sprintf('(s:%u | b:%u) [r:%u] [f:%u] [%s] ', $this->idxRowset, $this->idxBlock, $this->idxRecord, $this->idxField, $this->field);
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

    public function end()
    {
        $this->log('Finish.');
        $this->set('output', $this->result);
        $this->set('data', $this->output);
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