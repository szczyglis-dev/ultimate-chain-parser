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
 * Class FilterWorker
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class FilterWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param string $block
     * @param string $field
     * @param string $mode
     * @return bool
     */
    public function isIgnored(string &$block, string &$field, string $mode)
    {
        $patterns = [];

        switch ($mode) {
            case 'before':
                $patterns = $this->getOption('regex_ignore_before');
                break;
            case 'after':
                $patterns = $this->getOption('regex_ignore_after');
                break;
        }

        if (empty($patterns) || !is_array($patterns)) {
            return false;
        }

        return $this->checkPatterns($patterns, $block);
    }
}