<?php

namespace Szczyglis\ChainParser\Plugin\Splitter\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class RegexWorker
 * @package Szczyglis\ChainParser\Plugin\Splitter\Worker
 */
class RegexWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param string $input
     * @param string $pattern
     * @return array
     * @throws \Exception
     */
    public function split(string $input, string $pattern)
    {
        $splitter = 'x' . md5(random_bytes(32));
        $this->log(sprintf('Generated random split string [%s]', $splitter));
        $data = preg_replace($pattern, '$1' . $splitter, $input);
        $this->log(sprintf('Exploding string...'));
        return explode($splitter, $data);
    }
}