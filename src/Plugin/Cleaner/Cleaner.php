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

    /**
     * @return bool
     */
    public function run(): bool
    { 
        $trim = $this->getWorker('trim');
        $blocks = $this->getWorker('blocks');
        $html = $this->getWorker('html');

        $dataset = $this->get('dataset');

        if ((bool)$this->getOption('fix_newlines')) {
            $this->log('Running: fix new lines');
            $dataset = $blocks->fixLines($dataset);
        }

        if ((bool)$this->getOption('strip_tags')) {
            $this->log('Running: strip_tags()');
            $dataset = $html->stripTags($dataset);
        }

        if ((bool)$this->getOption('clean_blocks')) {
            $this->log('Running: clean empty blocks');
            $dataset = $blocks->removeEmpty($dataset);
        }
        
        if ((bool)$this->getOption('trim')) {
            $this->log('Running: trim()');
            $dataset = $trim->trim($dataset);
        }      

        $this->set('dataset', $dataset);      

        return true;
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