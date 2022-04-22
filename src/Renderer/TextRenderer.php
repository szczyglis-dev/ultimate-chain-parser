<?php

namespace Szczyglis\ChainParser\Renderer;

use Szczyglis\ChainParser\Contract\RendererInterface;
use Szczyglis\ChainParser\Contract\ConfigInterface;
use Szczyglis\ChainParser\Helper\AbstractRenderer;

/**
 * Class TextRenderer
 * @package Szczyglis\ChainParser\Renderer
 */
class TextRenderer extends AbstractRenderer implements RendererInterface
{
    /**
     * @return string
     */
    public function renderOutput()
    {
        $all = (bool)$this->config->get('full_output');

        if ($all) {

            $delimiter = "\n";
            if ($this->config->has('render_delimiter')) {
                $delimiter = $this->config->get('render_delimiter');
            }
            $res = [];
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
        $all = (bool)$this->config->get('full_output');

        if ($all) {

            $delimiter = "\n";
            if ($this->config->has('render_delimiter')) {
                $delimiter = $this->config->get('render_delimiter');
            }
            $res = [];
            foreach ($this->output as $item) {
                $res[] = json_encode($item->get('data'), JSON_PRETTY_PRINT);
            }
            return implode($delimiter, $res);

        } else {
            $k = array_key_last($this->output);
            if (!empty($this->output[$k])) {
                return json_encode($this->output[$k]->get('data'), JSON_PRETTY_PRINT);
            }
        }
    }

    /**
     * @return string
     */
    public function renderLog()
    {
        $all = (bool)$this->config->get('full_output');

        if ($all) {

            $delimiter = "\n";
            if ($this->config->has('render_delimiter')) {
                $delimiter = $this->config->get('render_delimiter');
            }
            $res = [];
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