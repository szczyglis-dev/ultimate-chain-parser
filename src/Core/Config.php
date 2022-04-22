<?php

namespace Szczyglis\ChainParser\Core;

/**
 * Class Config
 * @package Szczyglis\ChainParser\Core
 */
class Config
{
    const VERSION = '1.0.3';
    const BUILD = '2022-04-22';
    const GITHUB_URL = 'https://github.com/szczyglis-dev/ultimate-chain-parser';
    const WEB_URL = 'https://szczyglis.dev/ultimate-chain-parser';
    const IS_DEMO_MODE = false;
    const DEMO_MODE_INPUT_LIMIT = 10000;
    const DEMO_MODE_OPTION_LIMIT = 10000;
    const DEMO_MODE_CHAIN_LENGTH_LIMIT = 30;

    /**
     * @return array
     */
    public static function getOptions()
    {
        return [
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
                'rowset_separator' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => '<b>Column-mode parsing.</b> The rowset (set of blocks) separator in the input data. Use this option only if parsing columns located in multiple rowsets. It enables column-matching mode where columns are used as blocks and rows are used as rowsets. Leave this field empty to not use rowset explode and use row to column parsing mode. Default: empty.',
                    'example' => '',
                    'syntax' => '',
                ],
                'input_block_separator' => [
                    'type' => 'i',
                    'value' => '\n',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'The record (block) separator in the input data. Depending on the operating mode (rows or columns), you can enter e.g. a new line or a comma. Default: \n = newline.',
                    'example' => '',
                    'syntax' => '',
                ],
                'input_block_interval' => [
                    'type' => 'i',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'The interval at which blocks are parsed, you can eg only process every second or every third block. Default: 1 (parse every single block)',
                    'example' => '',
                    'syntax' => '',
                ],
                'output_record_separator' => [
                    'type' => 'i',
                    'value' => '\n',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Separator with which the records on the output will be joined, default: \n = newline',
                    'example' => '',
                    'syntax' => '',
                ],
                'output_field_separator' => [
                    'type' => 'i',
                    'value' => ',',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Separator with which fields in records on output will be joined, default: comma (,)',
                    'example' => '',
                    'syntax' => '',
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

            'splitter' => [
                'interval_split' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Splits blocks by defined interval, default = 1',
                    'example' => '',
                    'syntax' => '',
                ],
                'range_output' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Leave only blocks in defined range, leave empty to export all, or type ranges separated by coma,  e.g.: 0, 3, 5-7, 15- .Indexing starts from 0',
                    'example' => '',
                    'syntax' => '',
                ],
                'regex_split' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' Split blocks by defined regex pattern, leave empty to disable.<br/>You can use () to leave matched string, e.g.: to split by \'foo\' and leave \'foo\' in output use regex: <b>/(foo)/</b>',
                    'example' => '',
                    'syntax' => '',
                ],
                'input_separator' => [
                    'type' => 'i',
                    'value' => '\n',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Separator used to explode input into blocks, default: \n = new line',
                    'example' => '',
                    'syntax' => '',
                ],
                'output_separator' => [
                    'type' => 'i',
                    'value' => '\n',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' Separator used to join blocks in output, default: \n = new line',
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
                'input_separator' => [
                    'type' => 'i',
                    'value' => '\n',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Separator used to explode input into blocks, default: \n = new line',
                    'example' => '',
                    'syntax' => '',
                ],
                'output_separator' => [
                    'type' => 'i',
                    'value' => '\n',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Separator used to join blocks in output, default: \n = new line',
                    'example' => '',
                    'syntax' => '',
                ],
            ],

            'eraser' => [
                'interval_erase' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' Removes blocks at the defined interval',
                    'example' => '',
                    'syntax' => '',
                ],
                'range' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Removes blocks in specified range, specify range(s) separated by coma, indexing is from 0',
                    'example' => '',
                    'syntax' => '',
                ],
                'regex_erase' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Regular expressions whose matches will be removed throughout the document. <br/>The matching is done in the space of the entire document, you can enter a few on new lines.',
                    'example' => '',
                    'syntax' => '',
                ],
                'input_separator' => [
                    'type' => 'i',
                    'value' => '\n',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' Separator used to explode input into blocks, default: \n = new line',
                    'example' => '',
                    'syntax' => '',
                ],
                'output_separator' => [
                    'type' => 'i',
                    'value' => '\n',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Separator used to join blocks in output, default: \n = new line',
                    'example' => '',
                    'syntax' => '',
                ],
            ],

            'limiter' => [
                'interval' => [
                    'type' => 'i',
                    'value' => '1',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Restrict output to only blocks matching the given interval, default: 1',
                    'example' => '',
                    'syntax' => '',
                ],
                'range' => [
                    'type' => 'i',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => ' Limit blocks in output to specified ranges, leave empty to allow all blocks, or specify range(s) separated by coma, indexing is from 0',
                    'example' => '0, 3, 5-7, 15-',
                    'syntax' => '',
                ],
                'regex_allowed' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => '  Restrict output to only blocks matching the given regular expressions. You can enter a lot in new lines.',
                    'example' => '/^abc/<br/>/^xyz/',
                    'syntax' => '/REGEX/ (per line)',
                ],
                'input_separator' => [
                    'type' => 'i',
                    'value' => '\n',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Separator used to explode input into blocks, default: \n = new line',
                    'example' => '',
                    'syntax' => '',
                ],
                'output_separator' => [
                    'type' => 'i',
                    'value' => '\n',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Separator used to join blocks in output, default: \n = new line',
                    'example' => '',
                    'syntax' => '',
                ],
            ],

            'replacer' => [
                'regex_all' => [
                    'type' => 't',
                    'value' => '',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Regular expressions to replace the matched string. The matching takes place throughout the document, you can enter several phrases on new lines.',
                    'example' => ' /^abc/ => \'bca\'<br/>/^xyz/ => \'zxc\'',
                    'syntax' => '/REGEX/ => "REPLACED STRING" (per line)',
                ],
                'regex_block' => [
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
                'input_separator' => [
                    'type' => 'i',
                    'value' => '\n',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Separator used to explode input into blocks, default: \n = new line',
                    'example' => '',
                    'syntax' => '',
                ],
                'output_separator' => [
                    'type' => 'i',
                    'value' => '\n',
                    'placeholder' => '',
                    'label' => '',
                    'help' => 'Separator used to join blocks in output, default: \n = new line',
                    'example' => '',
                    'syntax' => '',
                ],
            ],
        ];
    }
}