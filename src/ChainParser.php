<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser;

use Szczyglis\ChainParser\Contract\InputInterface;
use Szczyglis\ChainParser\Contract\ConfigInterface;
use Szczyglis\ChainParser\Contract\OptionsInterface;
use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\LoggerInterface;
use Szczyglis\ChainParser\Contract\RendererInterface;
use Szczyglis\ChainParser\Contract\OptionResolverInterface;
use Szczyglis\ChainParser\Core\Chain;
use Szczyglis\ChainParser\Core\ChainElement;
use Szczyglis\ChainParser\Config\ArrayConfig;
use Szczyglis\ChainParser\Options\ArrayOptions;

/**
 * Class ChainParser
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class ChainParser
{
    private $plugins = [];
    private $loggers = [];
    private $resolvers = [];
    private $renderer;
    private $chain = [];
    private $input;
    private $output;
    private $config;
    private $initials = [];
    private $isInitialized = false;

    /**
     * ChainParser constructor.
     * @param bool $init
     */
    public function __construct(bool $init = false)
    {
        $this->initials['plugins'] = [
            Plugin\Cleaner\Cleaner::class,
            Plugin\Limiter\Limiter::class,
            Plugin\Replacer\Replacer::class,
            Plugin\Parser\Parser::class,
        ];

        $this->initials['loggers'] = [
            Logger\ArrayLogger::class,
            //Logger\PsrLogger::class,
            //Logger\ConsoleLogger::class,
        ];

        $this->initials['resolvers'] = [
            OptionResolver\SingleLineResolver::class,
            OptionResolver\MultiLineResolver::class,
            OptionResolver\RangeResolver::class,
        ];

        $this->initials['renderer'] = Renderer\TextRenderer::class;
        //$this->initials['renderer'] = Renderer\ConsoleRenderer::class;

        if ($init) {
            $this->init();
        }
    }

    /**
     * @return $this
     */
    public function init(): self
    {
        $this->initPlugins();
        $this->initLoggers();
        $this->initResolvers();
        $this->initRenderer();

        $this->isInitialized = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function initPlugins(): self
    {
        foreach ($this->initials['plugins'] as $classname) {
            $instance = new $classname;
            if (!$instance instanceof PluginInterface) {
                throw new \RuntimeException('Plugin must implement PluginInterface.');
            }
            $this->addPlugin($instance);
        }

        return $this;
    }

    /**
     * @param PluginInterface $plugin
     * @return $this
     */
    public function addPlugin(PluginInterface $plugin): self
    {
        $name = $plugin->getName();
        if (empty($name)) {
            throw new \InvalidArgumentException('Empty Plugin identifier - please specify name via getName() method.');
        }
        $this->plugins[$name] = $plugin;

        return $this;
    }

    /**
     * @return $this
     */
    public function initLoggers(): self
    {
        foreach ($this->initials['loggers'] as $classname) {
            $instance = new $classname;
            if (!$instance instanceof LoggerInterface) {
                throw new \RuntimeException('Logger must implement LoggerInterface.');
            }
            $this->addLogger($instance);
        }

        return $this;
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function addLogger(LoggerInterface $logger): self
    {
        $name = $logger->getName();
        if (empty($name)) {
            throw new \InvalidArgumentException('Empty Logger identifier - please specify name via getName() method.');
        }
        $this->loggers[$name] = $logger;

        return $this;
    }

    /**
     * @return $this
     */
    public function initResolvers(): self
    {
        foreach ($this->initials['resolvers'] as $classname) {
            $instance = new $classname;
            if (!$instance instanceof OptionResolverInterface) {
                throw new \RuntimeException('Option Resolver must implement OptionResolverInterface.');
            }
            $this->addResolver($instance);
        }

        return $this;
    }

    /**
     * @param OptionResolverInterface $resolver
     * @return $this
     */
    public function addResolver(OptionResolverInterface $resolver): self
    {
        $name = $resolver->getName();
        if (empty($name)) {
            throw new \InvalidArgumentException('Empty Option Resolver identifier - please specify name via getName() method.');
        }
        $this->resolvers[$name] = $resolver;

        return $this;
    }

    /**
     * @return $this
     */
    public function initRenderer(): self
    {
        if (is_null($this->initials['renderer'])) {
            return $this;
        }

        $instance = new $this->initials['renderer'];
        if (!$instance instanceof RendererInterface) {
            throw new \RuntimeException('Renderer must implement RendererInterface.');
        }

        $this->setRenderer($instance);

        return $this;
    }

    /**
     * @param RendererInterface $renderer
     * @return $this
     */
    public function setRenderer(RendererInterface $renderer): self
    {
        $this->renderer = $renderer;

        return $this;
    }

    /**
     * @return $this
     */
    public function run(): self
    {
        if (!$this->isInitialized) {
            $this->init();
        }

        if (is_null($this->config)) {
            $this->config = new ArrayConfig([]);
        }

        $this->appendResolvers();

        $chain = new Chain();
        $chain
            ->setConfig($this->config)
            ->setChain($this->chain)
            ->setPlugins($this->plugins)
            ->setLoggers($this->loggers)
            ->setInput($this->input)
            ->run();

        $this->output = $chain->getOutput();

        if (!is_null($this->renderer)) {
            $this->renderer->setConfig($this->config);
            $this->renderer->setOutput($this->output);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function appendResolvers(): self
    {
        foreach ($this->chain as $element) {
            if (!is_null($element->getOptions())) {
                $element->getOptions()->setResolvers($this->resolvers);
            }
        }

        return $this;
    }

    /**
     * @param array|null $options
     * @return mixed
     */
    public function renderOutput(?array $options = [])
    {
        if (!is_null($this->renderer)) {
            return $this->renderer->renderOutput($options);
        }
    }

    /**
     * @param array|null $options
     * @return mixed
     */
    public function renderData(?array $options = [])
    {
        if (!is_null($this->renderer)) {
            return $this->renderer->renderData($options);
        }
    }

    /**
     * @param array|null $options
     * @return mixed
     */
    public function renderLog(?array $options = [])
    {
        if (!is_null($this->renderer)) {
            return $this->renderer->renderLog($options);
        }
    }

    /**
     * @param InputInterface $input
     * @return $this
     */
    public function setInput(InputInterface $input): self
    {
        $this->input = $input;

        return $this;
    }

    /**
     * @return array
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
     * @return array
     */
    public function getChain(): array
    {
        return $this->chain;
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param ConfigInterface $config
     * @return $this
     */
    public function setConfig(ConfigInterface $config): self
    {
        $this->config = $config;

        $this->loadChain();

        return $this;
    }

    /**
     * @return $this
     */
    public function loadChain(): self
    {
        $chain = $this->config->get('chain');
        if (is_array($chain) && !empty($chain)) {
            foreach ($chain as $element) {
                if (!isset($element['plugin']) || !is_string($element['plugin']) || empty($element['plugin'])) {
                    throw new \InvalidArgumentException('No plugin identifier specified in configuration chain.');
                }
                if (!isset($element['options']) || !is_array($element['options']) || empty($element['options'])) {
                    throw new \InvalidArgumentException('No options for plugin specified in configuration chain.');
                }
                $this->add($element['plugin'], new ArrayOptions($element['options']));
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @param OptionsInterface $options
     * @return $this
     */
    public function add(string $name, OptionsInterface $options): self
    {
        $this->chain[] = new ChainElement($name, $options);

        return $this;
    }

    /**
     * @return $this
     */
    public function preventDefault(): self
    {
        $this->initials['loggers'] = [];
        $this->initials['renderer'] = null;

        return $this;
    }
}