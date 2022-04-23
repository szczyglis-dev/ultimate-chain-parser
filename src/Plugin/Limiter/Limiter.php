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

    /**
     * @return bool
     */
    public function run(): bool
    {
        $dataset = $this->getDataset();
        $mode = $this->getOption('data_mode');

        $mode = $this->getOption('data_mode');
        $regexAllow = $this->getOption('regex_allow');
        $intervalAllow = (int)$this->getOption('interval_allow');
        $rangeAllow = $this->getOption('range_allow');

        $regexDeny = $this->getOption('regex_deny');
        $intervalDeny = (int)$this->getOption('interval_deny');
        $rangeDeny = $this->getOption('range_deny');

        $intervalWorker = $this->getWorker('interval');
        $regexWorker = $this->getWorker('regex');
        $rangeWorker = $this->getWorker('range');

        if (empty($intervalAllow)) {
            $intervalAllow = 1;
        }
        if (empty($intervalDeny)) {
            $intervalDeny = 1;
        }

        if (!empty($regexAllow)) {
            $this->log(sprintf('Using patterns allow limit: %u pattern(s)', count($regexAllow)));
            $dataset = $regexWorker->limitAllowed($dataset, $regexAllow, $mode);
        }
        if ($intervalAllow > 1) {
            $this->log(sprintf('Using interval allow limit: %u', $intervalAllow));
            $dataset = $intervalWorker->limitAllowed($dataset, $intervalAllow, $mode);
        }
        if (!empty($rangeAllow) || $rangeAllow == '0') {
            $this->log(sprintf('Using range allow limit: %s', json_encode($rangeAllow, JSON_PRETTY_PRINT)));
            $dataset = $rangeWorker->limitAllowed($dataset, $rangeAllow, $mode);
        }

        if (!empty($regexDeny)) {
            $this->log(sprintf('Using patterns deny limit: %u pattern(s)', count($regexDeny)));
            $dataset = $regexWorker->limitDenied($dataset, $regexDeny, $mode);
        }
        if ($intervalDeny > 1) {
            $this->log(sprintf('Using interval deny limit: %u', $intervalDeny));
            $dataset = $intervalWorker->limitDenied($dataset, $intervalDeny, $mode);
        }
        if (!empty($rangeDeny) || $rangeDeny == '0') {
            $this->log(sprintf('Using range deny limit: %s', json_encode($rangeDeny, JSON_PRETTY_PRINT)));
            $dataset = $rangeWorker->limitDenied($dataset, $rangeDeny, $mode);
        }

        $this->setDataset($dataset);

        return true;
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
                'regex_allow',
                'regex_deny',
            ],
            'range' => [
                'range_allow',
                'range_deny',
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