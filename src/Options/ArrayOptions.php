<?php

namespace Szczyglis\ChainParser\Options;

use Szczyglis\ChainParser\Contract\OptionsInterface;
use Szczyglis\ChainParser\Helper\AbstractOptions;

/**
 * Class ArrayOptions
 * @package Szczyglis\ChainParser\Options
 */
class ArrayOptions extends AbstractOptions implements OptionsInterface
{
    const NAME = 'array-options';

    private $raw;
    private $parsed;
    private $isInitialized = false;

    /**
     * ArrayOptions constructor.
     * @param array $raw
     */
    public function __construct(array $raw)
    {
        $this->raw = $raw;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $this->parse();

        if (array_key_exists($key, $this->parsed)) {
            return $this->parsed[$key];
        }
    }

    public function parse()
    {
        if ($this->isInitialized) {
            return;
        }

        $this->parsed = $this->raw;

        ksort($this->parsed);

        $this->isInitialized = true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        $this->parse();

        if (array_key_exists($key, $this->parsed)) {
            return true;
        }
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $this->parse();

        return $this->parsed;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }
}