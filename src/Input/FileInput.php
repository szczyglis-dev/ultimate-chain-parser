<?php

namespace Szczyglis\ChainParser\Input;

use Szczyglis\ChainParser\Contract\InputInterface;

/**
 * Class FileInput
 * @package Szczyglis\ChainParser\Input
 */
class FileInput implements InputInterface
{
    private $path;

    /**
     * FileInput constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return false|string
     */
    public function read()
    {
        return file_get_contents($this->path);
    }
}