<?php

namespace Szczyglis\ChainParser\Plugin\Replacer;

use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\LoggableInterface;
use Szczyglis\ChainParser\Helper\AbstractPlugin;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class Replacer
 * @package Szczyglis\ChainParser\Plugin\Replacer
 */
class Replacer extends AbstractPlugin implements PluginInterface, LoggableInterface
{
    const NAME = 'replacer';

    private $output = [];
    private $result;
    private $input;

    /**
     * @return bool
     */
    public function run(): bool
    {
        $this->input = TextTools::prepareInput($this->getPrev('input'));

        $sepInput = TextTools::prepareSeparator($this->getOption('input_separator'));
        $sepOutput = TextTools::prepareSeparator($this->getOption('output_separator'));
        $regexBlocks = $this->getOption('regex_block');
        $regexAll = $this->getOption('regex_all');
        $interval = (int)$this->getOption('interval');
        $range = $this->getOption('range');

        $regexWorker = $this->getWorker('regex');
        $rangeWorker = $this->getWorker('range');

        $this->init();

        if (empty($interval)) {
            $interval = 1;
        }
        if (empty($sepInput)) {
            $sepInput = '\n';
            $this->log('Warning: no input separator specified, using default: \n');
        }
        if (empty($sepOutput)) {
            $sepOutput = '\n';
            $this->log('Warning: no output separator specified, using default: \n');
        }

        if (!empty($regexAll)) {
            $this->log(sprintf('Using patterns for all: %u pattern(s)', count($regexAll)));
            $regexWorker->replaceAll($this->input, $regexAll);
        }

        $this->output = TextTools::explode($sepInput, $this->input);
        foreach ($this->output as $i => $block) {
            $this->output[$i] = TextTools::trim($block);
        }

        if (!empty($regexBlocks)) {
            $ranges = [];
            if (!empty($range)) {
                $ranges = $rangeWorker->calc($this->output, $range);
                $this->log(sprintf('Limiting replacement to ranges: %s', json_encode($range, JSON_PRETTY_PRINT)));
            }

            $this->log(sprintf('Using patterns for blocks: %u pattern(s)', count($regexBlocks)));
            $regexWorker->replaceBlocks($this->output, $regexBlocks, $interval, $ranges);
        }

        $this->result = TextTools::implode($sepOutput, $this->output);

        $this->end();

        return true;
    }

    public function init()
    {
        $this->output = [];
        $this->result = $this->input;

        $this->log('Starting: replacer...');
        $this->log('Begin.');
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
            'regex' => new Worker\RegexWorker(),
            'range' => new Worker\RangeWorker(),
        ];
    }

    /**
     * @return array
     */
    public function registerOptions(): array
    {
        return [
            'multiline' => [
                'regex_all',
                'regex_block',
            ],
            'range' => [
                'range',
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