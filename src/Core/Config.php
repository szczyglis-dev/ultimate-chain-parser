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

/**
 * Class Config
 * @package szczyglis/ultimate-chain-parser
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/ultimate-chain-parser
 */
class Config
{
    const VERSION = '1.2.13';
    const BUILD = '2024-08-26';
    const GITHUB_URL = 'https://github.com/szczyglis-dev/ultimate-chain-parser';
    const WEB_URL = 'https://szczyglis.dev/ultimate-chain-parser';
    const IS_DEMO_MODE = false;
    const DEMO_MODE_INPUT_LIMIT = 100000;
    const DEMO_MODE_OPTION_LIMIT = 10000;
    const DEMO_MODE_CHAIN_LENGTH_LIMIT = 30;

    /**
     * @return array
     */
    public static function getOptions()
    {
        $separator = [
            'sep_input_rowset' => [
                'type' => 'i',
                'value' => '',
                'placeholder' => '',
                'label' => '',
                'help' => 'Input separator for the rowsets dimension, or leave empty if the data is not rowset-based.',
                'example' => '',
                'syntax' => '',
            ],
            'sep_input_row' => [
                'type' => 'i',
                'value' => '\n',
                'placeholder' => '',
                'label' => '',
                'help' => 'Input separator for the rows dimension, or leave empty if the data is not row-based.',
                'example' => '',
                'syntax' => '',
            ],
            'sep_input_col' => [
                'type' => 'i',
                'value' => '',
                'placeholder' => '',
                'label' => '',
                'help' => 'Input separator for the columns dimension, or leave empty if the data is not column-based.',
                'example' => '',
                'syntax' => '',
            ],
            'sep_output_rowset' => [
                'type' => 'i',
                'value' => '\n',
                'placeholder' => '',
                'label' => '',
                'help' => 'Output separator for the rowsets dimension, or leave empty if the data is not rowset-based.',
                'example' => '',
                'syntax' => '',
            ],
            'sep_output_row' => [
                'type' => 'i',
                'value' => '\n',
                'placeholder' => '',
                'label' => '',
                'help' => 'Output separator for the rows dimension, or leave empty if the data is not row-based.',
                'example' => '',
                'syntax' => '',
            ],
            'sep_output_col' => [
                'type' => 'i',
                'value' => ',',
                'placeholder' => '',
                'label' => '',
                'help' => 'Output separator for the columns dimension, or leave empty if the data is not column-based.',
                'example' => '',
                'syntax' => '',
            ],
        ];

        $dataset = [
            'use_dataset' => [
                'type' => 'c',
                'value' => '1',
                'placeholder' => '',
                'label' => '',
                'help' => 'Use the previous output dataset as the current dataset instead of the joined data. If disabled, the previous parsed output will be used as the current input, or the input if this is the first element in the chain.',
                'example' => '',
                'syntax' => '',
                'checked' => true,
            ],
        ];

        $dataMode = [
            'data_mode' => [
                'type' => 'r',
                'placeholder' => '',
                'value' => 'column',
                'label' => '',
                'help' => 'Select the dimension on which the other options will operate; this will also affect how the data is parsed in the dataset.',
                'example' => '',
                'syntax' => '',
                'choices' => [
                    'rowset' => 'rowset',
                    'row' => 'row',
                    'column' => 'column',
                ],
            ],
        ];

        $options = [
            'parser' => [
                'regex_match' => [
                    'type' => 't',
                    'value' => "id:/^[\d]+$/\ntitle:/^[^\d]+/\nactor:/^[^\d]+/\ndescription: /^[^\d]+/",
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'A set of regular expressions to match the data with the corresponding fields. You can add multiple patterns for each field on separate lines. If more than one pattern is provided, only one needs to match (the logical OR operation is performed).',
                    'example' => 'id:/^[\d]+$/<br/>name:/^[^\d]+/<br/>name:/[^\d]+$/',
                    'syntax' => ' FIELDNAME:/REGEX/ (per line)',
                ],
                'regex_ignore_before' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'List of regular expressions that, if matched (before applying the "replace_filter_before"), will skip the matched data block. This is used to ignore blocks matching the given pattern. You can add multiple patterns on separate lines.',
                    'example' => '/^XYZ+$/<br/>/^abc123+$/<br/>/^some unwanted data/',
                    'syntax' => '/REGEX/ (per line)',
                ],
                'regex_ignore_after' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'List of regular expressions that, if matched (after applying the "replace_filter_before"), will skip the matched data block. This is used to ignore blocks matching the given pattern. You can add multiple patterns on separate lines.',
                    'example' => '/^XYZ+$/<br/>/^abc123+$/<br/>/^some unwanted data/',
                    'syntax' => '/REGEX/ (per line)',
                ],
                'replace_field_before' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'A list of regular expressions used to replace or precondition a data block with another, applied before each attempt to match a given field. This can be used to pre-filter the data before each match attempt. You can add multiple replace patterns for each field on separate lines.',
                    'example' => 'id:/^[\d]+$/<br/>name:/^([^\d]+)/ => $1<br/>name:/^([\d])+$/ => "another for same field"',
                    'syntax' => 'FIELDNAME:/REGEX/ => "REPLACED STRING" (per line)',
                ],
                'replace_field_after' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'A list of regular expressions to replace an already matched field with another text string. This can be used for post-processing of matched fields. You can add multiple replace patterns for each field on separate lines.',
                    'example' => 'id:/^[\d]+$/<br/>name:/^([^\d]+)/ => $1',
                    'syntax' => 'FIELDNAME:/REGEX/ => "REPLACED STRING" (per line)',
                ],
                'replace_block_before' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'A list of regular expressions to replace or pre-prepare a data block with another, applied to the entire data block before attempting to match. This can be used to pre-filter the data before each match attempt. You can add multiple replace patterns on separate lines.',
                    'example' => '/^[\d]+$/<br/>/^([^\d]+)/ => $1',
                    'syntax' => '/REGEX/ => "REPLACED STRING" (per line)',
                ],
                'replace_block_after' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'A list of regular expressions to replace an already matched block with another text string. Applied to the entire block of data after a match is made, this can be used for post-processing of matched data. You can add multiple replace patterns on separate lines.',
                    'example' => '/^[\d]+$/<br/>/^([^\d]+)/ => $1',
                    'syntax' => '/REGEX/ => "REPLACED STRING" (per line)',
                ],
                'empty_field_placeholder' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'If TRUE, replaces empty spaces in the matched fields with the string specified in the `empty_placeholder` option.',
                    'example' => '',
                    'syntax' => '',
                ],
                'fields' => [
                    'type' => 't',
                    'value' => 'id,title,actor,description',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'List of fields to be matched. Enter the names of the fields into which you want to organize the parsed data, e.g., id, title, actor, description. The fields should be entered on one line, separated by commas (,).',
                    'example' => 'id,title,actor,description',
                    'syntax' => 'FIELDNAME1,FIELDNAME2,FIELDNAME3,FIELDNAME4...',
                ],
                'output_fields' => [
                    'type' => 't',
                    'value' => 'id,title,actor,description',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'List of fields to match from the list above to be displayed in the output, e.g., id, title, actor. The fields should be entered on one line, separated by commas (,).',
                    'example' => 'id,title,actor,description',
                    'syntax' => 'FIELDNAME1,FIELDNAME2,FIELDNAME3,FIELDNAME4...',
                ],
                'is_debug' => [
                    'type' => 'c',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'If TRUE, append debugger information to each line of output.',
                    'example' => '',
                    'syntax' => '',
                ],
                'is_empty_field_placeholder' => [
                    'type' => 'c',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'If TRUE, replace empty spaces in the matched fields with the string specified in the `empty_placeholder` option.',
                    'example' => '',
                    'syntax' => '',
                ],
            ],

            'cleaner' => [
                'trim' => [
                    'type' => 'c',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Apply the `trim()` function to every block.',
                    'example' => '',
                    'syntax' => '',
                    'checked' => true,
                ],
                'clean_blocks' => [
                    'type' => 'c',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Remove empty blocks',
                    'example' => '',
                    'syntax' => '',
                    'checked' => true,
                ],
                'strip_tags' => [
                    'type' => 'c',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Apply the `strip_tags()` function to all.',
                    'example' => '',
                    'syntax' => '',
                ],
                'fix_newlines' => [
                    'type' => 'c',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Replace all \r\n with \n',
                    'example' => '',
                    'syntax' => '',
                    'checked' => true,
                ],
            ],

            'limiter' => [
                'interval_allow' => [
                    'type' => 'i',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Restrict output to only blocks matching the given interval. Default is 1.',
                    'example' => '',
                    'syntax' => '',
                ],
                'interval_deny' => [
                    'type' => 'i',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Restrict output to only blocks NOT matching the given interval. Default is 1.',
                    'example' => '',
                    'syntax' => '',
                ],
                'range_allow' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Limit blocks in output to specified ranges, or leave empty to allow all blocks. Specify range(s) separated by commas, with indexing starting from 0.',
                    'example' => '0, 3, 5-7, 15-',
                    'syntax' => '',
                ],
                'range_deny' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Limit blocks in output to ranges NOT specified, or leave empty to allow all blocks. Specify range(s) separated by commas, with indexing starting from 0.',
                    'example' => '0, 3, 5-7, 15-',
                    'syntax' => '',
                ],
                'regex_allow' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Restrict output to only blocks matching the given regular expressions. You can define multiple regex patterns, one per line.',
                    'example' => '/^abc/<br/>/^xyz/',
                    'syntax' => '/REGEX/ (per line)',
                ],
                'regex_deny' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Restrict output to only blocks NOT matching the given regular expressions. You can define multiple regex patterns, one per line.',
                    'example' => '/^abc/<br/>/^xyz/',
                    'syntax' => '/REGEX/ (per line)',
                ],
            ],

            'replacer' => [
                'regex' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Regular expressions to replace the matched string. The matching is done block by block. You can enter multiple expressions on new lines. An input separator is required to split the document into blocks.',
                    'example' => ' /^abc/ => \'bca\'<br/>/^xyz/ => \'zxc\'',
                    'syntax' => '/REGEX/ => "REPLACED STRING" (per line)',
                ],
                'interval' => [
                    'type' => 'i',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Limit replacing only to a specific interval, such as every other block. Default is 1.',
                    'example' => '',
                    'syntax' => '',
                ],
                'range' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Limit blocks to replace to specified ranges, or leave empty to replace all blocks. Specify range(s) separated by commas, with indexing starting from 0.',
                    'example' => ' 0, 3, 5-7, 15-',
                    'syntax' => '',
                ],
            ],
        ];

        $options['cleaner'] = array_merge($options['cleaner'], $separator, $dataset);
        $options['limiter'] = array_merge($options['limiter'], $separator, $dataset, $dataMode);
        $options['replacer'] = array_merge($options['replacer'], $separator, $dataset, $dataMode);
        $options['parser'] = array_merge($options['parser'], $separator, $dataset);

        return $options;
    }
}