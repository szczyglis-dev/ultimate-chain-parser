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

    /**
     * @return bool
     */
    public function run(): bool
    {
        $mode = $this->getOption('data_mode');
        $regex = $this->getOption('regex');
        $interval = (int)$this->getOption('interval');
        $range = $this->getOption('range');

        if (empty($mode) || !in_array($mode, ['rowset', 'row', 'column'])) {
            $this->log('Warning: no data_mode specified, using default: column');
            $mode = 'column';
        }

        $dataset = $this->getDataset();
        $regexWorker = $this->getWorker('regex');

        if (empty($interval)) {
            $interval = 1;
        }
        if (!empty($regex)) {
            $this->log(sprintf('Using patterns: %u pattern(s)', count($regex)));
            $dataset = $regexWorker->replace($dataset, $regex, $interval, $range, $mode);
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
            'regex' => new Worker\RegexWorker(),
        ];
    }

    /**
     * @return array
     */
    public function registerOptions(): array
    {
        return [
            'multiline' => [
                'regex',
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