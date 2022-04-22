<?php

namespace Szczyglis\ChainParser\Plugin\Eraser\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class RegexWorker
 * @package Szczyglis\ChainParser\Plugin\Eraser\Worker
 */
class RegexWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param string $input
     * @param array $patterns
     * @return void
     */
    public function erase(string &$input, array &$patterns)
    {
        foreach ($patterns as $pattern) {
            if (!TextTools::isPattern($pattern)) {
                $this->log(sprintf('Warning: Invalid pattern: %s. Aborting!', $pattern));
                continue;
            }
            $this->log(sprintf('Erasing using pattern: %s', $pattern));
            $input = preg_replace($pattern, '', $input);
            $this->log(sprintf('Erased...'));
        }
    }
}