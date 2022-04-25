<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Plugin\Parser\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class BuildWorker
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class BuildWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $rowsets
     * @return array
     */
    public function postProcess(array &$rowsets): array
    {
        $outputFields = $this->getOption('output_fields');
        $placeholder = $this->getOption('empty_field_placeholder');
        if (!(bool)$this->getOption('is_empty_field_placeholder')) {
            $placeholder = '';
        }

        $result = [];
        foreach ($rowsets as $i => $rows) {
            foreach ($rows as $j => $row) {
                $cols = [];
                foreach ($row as $k => $col) {
                    $cols[$k] = '';
                    if (in_array($k, $outputFields)) {
                        $cols[$k] = $col;
                    }
                    if (empty($cols[$k]) && !empty($placeholder)) {
                        $cols[$k] = $placeholder;
                    }
                }
                $result[$i][$j] = $cols;
            }
        }

        return $result;
    }
}