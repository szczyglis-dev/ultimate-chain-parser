<?php

namespace Szczyglis\ChainParser\Logger;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Szczyglis\ChainParser\Contract\LoggerInterface;
use Szczyglis\ChainParser\Contract\ConfigInterface;
use Szczyglis\ChainParser\Helper\AbstractLogger;

/**
 * Class ConsoleLogger
 * @package Szczyglis\ChainParser\Logger
 */
class ConsoleLogger extends AbstractLogger implements LoggerInterface
{
    const NAME = 'console-logger';

    private $logs = [];
    private $input;
    private $output;

    /**
     * ConsoleLogger constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function onBegin(): void
    {
        $this->output->writeln($this->prefix() . 'BEGIN');
    }

    /**
     * @return string
     */
    private function prefix(): string
    {
        return parent::appendTime() . parent::appendPrefix();
    }

    public function onEnd(): void
    {
        $this->output->writeln($this->prefix() . 'END');
        $this->output->writeln($this->prefix() . $this->appendMemoryUsage());
    }

    public function onIterationBegin(): void
    {
        $this->addMessage(parent::STRING_INITIALIZING);

        if (!is_null($this->data)) {
            $element = $this->data->getElement();
            if (!is_null($element->getOptions())) {
                $options = $element->getOptions()->all();
                foreach ($options as $k => $v) {
                    if (is_array($v)) {
                        $value = json_encode($v, JSON_PRETTY_PRINT);
                    } else {
                        $value = !empty($v) ? $v : parent::STRING_EMPTY;
                    }
                    $this->addMessage(sprintf('%s %s: %s', parent::STRING_OPTION, $k, $value));
                }
            }
        }
    }

    /**
     * @param string $message
     * @param array $additionalData
     */
    public function addMessage(string $message, array $additionalData = [])
    {
        if ($this->isDisabled()) {
            return;
        }

        $msg = $this->prefix() . $message;
        $this->output->writeln($msg);
    }

    public function onIterationEnd(): void
    {
        $this->addMessage(parent::STRING_ENDING);
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }
}