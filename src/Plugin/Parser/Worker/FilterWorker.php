<?php

namespace Szczyglis\ChainParser\Plugin\Parser\Worker;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;
use Szczyglis\ChainParser\Helper\TextTools;

/**
 * Class FilterWorker
 * @package Szczyglis\ChainParser\Plugin\Parser\Worker
 */
class FilterWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
    /**
     * @param string $block
     * @param string $field
     * @param string $mode
     * @return bool
     */
    public function isIgnored(string &$block, string &$field, string $mode)
    {
        $patterns = [];

        switch ($mode) {
            case 'before':
                $patterns = $this->getOption('regex_ignore_before');
                break;
            case 'after':
                $patterns = $this->getOption('regex_ignore_after');
                break;
        }

        if (empty($patterns) || !is_array($patterns)) {
            return false;
        }

        foreach ($patterns as $pattern) {
            if (!TextTools::isPattern($pattern)) {
                $this->log(sprintf('Warning: Invalid pattern: %s. Aborting!', $pattern));
                continue;
            }
            if (preg_match($pattern, $block)) {
                $this->log(sprintf('Block >>%s<< marked as ignored with regex: %s', $block, $pattern));
                return true;
            }
        }

        return false;
    }
}