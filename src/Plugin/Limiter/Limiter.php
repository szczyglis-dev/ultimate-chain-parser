<?php

namespace Szczyglis\ChainParser\Plugin\Limiter;

use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\LoggableInterface;
use Szczyglis\ChainParser\Helper\AbstractPlugin;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class Limiter
 * @package Szczyglis\ChainParser\Plugin\Limiter
 */
class Limiter extends AbstractPlugin implements PluginInterface, LoggableInterface
{
    const NAME = 'limiter';

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
        $regexAllowed = $this->getOption('regex_allowed');
        $interval = (int)$this->getOption('interval');
        $range = $this->getOption('range');

        $intervalWorker = $this->getWorker('interval');
        $regexWorker = $this->getWorker('regex');
        $rangeWorker = $this->getWorker('range');

        $this->init();

        if (empty($interval)) {
            $interval = 1;
        }
        if (empty($sepInput)) {
            $sepInput = '\n';
            $this->log('Warning:  no input separator specified, using default: \n');
        }

        $this->output = TextTools::explode($sepInput, $this->input);
        foreach ($this->output as $i => $block) {
            $this->output[$i] = TextTools::trim($block);
        }

        if (!empty($regexAllowed)) {
            $this->log(sprintf('Using patterns limit: %u pattern(s)', count($regexAllowed)));
            $this->output = $regexWorker->limitToAllowed($this->output, $regexAllowed);
        } else if ($interval > 1) {
            $this->log(sprintf('Using interval limit: %u', $interval));
            $this->output = $intervalWorker->limitToAllowed($this->output, $interval);
        }

        if (!empty($range)) {
            $this->log(sprintf('Using limit range: %s', json_encode($range, JSON_PRETTY_PRINT)));
            $this->output = $rangeWorker->limitToAllowed($this->output, $range);
        }

        $this->result = TextTools::implode($sepOutput, $this->output);

        $this->end();

        return true;
    }

    public function init()
    {
        $this->output = [];
        $this->result = $this->input;

        $this->log('Starting: limiter..');
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
                'regex_allowed',
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