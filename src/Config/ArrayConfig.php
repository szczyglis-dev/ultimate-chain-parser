<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Config;

use Szczyglis\ChainParser\Contract\ConfigInterface;

/**
 * Class ArrayConfig
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class ArrayConfig implements ConfigInterface
{
    private $raw;
    private $parsed;
    private $isInitialized = false;

    /**
     * ArrayConfig constructor.
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

        $this->isInitialized = true;
    }

    /**
     * @param string $key
     * @param $value
     * @return mixed|void
     */
    public function set(string $key, $value)
    {
        $this->parse();

        $this->parsed[$key] = $value;
    }

    /**
     * @param string $key
     * @return bool|mixed
     */
    public function has(string $key)
    {
        $this->parse();

        if (array_key_exists($key, $this->parsed)) {
            return true;
        }
    }

    public function all()
    {
        $this->parse();

        return $this->parsed;
    }
}