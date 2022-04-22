<?php

namespace Szczyglis\ChainParser\Input;

use Szczyglis\ChainParser\Contract\InputInterface;

/**
 * Class TextInput
 * @package Szczyglis\ChainParser\Input
 */
class TextInput implements InputInterface
{
    private $data;

    /**
     * TextInput constructor.
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function read()
    {
        return $this->data;
    }
}