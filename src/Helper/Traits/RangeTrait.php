<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Helper\Traits;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Core\DataBag;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Trait ToolsTrait
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
trait RangeTrait
{
    /**
     * @param array $ranges
     * @param int $i
     * @return bool
     */
    public function inRange(array $ranges, int $i)
    {
        $res = false;

        foreach ($ranges as $k) {
            // single
            if (!is_array($k)) {
                if ($k == $i) {
                    $res = true;
                    $this->log(sprintf('Range matched: %s', $k));
                }
            } else {
                // range
                if (!is_null($k['from']) && !is_null($k['to'])) {
                    if ($k['from'] <= $k['to']) {
                        if ($i >= $k['from'] && $i <= $k['to']) {
                            $res = true;
                            $this->log(sprintf('Range matched [%u-%u] : %u', $k['from'], $k['to'], $i));
                        }
                    }
                } else if (!is_null($k['from'])) {
                    if ($i >= $k['from']) {
                        $res = true;
                        $this->log(sprintf('Range matched: [%u>] : %u', $k['from'], $i));
                    }
                } else if (!is_null($k['to'])) {
                    if ($i <= $k['to']) {
                        $res = true;
                        $this->log(sprintf('Range matched: [<%u] : %u', $k['to'], $i));
                    }
                }
            }
        }
        return $res;
    }
}