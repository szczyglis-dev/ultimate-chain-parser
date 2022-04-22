<?php

namespace Szczyglis\ChainParser\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\HandlerInterface;
use Szczyglis\ChainParser\Contract\LoggerInterface;
use Szczyglis\ChainParser\Helper\AbstractLogger;

/**
 * Class PsrLogger
 * @package Szczyglis\ChainParser\Logger
 */
class PsrLogger extends AbstractLogger implements LoggerInterface
{
    const NAME = 'psr-logger';

    const LOG_FILE_PATH_DEFAULT = 'debug.log';
    const LOG_FILE_PATH_CONFIG_KEY = 'logfile';

    private static $instance = null;
    private $logger;
    private $handler;

    /**
     * PsrLogger constructor.
     * @param HandlerInterface|null $handler
     */
    public function __construct(?HandlerInterface $handler = null)
    {
        if (!is_null($handler)) {
            $this->handler = $handler;
        }
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        return self::$instance;
    }

    public function onBegin(): void
    {
        $path = self::LOG_FILE_PATH_DEFAULT;
        if (!is_null($this->data)) {
            $config = $this->data->get('config');
            if (!is_null($config)) {
                $tmp = $this->config->get(self::LOG_FILE_PATH_CONFIG_KEY);
                if (!empty($tmp)) {
                    $path = $tmp;
                }
            }
        }
        self::getLogger($this->handler, $path);
    }

    /**
     * @param HandlerInterface|null $handler
     * @param string|null $path
     * @return Logger
     */
    public static function getLogger(?HandlerInterface $handler = null, ?string $path = null): Logger
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;

        }
        return self::$instance->initLogger($handler, $path);
    }

    /**
     * @param HandlerInterface|null $handler
     * @param string|null $path
     * @return Logger
     */
    private function initLogger(?HandlerInterface $handler = null, ?string $path = null): Logger
    {
        if (!is_null($this->logger)) {
            return $this->logger;
        }

        $this->logger = new Logger(basename($path));
        if (!is_null($handler)) {
            $this->logger->pushHandler($handler);
        } elseif (!is_null($path)) {
            $this->logger->pushHandler(new StreamHandler($path, Logger::DEBUG));
        }
        return $this->logger;
    }

    public function onEnd(): void
    {
        $this->addMessage($this->prefix() . $this->appendMemoryUsage());
    }

    /**
     * @param string $message
     * @param array $additionalData
     */
    public function addMessage(string $message, array $additionalData = []): void
    {
        $msg = $this->prefix() . $message;
        $logger = self::getLogger();
        if (!is_null($logger)) {
            $logger->info($msg, $additionalData);
        }
    }

    /**
     * @return string
     */
    private function prefix(): string
    {
        return parent::appendPrefix();
    }

    public function onIterationBegin(): void
    {
        $this->addMessage(parent::STRING_INITIALIZING);

        if (!is_null($this->data)) {
            $element = $this->data->getElement();
            if (!is_null($element->getOptions())) {
                $options = $element->getOptions()->all();
                foreach ($options as $k => $v) {
                    $ary = [];
                    if (is_array($v)) {
                        $strValue = $k;
                        $ary = [
                            $k => $v,
                        ];
                    } else {
                        $strValue = !empty($v) ? $v : parent::STRING_EMPTY;
                    }
                    $this->addMessage(sprintf('%s %s: %s', parent::STRING_OPTION, $k, $strValue), $ary);
                }
            }
        }
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