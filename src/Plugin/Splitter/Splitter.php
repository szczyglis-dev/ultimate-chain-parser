<?php

namespace Szczyglis\ChainParser\Plugin\Splitter;

use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\LoggableInterface;
use Szczyglis\ChainParser\Helper\AbstractPlugin;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class Splitter
 * @package Szczyglis\ChainParser\Plugin\Splitter
 */
class Splitter extends AbstractPlugin implements PluginInterface, LoggableInterface
{
    const NAME = 'splitter';

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
        $regexSplit = $this->getOption('regex_split');
        $interval = (int)$this->getOption('interval_split');
        $range = $this->getOption('range_output');

        $intervalWorker = $this->getWorker('interval');
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

        $this->output = TextTools::explode($sepInput, $this->input);
        foreach ($this->output as $i => $line) {
            $this->output[$i] = TextTools::trim($line);
        }

        $this->result = $this->input;

        if (!empty($regexSplit) && TextTools::isPattern($regexSplit)) {
            $this->log(sprintf('Using pattern split: %s', $regexSplit));
            $this->output = $regexWorker->split($this->input, $regexSplit);
        } else {
            $this->log(sprintf('Using interval split: %u', $interval));
            $this->output = $intervalWorker->split($this->output, $interval, $sepOutput);
        }

        if (!empty($range)) {
            $this->log(sprintf('Using export range: %s', json_encode($range, JSON_PRETTY_PRINT)));
            $this->output = $rangeWorker->split($this->output, $range);
        }

        $this->result = TextTools::implode($sepOutput, $this->output);

        $this->end();

        return true;
    }

    public function init()
    {
        $this->output = [];
        $this->result = $this->input;

        $this->log('Starting: eraser...');
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
            'interval' => new Worker\IntervalWorker(),
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
            'singleline' => [
                'regex_split',
            ],
            'range' => [
                'range_output',
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