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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Szczyglis\ChainParser\Contract\RendererInterface;
use Szczyglis\ChainParser\Contract\ConfigInterface;
use Szczyglis\ChainParser\Helper\AbstractRenderer;

/**
 * Class ConsoleRenderer
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class ConsoleRenderer extends AbstractRenderer implements RendererInterface
{
    private $input;
    private $console;

    /**
     * ConsoleRenderer constructor.
     * @param InputInterface $input
     * @param OutputInterface $console
     */
    public function __construct(InputInterface $input, OutputInterface $console)
    {
        $this->input = $input;
        $this->console = $console;
    }

    public function renderOutput()
    {
        $showAll = (bool)$this->config->get('full_output');

        if ($showAll) {
            foreach ($this->output as $item) {
                $this->console->writeln($item->get('output'));
            }
        } else {
            $k = array_key_last($this->output);
            if (!empty($this->output[$k])) {
                $this->console->writeln($this->output[$k]->get('output'));
            }
        }
    }

    public function renderData()
    {
        $showAll = (bool)$this->config->get('full_output');

        if ($showAll) {
            foreach ($this->output as $item) {
                $this->console->writeln(json_encode($item->get('data'), JSON_PRETTY_PRINT));
            }
        } else {
            $k = array_key_last($this->output);
            if (!empty($this->output[$k])) {
                $this->console->writeln(json_encode($this->output[$k]->get('data'), JSON_PRETTY_PRINT));
            }
        }
    }

    public function renderLog()
    {
        $showAll = (bool)$this->config->get('full_output');

        if ($showAll) {
            foreach ($this->output as $item) {
                $loggers = $item->getLog();
                foreach ($loggers as $lines) {
                    foreach ($lines as $line) {
                        $this->console->writeln($line);
                    }
                }
            }
        } else {
            $k = array_key_last($this->output);
            if (!empty($this->output[$k])) {
                $loggers = $this->output[$k]->getLog();
                foreach ($loggers as $lines) {
                    foreach ($lines as $line) {
                        $this->console->writeln($line);
                    }
                }
            }
        }
    }
}