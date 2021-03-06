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
trait RegexTrait
{
    /**
     * @param array $patterns
     * @param string $string
     * @return bool
     */
    public function checkPatterns(array $patterns, string $string)
    {
        $res = false;
        foreach ($patterns as $pattern) {
            if (!$this->isPattern($pattern)) {
                $this->log(sprintf('Warning: Invalid pattern: %s. Aborting!', $pattern));
                continue;
            }

            if (preg_match($pattern, $string)) {
                $res = true;
                $this->log(sprintf('Matched block >>%s<< to pattern: %s', $string, $pattern));
                break;
            }
        }
        return $res;
    }

    /**
     * @param $pattern
     * @return bool
     */
    public function isPattern($pattern)
    {
        if (!is_string($pattern)) {
            return false;
        }

        if (preg_match('~^/[^\/]+/$~', trim($pattern))) {
            return true;
        }
        return false;
    }

    /**
     * @param array $patterns
     * @param string $string
     * @return string|string[]|null
     */
    public function applyPatterns(array $patterns, string $string)
    {
        foreach ($patterns as $pattern) {
            if (!isset($pattern['pattern']) || !isset($pattern['replacement'])) {
                $this->log(sprintf('Invalid pattern option format!'));
                continue;;
            }
            if (!$this->isPattern($pattern['pattern'])) {
                $this->log(sprintf('Warning: Invalid pattern: %s. Aborting!', $pattern['pattern']));
                continue;
            }
            $string = preg_replace($pattern['pattern'], $pattern['replacement'], $string);
            $this->log(sprintf('Executed pattern: %s => %s', $pattern['pattern'], $pattern['replacement']));
        }
        return $string;
    }
}