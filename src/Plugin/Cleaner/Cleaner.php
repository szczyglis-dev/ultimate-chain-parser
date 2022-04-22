<?php

namespace Szczyglis\ChainParser\Plugin\Cleaner;

use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\LoggableInterface;
use Szczyglis\ChainParser\Helper\AbstractPlugin;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class Cleaner
 * @package Szczyglis\ChainParser\Plugin\Cleaner
 */
class Cleaner extends AbstractPlugin implements PluginInterface, LoggableInterface
{
    const NAME = 'cleaner';

    private $output = [];
    private $result;
    private $input;

    /**
     * @return bool
     */
    public function run(): bool
    {
        $this->input = TextTools::prepareInput($this->getPrev('input'));

        $sepInput = TextTools::prepareSeparator($this->getOption('input_separator'));
        $sepOutput = TextTools::prepareSeparator($this->getOption('output_separator'));

        $trim = $this->getWorker('trim');
        $blocks = $this->getWorker('blocks');
        $html = $this->getWorker('html');

        $this->init();

        if (empty($sepInput)) {
            $sepInput = '\n';
            $this->log('Warning: no input separator specified, using default: \n');
        }

        if ((bool)$this->getOption('fix_newlines')) {
            $this->log('Running: fix new lines');
            $this->input = $blocks->fixNewLines($this->input);
        }

        if ((bool)$this->getOption('strip_tags')) {
            $this->log('Running: strip_tags()');
            $this->input = $html->stripTags($this->input);
        }

        $this->output = TextTools::explode($sepInput, $this->input);
        if ((bool)$this->getOption('trim')) {
            $this->log('Running: trim()');
            $trim->trim($this->output);
        }
        if ((bool)$this->getOption('clean_blocks')) {
            $this->log('Running: clean empty blocks');
            $this->output = $blocks->cleanEmpty($this->output);
        }

        $this->result = TextTools::implode($sepOutput, $this->output);

        $this->end();

        return true;
    }

    public function init()
    {
        $this->output = [];
        $this->result = $this->input;

        $this->log('Starting: cleaner...');
        $this->log('Begin.');
    }

    public function end()
    {
        $this->log('Finish.');
        $this->set('output', $this->result);
        $this->set('data', $this->output);
    }

    /**
     * @return array
     */
    public function registerWorkers(): array
    {
        return [
            'trim' => new Worker\TrimerWorker(),
            'blocks' => new Worker\BlocksWorker(),
            'html' => new Worker\HtmlWorker(),
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