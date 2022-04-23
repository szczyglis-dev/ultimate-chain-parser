<?php

namespace Szczyglis\ChainParser\Core;

/**
 * Class Config
 * @package Szczyglis\ChainParser\Core
 */
class Config
{
    const VERSION = '1.2.0';
    const BUILD = '2022-04-23';
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
                'help' => 'Input separator for rowsets dimmension or leave empty if not rowset-based data',
                'example' => '',
                'syntax' => '',
            ],
            'sep_input_row' => [
                'type' => 'i',
                'value' => '\n',
                'placeholder' => '',
                'label' => '',
                'help' => 'Input separator for rows dimmension or leave empty if not row-based data',
                'example' => '',
                'syntax' => '',
            ],
            'sep_input_col' => [
                'type' => 'i',
                'value' => '',
                'placeholder' => '',
                'label' => '',
                'help' => 'Input separator for columns dimmension or leave empty if not column-based data',
                'example' => '',
                'syntax' => '',
            ],
            'sep_output_rowset' => [
                'type' => 'i',
                'value' => '\n',
                'placeholder' => '',
                'label' => '',
                'help' => 'Output separator for rowsets dimmension or leave empty if not rowset-based data',
                'example' => '',
                'syntax' => '',
            ],
            'sep_output_row' => [
                'type' => 'i',
                'value' => '\n',
                'placeholder' => '',
                'label' => '',
                'help' => 'Output separator for rows dimmension or leave empty if not row-based data',
                'example' => '',
                'syntax' => '',
            ],
            'sep_output_col' => [
                'type' => 'i',
                'value' => ',',
                'placeholder' => '',
                'label' => '',
                'help' => 'Output separator for columns dimmension or leave empty if not column-based data',
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
                'help' => 'Use previous  output dataset as current dataset instead joined data (if disabled then previous parsed output will be used as current input, or input if this is first element in chain)',
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
                'help' => 'Select the dimension on which the other options are to operate, it will also affect the form of parsing the data in the table',
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
                    'help' => ' A set of regular expressions to match the data found with the corresponding fields. You can add more than one pattern for each field in separate lines, if more are given, it is enough that one of them is matched (the logical OR operation is performed).',
                    'example' => 'id:/^[\d]+$/<br/>name:/^[^\d]+/<br/>name:/[^\d]+$/',
                    'syntax' => ' FIELDNAME:/REGEX/ (per line)',
                ],
                'regex_ignore_before' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' List of regular expressions that, if matched (before apply "replace_filter_before"), will skip the matched data block. Is used to ignore blocks matching the given pattern. You can add more than one pattern in separate lines.',
                    'example' => '/^XYZ+$/<br/>/^abc123+$/<br/>/^some unwanted data/',
                    'syntax' => '/REGEX/ (per line)',
                ],
                'regex_ignore_after' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' List of regular expressions that, if matched (after apply "replace_filter_before"), will skip the matched data block. Is used to ignore blocks matching the given pattern. You can add more than one pattern in separate lines.',
                    'example' => '/^XYZ+$/<br/>/^abc123+$/<br/>/^some unwanted data/',
                    'syntax' => '/REGEX/ (per line)',
                ],
                'replace_field_before' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' A list of regular expressions used to replace or precondition a data block with another, run before each attempt to match a given field. Can be used to pre-filter the data before each match attempt. You can add more than one replace pattern for each field in separate lines.',
                    'example' => 'id:/^[\d]+$/<br/>name:/^([^\d]+)/ => $1<br/>name:/^([\d])+$/ => "another for same field"',
                    'syntax' => 'FIELDNAME:/REGEX/ => "REPLACED STRING" (per line)',
                ],
                'replace_field_after' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' A list of regular expressions to replace an already matched field with another text string. It can be used for post-processing of already matched fields. You can add more than one replace pattern for each field in separate lines.',
                    'example' => 'id:/^[\d]+$/<br/>name:/^([^\d]+)/ => $1',
                    'syntax' => 'FIELDNAME:/REGEX/ => "REPLACED STRING" (per line)',
                ],
                'replace_block_before' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' A list of regular expressions to replace or pre-prepare a data block with another, run over the entire data block before trying to match. Can be used to pre-filter the data before each match attempt. You can add more than one replace pattern in separate lines.',
                    'example' => '/^[\d]+$/<br/>/^([^\d]+)/ => $1',
                    'syntax' => '/REGEX/ => "REPLACED STRING" (per line)',
                ],
                'replace_block_after' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'A list of regular expressions to replace an already matched block with another text string. Run for the entire block of data after a match is made. Can be used for post-processing of already matched data. You can add more than one replace pattern in separate lines.',
                    'example' => '/^[\d]+$/<br/>/^([^\d]+)/ => $1',
                    'syntax' => '/REGEX/ => "REPLACED STRING" (per line)',
                ],
                'empty_field_placeholder' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'if TRUE, then replaces empty spaces in the matched fields with the string specified in the `empty_placeholder` option',
                    'example' => '',
                    'syntax' => '',
                ],
                'fields' => [
                    'type' => 't',
                    'value' => 'id,title,actor,description',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'List of fields to be matched, enter here the names of the fields into which you want to organize the parsed data, e.g. id, title, actor, description. It should be entered in one line, separated by a comma (,).',
                    'example' => 'id,title,actor,description',
                    'syntax' => 'FIELDNAME1,FIELDNAME2,FIELDNAME3,FIELDNAME4...',
                ],
                'output_fields' => [
                    'type' => 't',
                    'value' => 'id,title,actor,description',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'List of fields to match from the list above to be displayed in the output, e.g. id, title, actor. It should be entered in one line, separated by a comma (,).',
                    'example' => 'id,title,actor,description',
                    'syntax' => 'FIELDNAME1,FIELDNAME2,FIELDNAME3,FIELDNAME4...',
                ],
                'is_debug' => [
                    'type' => 'c',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'if TRUE, then append debugger information to each line of output',
                    'example' => '',
                    'syntax' => '',
                ],
                'is_empty_field_placeholder' => [
                    'type' => 'c',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'if TRUE, then replaces empty spaces in the matched fields with the string specified in the `empty_placeholder` option',
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
                    'help' => 'Apply function trim() to every block',
                    'example' => '',
                    'syntax' => '',
                    'checked' => true,
                ],
                'clean_blocks' => [
                    'type' => 'c',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' Remove empty blocks',
                    'example' => '',
                    'syntax' => '',
                    'checked' => true,
                ],
                'strip_tags' => [
                    'type' => 'c',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Apply function strip_tags() to all',
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
                    'help' => 'Restrict output to only blocks matching the given interval, default: 1',
                    'example' => '',
                    'syntax' => '',
                ],
                'interval_deny' => [
                    'type' => 'i',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Restrict output to only blocks NOT matching the given interval, default: 1',
                    'example' => '',
                    'syntax' => '',
                ],
                'range_allow' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' Limit blocks in output to specified ranges, leave empty to allow all blocks, or specify range(s) separated by coma, indexing is from 0',
                    'example' => '0, 3, 5-7, 15-',
                    'syntax' => '',
                ],
                'range_deny' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' Limit blocks in output to NOT specified ranges, leave empty to allow all blocks, or specify range(s) separated by coma, indexing is from 0',
                    'example' => '0, 3, 5-7, 15-',
                    'syntax' => '',
                ],
                'regex_allow' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => '  Restrict output to only blocks matching the given regular expressions. You can enter a lot in new lines.',
                    'example' => '/^abc/<br/>/^xyz/',
                    'syntax' => '/REGEX/ (per line)',
                ],
                'regex_deny' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => '  Restrict output to only blocks NOT matching the given regular expressions. You can enter a lot in new lines.',
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
                    'help' => ' Regular expressions to replace the matched string. The matching is done block by block, you can enter several expressions on new lines. Requires an input seperator to split the document into blocks.',
                    'example' => ' /^abc/ => \'bca\'<br/>/^xyz/ => \'zxc\'',
                    'syntax' => '/REGEX/ => "REPLACED STRING" (per line)',
                ],
                'interval' => [
                    'type' => 'i',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' Limits replacing only to a specific interval, such as every other block, default: 1',
                    'example' => '',
                    'syntax' => '',
                ],
                'range' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' Limit blocks to replace to specified ranges, leave empty to replace all blocks, or specify range(s) separated by coma, indexing is from 0',
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