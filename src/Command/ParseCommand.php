<?php

namespace Szczyglis\ChainParser\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Szczyglis\ChainParser\Input\FileInput;
use Szczyglis\ChainParser\Logger\ConsoleLogger;
use Szczyglis\ChainParser\Renderer\ConsoleRenderer;
use Szczyglis\ChainParser\Config\YamlConfig;
use Szczyglis\ChainParser\ChainParser;

/**
 * Class ParseCommand
 * @package Szczyglis\ChainParser\Command
 */
class ParseCommand extends Command
{
    protected static $defaultName = 'chainparser';

    protected function configure(): void
    {
        $this
            ->addArgument('source', InputArgument::REQUIRED, 'Path to file with input data, eg. /home/user/data.txt')
            ->addArgument('config', InputArgument::REQUIRED, 'Path to file with YAML config, eg. /home/user/config.yaml')
            ->addOption(
                'log',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Enable/disable log output',
                1,
        )
            ->addOption(
                'data',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Enable/disable data output',
                1,
        )
            ->setDescription('Runs Chain Parser')
            ->setHelp('Chain Parser command line interface');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourcePath = $input->getArgument('source');
        $configPath = $input->getArgument('config');
        $isLog = (bool)$input->getOption('log');
        $isData = (bool)$input->getOption('data');

        $parser = new ChainParser;
        $parser->preventDefault();
        $parser->setConfig(new YamlConfig($configPath));
        $parser->setRenderer(new ConsoleRenderer($input, $output));
        if ($isLog) {
            $parser->addLogger(new ConsoleLogger($input, $output));
        }

        $parser->setInput(new FileInput($sourcePath));
        $parser->run();
        $parser->renderOutput();

        if ($isData) {
            $parser->renderData();
        }

        return Command::SUCCESS;
    }
}