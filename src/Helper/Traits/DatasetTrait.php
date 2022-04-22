<?php

namespace Szczyglis\ChainParser\Helper\Traits;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Core\DataBag;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Trait ToolsTrait
 * @package Szczyglis\ChainParser\Helper\Traits
 */
trait DatasetTrait
{
    public function makeDataset(string $input, string $sRowset, string $sRow, string $sCol)
    {
        return TextTools::makeDataset($input, $sRowset, $sRow, $sCol);
    }

    public function packDataset(array $dataset, string $sRowset, string $sRow, string $sCol)
    {
        return TextTools::packDataset($dataset, $sRowset, $sRow, $sCol);
    }

    public function onDataset(array $dataset, callable $callback)
    {
        return TextTools::onDataset($dataset, $callback);
    }

    public function getSep(string $mode, string $type)
    {
        $k = 'sep_'.$mode.'_'.$type;
        return TextTools::prepareSeparator($this->getOption($k));
    }
    public function unpack(?string $input)
    {
        if (is_null($input)) {
            $input = '';
        }

        $sRowset = $this->getSep('input', 'rowset');
        $sRow = $this->getSep('input', 'row');
        $sCol = $this->getSep('input', 'col');

        return $this->makeDataset($input, $sRowset, $sRow, $sCol);
    }

    public function pack(array $dataset)
    {
        $sRowset = $this->getSep('output', 'rowset');
        $sRow = $this->getSep('output', 'row');
        $sCol = $this->getSep('output', 'col');

        return $this->packDataset($dataset, $sRowset, $sRow, $sCol);
    }
}