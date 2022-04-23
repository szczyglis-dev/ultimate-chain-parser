<?php

namespace Szczyglis\ChainParser\Helper;

/**
 * Class AbstractWorker
 * @package Szczyglis\ChainParser\Helper
 */
abstract class AbstractWorker
{
    use Traits\DataTrait;
    use Traits\WorkerLoggerTrait;
    use Traits\DatasetTrait;
    use Traits\RangeTrait;
    use Traits\RegexTrait;
    use Traits\TextTrait;
}