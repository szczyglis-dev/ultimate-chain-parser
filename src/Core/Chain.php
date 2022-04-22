<?php

namespace Szczyglis\ChainParser\Core;

use Szczyglis\ChainParser\Contract\InputInterface;
use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Contract\ConfigInterface;

/**
 * Class Chain
 * @package Szczyglis\ChainParser\Core
 */
class Chain
{
    private $plugins = [];
    private $loggers = [];
    private $chain = [];
    private $output = [];
    private $input;
    private $config;

    /**
     * @return $this
     */
    public function run(): self
    {
        $prevOutput = $this->input->read();
        $prevData = [];

        foreach ($this->loggers as $logger) {
            $logger->onBegin();
        }

        foreach ($this->chain as $i => $element) {
            $name = $element->getName();
            $element->setIteration($i);
            $element->setConfig($this->config);

            // initialize data
            $data = new DataBag;
            $data->setElement($element);
            $data->setPrev('input', $prevOutput);
            $data->setPrev('data', $prevData);
            $data->set('input', $this->input);
            $data->set('outputs', $this->output);

            if (isset($this->plugins[$name])) {
                $plugin = $this->plugins[$name];
                $element->setInstance($plugin);

                // register options resolve map
                if (method_exists($plugin, 'registerOptions')) {
                    $map = $plugin->registerOptions();
                    if (!is_array($map)) {
                        throw new \RuntimeException('registerOptions() method must return array with options mapping.');
                    }
                    $element->getOptions()->setResolveMap($map);
                }

                // register workers
                if (method_exists($plugin, 'registerWorkers')) {
                    $workers = $plugin->registerWorkers();

                    foreach ($workers as $id => $worker) {
                        if (!$worker instanceof WorkerInterface) {
                            throw new \RuntimeException('Plugin Worker must implement WorkerInterface.');
                        }
                        $worker->setData($data);
                        $data->addWorker($id, $worker);
                        if ($plugin instanceof LoggableInterface && $worker instanceof LoggableWorkerInterface) {
                            $worker->setLoggerCallback(function ($log) use ($plugin) {
                                $plugin->log($log);
                            });
                        }
                    }
                }

                // setup loggers
                if ($plugin instanceof LoggableInterface) {
                    foreach ($this->loggers as $logger) {
                        $plugin->addLogger($logger);
                        $logger->setData($data);
                        $logger->onIterationBegin();
                    }
                }

                $plugin->setData($data);
                $plugin->run();

                $prevOutput = $data->get('output');
                $prevData = $data->get('data');

                if ($plugin instanceof LoggableInterface) {
                    foreach ($this->loggers as $logger) {
                        $logger->onIterationEnd();
                    }
                    $data->setLog($plugin->getLogs());
                }

                $this->output[$i] = $data;
            }
        }

        foreach ($this->loggers as $logger) {
            $logger->onEnd();
        }

        return $this;
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
     * @param array $chain
     * @return $this
     */
    public function setChain(array $chain): self
    {
        $this->chain = $chain;

        return $this;
    }

    /**
     * @param array $plugins
     * @return $this
     */
    public function setPlugins(array $plugins): self
    {
        $this->plugins = $plugins;

        return $this;
    }

    /**
     * @param array $loggers
     * @return $this
     */
    public function setLoggers(array $loggers): self
    {
        $this->loggers = $loggers;

        return $this;
    }

    /**
     * @param ConfigInterface|null $config
     * @return $this
     */
    public function setConfig(?ConfigInterface $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutput(): array
    {
        return $this->output;
    }
}