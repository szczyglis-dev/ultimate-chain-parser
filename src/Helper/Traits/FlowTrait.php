<?php

namespace Szczyglis\ChainParser\Helper\Traits;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Core\DataBag;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Trait ToolsTrait
 * @package Szczyglis\ChainParser\Helper\Traits
 */
trait FlowTrait
{
    public function init()
    {
        $this->log('Starting: ' . $this->getName() . '...');
        $this->log('Begin.');

        $this->set('input', (string)$this->getPrev('output'));
        $this->set('output', $this->get('input'));

        $useDataset = (bool)$this->getOption('use_dataset');

        if ($useDataset) {
            $this->log('Using previous output dataset as current dataset...');
            $this->set('dataset', $this->getPrev('dataset'));
        } else {
            $this->log('Using previous parsed output as input...');
            $this->set('dataset', $this->unpack($this->get('input')));
        }
    }

    public function end()
    {
        $this->set('output', $this->pack($this->get('dataset')));
        $this->log('Finish.');
    }
}