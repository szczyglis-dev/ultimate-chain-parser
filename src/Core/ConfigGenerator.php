<?php

namespace Szczyglis\ChainParser\Core;

use Szczyglis\ChainParser\ChainParser;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ConfigGenerator
 * @package Szczyglis\ChainParser\Core
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