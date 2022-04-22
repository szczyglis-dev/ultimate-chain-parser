<?php

namespace Szczyglis\ChainParser\Plugin\Limiter\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class RegexWorker
 * @package Szczyglis\ChainParser\Plugin\Limiter\Worker
 */
class RegexWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param array $data
     * @param array $patterns
     * @return array
     */
    public function limitToAllowed(array &$data, array $patterns)
    {
        $result = [];
        $c = count($data);
        foreach ($data as $i => $block) {
            foreach ($patterns as $pattern) {
                if (!TextTools::isPattern($pattern)) {
                    $this->log(sprintf('Warning: Invalid pattern: %s. Aborting!', $pattern));
                    continue;
                }

                if (preg_match($pattern, $block)) {
                    $result[] = $block;
                    $this->log(sprintf('Matched line [%u / %u] >>%s<< to pattern: %s', $i, $c, $block, $pattern));
                    break;
                }
            }
        }
        return $result;
    }
}