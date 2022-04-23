<?php

namespace Szczyglis\ChainParser\Logger;

use Szczyglis\ChainParser\Contract\LoggerInterface;
use Szczyglis\ChainParser\Helper\AbstractLogger;

/**
 * Class ArrayLogger
 * @package Szczyglis\ChainParser\Logger
 */
class ArrayLogger extends AbstractLogger implements LoggerInterface
{
    const NAME = 'array-logger';

    private $logs = [];

    public function onBegin(): void
    {
        //
    }

    public function onEnd(): void
    {
        $this->addMessage($this->prefix() . $this->appendMemoryUsage());
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

        $this->logs[] = $this->prefix() . $message;
    }

    /**
     * @return string
     */
    private function prefix(): string
    {
        return parent::appendTime() . parent::appendPrefix();
    }

    public function onIterationBegin(): void
    {
        $this->clear();
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

    public function clear(): void
    {
        $this->logs = [];
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
        return $this->logs;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }
}