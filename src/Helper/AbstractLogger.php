<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Helper;

/**
 * Class AbstractLogger
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
abstract class AbstractLogger
{
    use Traits\DataTrait;

    const STRING_EMPTY = '<<EMPTY>>';
    const STRING_INITIALIZING = 'PLUGIN BEGIN';
    const STRING_ENDING = 'PLUGIN END';
    const STRING_BEGIN = '';
    const STRING_END = '';
    const STRING_OPTION = 'OPTION';
    const STRING_MEMORY_USED = 'MEMORY USED';

    /**
     * @return string
     */
    protected function appendTime()
    {
        return date('H:i:s') . ': ';
    }

    /**
     * @return string
     */
    protected function appendPrefix()
    {
        if (is_null($this->data)) {
            return '';
        }
        $element = $this->data->getElement();
        return '[' . $element->getIteration() . '] [' . $element->getName() . '] ';
    }

    /**
     * @return string
     */
    protected function appendMemoryUsage()
    {
        $mem = memory_get_usage(false);
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        return self::STRING_MEMORY_USED . ': ' . round($mem / pow(1024, ($i = floor(log($mem, 1024)))), 2) . ' ' . $units[$i];
    }

    /**
     * @return bool|void
     */
    protected function isDisabled()
    {
        $element = $this->data->getElement();
        if (!is_null($element)) {
            $config = $this->data->getElement()->getConfig();
        }
        if (!is_null($config)) {
            if ($config->get('no_log') == true) {
                return;
            }
        }
        return false;
    }
}