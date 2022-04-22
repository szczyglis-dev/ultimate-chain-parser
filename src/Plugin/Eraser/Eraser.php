<?php

namespace Szczyglis\ChainParser\Plugin\Eraser;

use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\LoggableInterface;
use Szczyglis\ChainParser\Helper\AbstractPlugin;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class Eraser
 * @package Szczyglis\ChainParser\Plugin\Eraser
 */
class Eraser extends AbstractPlugin implements PluginInterface, LoggableInterface
{
    const NAME = 'eraser';

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
        $regexErase = $this->getOption('regex_erase');
        $interval = (int)$this->getOption('interval_erase');
        $range = $this->getOption('range');

        $intervalWorker = $this->getWorker('interval');
        $regexWorker = $this->getWorker('regex');
        $rangeWorker = $this->getWorker('range');

        $this->init();

        if (empty($sepInput)) {
            $sepInput = '\n';
            $this->log('Warning: no input separator specified, using default: \n');
        }

        if (!empty($regexErase)) {
            $this->log(sprintf('Using pattern erase, num of patterns: %u', count($regexErase)));
            $regexWorker->erase($this->input, $regexErase);
        }

        $this->output = TextTools::explode($sepInput, $this->input);
        foreach ($this->output as $i => $line) {
            $this->output[$i] = TextTools::trim($line);
        }
        if (!empty($interval)) {
            $this->log(sprintf('Using interval erase: %u', $interval));
            $this->output = $intervalWorker->erase($this->output, $interval);
        }

        if (!empty($range)) {
            $this->log(sprintf('Using range erase: %s', json_encode($range, JSON_PRETTY_PRINT)));
            $this->output = $rangeWorker->erase($this->output, $range);
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
            'multiline' => [
                'regex_erase',
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