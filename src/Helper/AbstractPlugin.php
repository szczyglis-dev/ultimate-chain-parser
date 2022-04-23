<?php

namespace Szczyglis\ChainParser\Helper;

/**
 * Class AbstractPlugin
 * @package Szczyglis\ChainParser\Helper
 */
abstract class AbstractPlugin
{
    use Traits\DataTrait;
    use Traits\LoggerTrait;
    use Traits\DatasetTrait;
    use Traits\FlowTrait;
    use Traits\RangeTrait;
    use Traits\RegexTrait;
    use Traits\TextTrait;
}