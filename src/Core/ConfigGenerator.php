<?php

/**
 * This file is part of szczyglis/ultimate-chain-parser.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ChainParser\Core;

use Szczyglis\ChainParser\ChainParser;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ConfigGenerator
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class ConfigGenerator
{
    /**
     * @param ChainParser $parser
     * @param string $format
     * @return false|string
     */
    public function build(ChainParser $parser, string $format = 'yaml')
    {
        $chain = $parser->getChain();
        $config = $parser->getConfig();

        $elements = [];
        foreach ($chain as $item) {
            $elements[] = [
                'plugin' => $item->getName(),
                'options' => $item->getOptions()->all(),
            ];
        }

        $result = [];
        if (!is_null($config)) {
            $result = $config->all();
        }
        $result['chain'] = $elements;

        switch ($format) {
            case 'json':
                return json_encode($result, JSON_PRETTY_PRINT);
                break;
            case 'yaml':
                return Yaml::dump($result, 8);
                break;
        }
    }
}