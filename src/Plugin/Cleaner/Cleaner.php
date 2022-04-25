<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Plugin\Cleaner;

use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\LoggableInterface;
use Szczyglis\ChainParser\Helper\AbstractPlugin;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class Cleaner
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
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
            $dataset = $html->applyStripTags($dataset);
        }

        if ((bool)$this->getOption('clean_blocks')) {
            $this->log('Running: clean empty blocks');
            $dataset = $blocks->removeEmpty($dataset);
        }

        if ((bool)$this->getOption('trim')) {
            $this->log('Running: trim()');
            $dataset = $trim->applyTrim($dataset);
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