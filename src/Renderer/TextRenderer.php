<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Renderer;

use Szczyglis\ChainParser\Contract\RendererInterface;
use Szczyglis\ChainParser\Contract\ConfigInterface;
use Szczyglis\ChainParser\Helper\AbstractRenderer;

/**
 * Class TextRenderer
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class TextRenderer extends AbstractRenderer implements RendererInterface
{
    /**
     * @return string
     */
    public function renderOutput()
    {
        $showAll = (bool)$this->config->get('full_output');
        $res = [];
        if ($showAll) {
            $delimiter = "\n";
            if ($this->config->has('render_delimiter')) {
                $delimiter = $this->config->get('render_delimiter');
            }
            foreach ($this->output as $item) {
                $res[] = $item->get('output');
            }
            return implode($delimiter, $res);

        } else {
            $k = array_key_last($this->output);
            if (!empty($this->output[$k])) {
                return $this->output[$k]->get('output');
            }
        }
    }

    /**
     * @return false|string
     */
    public function renderData()
    {
        $showAll = (bool)$this->config->get('full_output');
        $res = [];

        if ($showAll) {
            $delimiter = "\n";
            if ($this->config->has('render_delimiter')) {
                $delimiter = $this->config->get('render_delimiter');
            }
            foreach ($this->output as $item) {
                $res[] = json_encode($item->get('dataset'), JSON_PRETTY_PRINT);
            }
            return implode($delimiter, $res);

        } else {
            $k = array_key_last($this->output);
            if (!empty($this->output[$k])) {
                return json_encode($this->output[$k]->get('dataset'), JSON_PRETTY_PRINT);
            }
        }
    }

    /**
     * @return string
     */
    public function renderLog()
    {
        $showAll = (bool)$this->config->get('full_output');
        $res = [];

        if ($showAll) {
            $delimiter = "\n";
            if ($this->config->has('render_delimiter')) {
                $delimiter = $this->config->get('render_delimiter');
            }
            foreach ($this->output as $item) {
                $loggers = $item->getLog();
                foreach ($loggers as $lines) {
                    foreach ($lines as $line) {
                        $res[] = $line;
                    }
                }
            }
            return implode($delimiter, $res);
        } else {
            $k = array_key_last($this->output);
            if (!empty($this->output[$k])) {
                $loggers = $this->output[$k]->getLog();
                foreach ($loggers as $lines) {
                    foreach ($lines as $line) {
                        $res[] = $line;
                    }
                }
                return implode("\n", $res);
            }
        }
    }
}