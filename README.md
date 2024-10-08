Release: **1.2.13** | build: **2024.08.26** | PHP: **^7.2.5|^8.0**

# Ultimate Chain Parser - advanced chain-flow-based parser

**Ultimate Chain Parser is a modular package designed for chain processing of text data, converting it into structured output.**
The application concept is based on processing data in successive iterations using configurable data processing modules. Each module in the execution chain sequentially accesses the output of the preceding module and uses it as input.

## How to install
```
composer require szczyglis/ultimate-chain-parser
``` 
**For what purposes can the Ultimate Chain Parser be used?**

- Processing any data broken into inconsistent rows or columns into a standardized format (e.g., CSV).
- Re-parsing data according to specific complex patterns.
- Creating datasets that are easy to insert into a database or import into software like Excel.
- Performing complex text manipulation.
- ...and many other tasks.

## Live Demo: https://szczyglis.dev/ultimate-chain-parser

![parser2](https://user-images.githubusercontent.com/61396542/164573563-e034b324-37e2-4742-a120-fd8a90324708.png)

# Features

- Re-parsing poorly arranged data into structured, clean datasets (e.g., CSV)
- Parsing poorly arranged or broken data copied from websites, Word documents, or PDFs
- Running pre-configured tools (plugins) in a sequence
- Performing complex text data manipulations
- Parsing complex data using programmable regular expressions executed in a defined sequence
- Featuring an easy-to-use and powerful configuration system
- Executing actions through splitting tasks into smaller, separate tools, each performing a different batch of tasks in cooperation with the rest
- Including tools that can work separately or together: parser, cleaner, limiter, and replacer
- Offering a modular structure based on a plug-in system, with each element extendable or replaceable by custom implementations; every component has its own interface for extending functionality or replacing existing ones
- Providing multiple extendable components: configuration providers, input data readers, data parsers, renderers, loggers, etc.
- Including an HTML/AJAX-based configurator application for real-time testing and configuration
- Featuring a command-line tool for ease of use
- Easy integration with modern frameworks like Symfony

# Requirements:

  - PHP 7.2.5+ or PHP 8.0+
  - Composer - https://getcomposer.org/

# Example of an Action

**Sample text data that requires processing:**

```
123
terminator
schwarzenegger

action movie

very good


456
titanic

dicaprio


same director
                  

```

**Ugly, right? The Ultimate Chain Parser can transform such inconsistently arranged data into a structured format such as CSV, JSON, raw PHP array, or any other schema easily defined by the user:**
```
123,terminator,schwarzenegger,action movie very good
456,titanic,dicaprio,same director
```

```
[
    [
        {
            "id": "123",
            "title": "terminator",
            "actor": "schwarzenegger",
            "description": "action movie very good"
        },
        {
            "id": "456",
            "title": "titanic",
            "actor": "dicaprio",
            "description": "same director"
        }
    ]
]
```

The above CSV and JSON data has been generated completely automatically using only a few configuration options provided in the parser input. The main concept behind the operation is to run a set of processing tools (called Plugins) in a chain. Each successively started process accesses the output from the previous process in the chain. Each of these chain elements can be freely configured with different options. Configuration can be done in many ways: by running Chain Parser directly from your code, loading configuration from an external file and running from the command line, or completely live using the Ajax web form-based configurator included in the package. Ultimate Chain Parser can also directly return a ready (not parsed) dataset prepared from analyzed data (in the form of a PHP array or JSON data).

# Installation

**Composer / packagist**

```
composer require szczyglis/ultimate-chain-parser
``` 

**Manual installation**

- Download the zip package and extract it.
- Run `composer install` in the project directory to install dependencies.
- Include the Composer autoloader in your application and instantiate the `ChainParser` object.


# Example of use

```php
  <?php

  // app.php

  require  __DIR__.'/vendor/autoload.php';

  use Szczyglis\ChainParser\ChainParser;
  use Szczyglis\ChainParser\Input\TextInput;
  use Szczyglis\ChainParser\Options\ArrayOptions;

  $parser = new ChainParser();
  $parser->setInput(new TextInput('some text data that needs to be parsed'));
  $parser->add('parser', new ArrayOptions([
    // options here
  ]); 
  $parser->run();

  $result = $parser->renderOutput();
  $log = $parser->renderLog();

  echo $result;
```

## Live example

Go to https://szczyglis.dev/ultimate-chain-parser to run the online demo, or run **example.php** included in the package to open the AJAX-based demo with the chain configurator in real-time mode. On the page that you see on the screen, you will find the described options, each of which you will be able to use when manually configuring the chain:

### Adding Elements to the Chain

**Manually adding elements to the chain is very easy:**

```php
  $parser = new ChainParser();  
  $parser
    ->add('cleaner', new ArrayOptions([
        //options
    ])
    ->add('parser', new ArrayOptions([
        //options
    ])
    ->add('limiter', new ArrayOptions([
        //options
    ]); 
  
  $parser->run();
```
The above code adds 3 new elements with defined tools (called Plugins) to the chain. Each of these elements will operate on the output of the previous element. The options are passed as the second argument, wrapped in the option provider class. You can combine the elements in any order and quantity until you achieve the desired result.

**Elements do not have to be added manually as described above. You can use a predefined configuration to programmatically build the defined chain automatically.**

## Configuration, options and usage

### Tool: parser

The main tool of the application, used to parse data according to specific patterns and rules.

**Options:**

**- use_dataset** - `boolean` Enables operation on a dataset prepared by the previous element, instead of its parsed output. This allows transferring an already prepared set of data between elements. Do not use this in the first element of the chain when starting from raw input, as there is no prepared dataset from a previous element at the beginning.

**- regex_match** - `array` A set of regular expressions to match the data with the corresponding fields. You can add multiple patterns for each field; if more than one pattern is provided, only one needs to match (the logical OR operation is performed). This option can be specified in text (one field per line) or directly in a PHP array.

  *Syntax:* FIELDNAME:/REGEX/ (per line)

  *Example (text):*

    id:/^[\d]+$/
    name:/^[^\d]+/
    name:/[^\d]+$/

  *Example (array):*
  ```php
    $options['regex_match'] = [
      'id' => [
         0 => '/^[\d]+$/',
      ],
      'name' => [
        0 => '/^[^\d]+/',
        1 => '/[^\d]+$/',
      ],
    ];
```
**- regex_ignore_before** - `array` A list of regular expressions that, if matched (before applying "replace_filter_before"), will skip the matched data block. This is used to ignore blocks matching the given pattern. You can enter multiple expressions, either in text form with each expression on a new line, or directly in a PHP array.

  *Syntax:* /REGEX/ (per line)

  *Example (text):*

    /^XYZ+$/
    /^some unwanted data/

  *Example (array):*
  ```php
    $options['regex_ignore_before'] = [
      0 => '/^XYZ+$/',
      1 => '/^some unwanted data/',
    ];
  ```
**- regex_ignore_after** - `array` A list of regular expressions that, if matched (after applying "replace_filter_before"), will skip the matched data block. This is used to ignore blocks matching the given pattern. You can enter multiple expressions, either in text form with each expression on a new line, or directly in a PHP array.

  *Syntax*: /REGEX/ (per line)

  *Example (text):*

    /^XYZ+$/
    /^some unwanted data/

  *Example (array):*
  ```php
    $options['regex_ignore_after'] = [
      0 => '/^XYZ+$/',
      1 => '/^some unwanted data/',
    ];
  ```
**- replace_field_before** - `array` A list of regular expressions used to replace or precondition a data block with another, applied before each attempt to match a given field. This can be used to pre-filter the data before each match attempt. You can enter multiple expressions, either in text form with each expression on a new line, or directly in a PHP array.

  *Syntax:* FIELDNAME:/REGEX/ => "REPLACED STRING" (one pattern per line)

  *Example (text):*

    id:/^[\d]+$/ => 12345
    name:/^([^\d]+)/ => $1
    name:/^([A-Z]+)/ => abc$1

  *Example (array):*
  ```php
    $options['replace_field_before'] = [
      'id' => [
        0 => [
          'pattern' => '/^[\d]+$/',
          'replacement' => '12345',
        ],        
      ],
      'name' => [
        0 => [
          'pattern' => '/^([^\d]+)/',
          'replacement' => '$1',
        ], 
        1 => [
          'pattern' => '/^([A-Z]+)/',
          'replacement' => 'abc$1',
        ],        
      ],
    ];
  ``` 
**- replace_field_after** - `array` A list of regular expressions to replace an already matched field with another text string. This can be used for post-processing of matched fields. You can enter multiple expressions, either in text form with each expression on a new line, or directly in a PHP array.

  *Syntax:* FIELDNAME:/REGEX/ => "REPLACED STRING" (one pattern per line)

  *Example (text):*

    id:/^[\d]+$/ => 12345
    name:/^([^\d]+)/ => $1

  *Example (array):*
  ```php
    $options['replace_field_after'] = [
      'id' => [
        0 => [
          'pattern' => '/^[\d]+$/',
          'replacement' => '12345',
        ],        
      ],
      'name' => [
        0 => [
          'pattern' => '/^([^\d]+)/',
          'replacement' => '$1',
        ],        
      ],
    ];
```
**- replace_block_before** - `array` A list of regular expressions to replace or pre-prepare a data block with another, applied to the entire data block before attempting to match. This can be used to pre-filter the data before each match attempt. You can enter multiple expressions, either in text form with each expression on a new line, or directly in a PHP array.
  
  *Syntax:* /REGEX/ => "REPLACED STRING" (one pattern per line).

  *Example (text):*

    /^[\d]+$/ => 12345
    /^([^\d]+)/ => $1

  *Example (array):*
  ```php
    $options['replace_block_before'] = [
      0 => [
        'pattern' => '/^[\d]+$/',
        'replacement' => '12345',
      ]
      1 => [
        'pattern' => '/^([^\d]+)/',
        'replacement' => '$1',
      ],
    ];
```
**- replace_block_after** - `array` A list of regular expressions to replace an already matched block with another text string. Applied to the entire block of data after a match is made, this can be used for post-processing of matched data. You can enter multiple expressions, either in text form with each expression on a new line, or directly in a PHP array.

  *Syntax:* /REGEX/ => "REPLACED STRING" (one pattern per line)

  *Example (text):*

    /^[\d]+$/ => 12345
    /^([^\d]+)/ => $1

  *Example (array):*
  ```php
    $options['replace_block_after'] = [
      0 => [
        'pattern' => '/^[\d]+$/',
        'replacement' => '12345',
      ]
      1 => [
        'pattern' => '/^([^\d]+)/',
        'replacement' => '$1',
      ],
    ];
```
**- fields** - `array` A list of fields to be matched. Enter the names of the fields into which you want to divide the parsed data, e.g., id, name, actor, description. The fields should be entered on one line, separated by commas (,), or as an array of fields.

  *Syntax:* FIELDNAME1,FIELDNAME2,FIELDNAME3,FIELDNAME4...

  *Example (text):*

    id,title,actor,description

  *Example (array):*
  ```php
    $options['fields'] = [
      'id',
      'title',
      'actor',
      'description',
    ];
```
**- output_fields** - `array` A list of fields to match from the list above to be displayed in the output, e.g., id, name, actor. The fields should be entered on one line, separated by commas (,), or as an array of fields.

  *Syntax:* FIELDNAME1,FIELDNAME2,FIELDNAME3,FIELDNAME4...

  *Example (text):*

    id,title,actor,description

  *Example (array):*
  ```php
    $options['output_fields'] = [
      'id',
      'title',
      'actor',
      'description',
    ];

```
**- sep_input_rowset** - `string` Separator for rowsets for input when splitting into rowsets, used depending on the expected output distribution, e.g., \n.

**- sep_input_row** - `string` Separator for rows in input data when splitting into rows, used depending on the expected output distribution, e.g., \n.

**- sep_input_column** - `string` Separator for columns in input data when splitting into columns, used depending on the expected output distribution, e.g., a comma (,).

**- sep_output_rowset** - `string` Separator for rowsets in output when joining results from rowsets, used depending on the desired output format, e.g., \n.

**- sep_output_row** - `string` Separator for rows in output when joining results from rows, used depending on the desired output format, e.g. \n.

**- sep_output_column** - `string` Separator for columns in output when joining results from columns, used depending on the desired output format, e.g. comma (,).

**- empty_field_placeholder** - `string` Placeholder to replace the given field if the matched field is empty. Leave blank if you do not want to use any placeholders.

**- is_debug** - `boolean`, If TRUE, append debugger information to each line of output.

**- is_empty_field_placeholder** - `boolean` If TRUE, replace empty spaces in the matched fields with the string specified in the `empty_placeholder` option.

___

## Tool: cleaner

A tool for cleaning, sanitizing, and pre-preparing input data for further processing.

**Options:**

**- use_dataset** - `boolean`, Enables operation on a dataset prepared by the previous element, instead of its parsed output. This allows transferring an already prepared set of data between elements. Do not use this in the first element of the chain when starting from raw input, as there is no prepared dataset from a previous element at the beginning.

**- trim** - `boolean` Applies the `trim()` function to every block.

**- clean_blocks** - `boolean` Removes empty blocks.

**- fix_newlines** - `boolean` Replaces all \r\n with \n.

**- strip_tags** - `boolean` Applies the `strip_tags()` function to all.

**- sep_input_rowset** - `string` Separator for rowsets for input when splitting into rowsets, used depending on the expected output distribution, e.g., \n.

**- sep_input_row** - `string` Separator for rows in input data when splitting into rows, used depending on the expected output distribution, e.g., \n.

**- sep_input_column** - `string` Separator for columns in input data when splitting into columns, used depending on the expected output distribution, e.g., a comma (,).

**- sep_output_rowset** - `string` Separator for rowsets in output when joining results from rowsets, used depending on the desired output format, e.g., \n.

**- sep_output_row** - `string` Separator for rows in output when joining results from rows, used depending on the desired output format, e.g. \n.

**- sep_output_column** - `string` Separator for columns in output when joining results from columns, used depending on the desired output format, e.g. comma (,).

___


## Tool: limiter

A tool for limiting the amount of generated or received data according to specific patterns and rules. It can also be used for deleting data.

**Options:**

**- use_dataset** - `boolean` Enables operation on a dataset prepared by the previous element, instead of its parsed output. This allows transferring an already prepared set of data between elements. Do not use this in the first element of the chain when starting from raw input, as there is no prepared dataset from a previous element at the beginning.

**- data_mode** - `string` `[rowset|row|column]` - Selects the dimension on which the other options will operate.

**- interval_allow** - `integer` Restricts output to blocks that match the given interval. Default is 1.

**- range_allow** - `array` Limits output to blocks that match specified ranges; leave empty to allow all blocks. Specify range(s) separated by commas, with indexing starting from 0.

  *Syntax:* integer1, integer2, integer3-integer4,integer5-,-integer6 [...]

  *Example (text):*

    0, 3, 5-7, 15-, -20

  *Example (array):*
  ```php
    $options['range'] = [
      0 => 0,
      1 => 3,
      2 => [
        'from' => 5
        'to' => 7,
      ],
      3 => [
        'from' => 15,
        'to' => null,
      ],
      4 => [
        'from' => null,
        'to' => 20,
      ],
    ];
  ```
**- regex_allow** - `array` Restricts output to blocks that match the given regular expressions. You can enter multiple expressions, each on a new line.

  *Syntax:* /REGEX/ (per line)

  *Example (text):*

    /^XYZ+$/
    /^ZYX+$/

  *Example (array):*
  ```php
    $options['regex_allow'] = [
      0 => '/^XYZ+$/',
      1 => '/^ZYX+$/',
    ];

  ```
**- interval_deny** - `integer` Restricts output to blocks that do not match the given interval. Default is 1.

**- range_deny** - `array`, Limits blocks in output to those that do not match specified ranges. Leave empty to allow all blocks, or specify range(s) separated by commas. Indexing starts from 0.

  *Syntax:* integer1, integer2, integer3-integer4,integer5-,-integer6 [...]

  *Example (text):*

    0, 3, 5-7, 15-, -20

  *Example (array):*
  ```php
    $options['range'] = [
      0 => 0,
      1 => 3,
      2 => [
        'from' => 5
        'to' => 7,
      ],
      3 => [
        'from' => 15,
        'to' => null,
      ],
      4 => [
        'from' => null,
        'to' => 20,
      ],
    ];
  ```
**- regex_deny** - `array` Restricts output to blocks that do not match the given regular expressions. You can enter multiple expressions, each on a new line.

  *Syntax:* /REGEX/ (per line)

  *Example (text):*

    /^XYZ+$/
    /^ZYX+$/

  *Example (array):*
  ```php
    $options['regex_deny'] = [
      0 => '/^XYZ+$/',
      1 => '/^ZYX+$/',
    ];

  ```
**- sep_input_rowset** - `string` Separator for rowsets for input when splitting into rowsets, used depending on the expected output distribution, e.g., \n.

**- sep_input_row** - `string` Separator for rows in input data when splitting into rows, used depending on the expected output distribution, e.g., \n.

**- sep_input_column** - `string` Separator for columns in input data when splitting into columns, used depending on the expected output distribution, e.g., a comma (,).

**- sep_output_rowset** - `string` Separator for rowsets in output when joining results from rowsets, used depending on the desired output format, e.g., \n.

**- sep_output_row** - `string` Separator for rows in output when joining results from rows, used depending on the desired output format, e.g. \n.

**- sep_output_column** - `string` Separator for columns in output when joining results from columns, used depending on the desired output format, e.g. comma (,).
___

## Tool: replacer

A tool for converting specific batches of data to other formats according to defined patterns and rules.

**Options:**

**- use_dataset** - `boolean` Enables operation on a dataset prepared by the previous element, instead of its parsed output. This allows transferring an already prepared set of data between elements. Do not use this in the first element of the chain when starting from raw input, as there is no prepared dataset from a previous element at the beginning.

**- data_mode** - `string` `[rowset|row|column]` Selects the dimension on which the other options will operate.

**- regex** - `array` Regular expressions to replace the appropriate strings. You can enter multiple patterns, each on a new line.

  *Syntax:* /REGEX/ => "REPLACED STRING" (one pattern per line)

  *Example (text):*

    /^[\d]+$/ => 12345
    /^([^\d]+)/ => $1

  *Example (array):*
  ```php
    $options['regex'] = [
      0 => [
        'pattern' => '/^[\d]+$/',
        'replacement' => '12345',
      ]
      1 => [
        'pattern' => '/^([^\d]+)/',
        'replacement' => '$1',
      ],
    ];
  ```

**- interval** - `integer` Limits replacing to a specific interval. Default is 1.

**- range** - `array` Limits blocks to replace to specified ranges; leave empty to replace all blocks. Specify range(s) separated by commas. Indexing starts from 0.

  *Syntax:* integer1, integer2, integer3-integer4,integer5-,-integer6 [...]

  *Example (text):*

    0, 3, 5-7, 15-, -20

  Example (array):
  ```php
    $options['range'] = [
      0 => 0,
      1 => 3,
      2 => [
        'from' => 5
        'to' => 7,
      ],
      3 => [
        'from' => 15,
        'to' => null,
      ],
      4 => [
        'from' => null,
        'to' => 20,
      ],
    ];
  ```

**- sep_input_rowset** - `string` Separator for rowsets for input when splitting into rowsets, used depending on the expected output distribution, e.g., \n.

**- sep_input_row** - `string` Separator for rows in input data when splitting into rows, used depending on the expected output distribution, e.g., \n.

**- sep_input_column** - `string` Separator for columns in input data when splitting into columns, used depending on the expected output distribution, e.g., a comma (,).

**- sep_output_rowset** - `string` Separator for rowsets in output when joining results from rowsets, used depending on the desired output format, e.g., \n.

**- sep_output_row** - `string` Separator for rows in output when joining results from rows, used depending on the desired output format, e.g. \n.

**- sep_output_column** - `string` Separator for columns in output when joining results from columns, used depending on the desired output format, e.g. comma (,).
___

# Running in command line (PHP CLI)

The package includes a Symfony command in the `Command` directory. You can run the command with the script `cmd.php`:

**Usage in CLI:**

  ```
  ./cmd.php chainparser /path/to/data /path/to/config.yaml [--options]
  ```

**Arguments:**

`/path/to/data` - path to the file with text data to parse.

`/path/to/config.yaml` - path to the file with the YAML config.

**Options:**

  --log=0   - disable log output

  --data=0  - disable raw data output

The package contains 2 example files:

  - example.txt

  - example.yaml

You can use these example files for testing:

  ```
  ./cmd.php chainparser ./example.txt ./example.yaml
  ```

Disable log and data output, leaving only the parse result output:

  ```
  ./cmd.php chainparser ./example.txt ./example.yaml --log=0 --data=0
  ```

Store the output result in a file named `output.txt`:

  ```
  ./cmd.php chainparser ./example.txt ./example.yaml --log=0 --data=0 > output.txt
  ```


# Config options

You can use the included config provider or write your own. Currently, two config providers are included:

- **ArrayConfig** - works with the configuration provided directly from a PHP array.

- **YamlConfig** - works with the configuration read from a YAML file.

### Usage
```php  
<?php

namespace App;

use Szczyglis\ChainParser\Config\YamlConfig;


//...

$parser->setConfig(new YamlConfig('/path/to/config.yaml'));
```
or
```php
<?php

namespace App;

use Szczyglis\ChainParser\Config\ArrayConfig;

//...

$parser->setConfig(new ArrayConfig([
  'key' => 'value',
]));
```

### Available configuration options

**- full_output** - `boolean`, default: false. If true, all outputs from all chain elements are rendered in the output. If false, only the last result is rendered.

**- log_file** - `string`, absolute path to the logfile if using PsrLogger (Monolog).


**- no_log** - bool, if true, logging is disabled.


# Loggers

You can use the included loggers or write your own. The package includes three different loggers:

- **ArrayLogger** - logs everything to a PHP array.

- **ConsoleLogger** - logs output to the CLI console.

- **PsrLogger** - stores logs in a file using Monolog Logger.


### Usage
```php
<?php

namespace App;

use Szczyglis\ChainParser\Logger\ArrayLogger;
use Szczyglis\ChainParser\Logger\PsrLogger;
use Szczyglis\ChainParser\Logger\ConsoleLogger;


//...

$parser->addLogger(new ArrayLogger());
$parser->addLogger(new PsrLogger('/path/to/logfile'));
$parser->addLogger(new ConsoleLogger());

```

You can register multiple loggers at once; the output will be sent to all of them simultaneously.

Accessing the generated logs is very simple:

```php  
  ...
$parser->run();

$log = $parser->renderLog();

dump($log);
```
Or if you want to get raw (not rendered/parsed) log data, use:
```php
$parser->run();

$output = $parser->getOutput();
$log = $output->get('logs');
dump($log);
```

___

# Extending Chain Parser

The concept is based on full modularity, allowing you to extend the package with your own parsers and features. 
Each element can be adapted to meet your needs and solve your specific problems.

## Configuration providers

Configuration providers are responsible for reading and parsing the configuration. There are a few base configuration providers included in the package, but you can easily create your own.

Configuration providers are located in: `Szczyglis\ChainParser\Config` namespace.

### Included configuration providers

- **ArrayConfig** - reads configuration from a PHP array passed as a constructor argument.

- **YamlConfig** - reads configuration from a YAML file (path to the file needs to be passed as a constructor argument).

### How to create your own configuration provider

- implement the interface `Szczyglis\ChainParser\Contract\ConfigInterface`
- attach your config provider using the `setConfig()` method


### Example

```php  
<?php

// MyConfig.php

namespace App;

use Szczyglis\ChainParser\Contract\ConfigInterface;
use Szczyglis\ChainParser\Helper\AbstractInput;

class MyConfig implements ConfigInterface
{
  private $config;

  public function __construct(array $config)
  {
    $this->config = $config;
  }

  public function get(string $key)
  {
    if (array_key_exists($key, $this->config)) {
      return $this->config[$key];
    }   
  }

  public function set(string $key, $value)
  {
    $this-config[$key] = $value;    
  }

  public function has(string $key)
  {
    if (array_key_exists($key, $this->config)) {
      return true;
    }   
  }

  public function all()
  {
    return $this->config;
  }
}

```
```php

<?php

// app.php

use Szczyglis\ChainParser\ChainParser;
use App\MyConfig;

$parser = new ChainParser();
$parser->setConfig(new MyConfig([
  'foo' => 'bar',
]));

// rest of initialization, config, etc...

$parser->run();
$parser->renderOutput();
```
___


## Input data providers

Input data providers are responsible for reading input data. There are a few base input data providers included in the package, but you can easily create your own.

Input data providers are located in: `Szczyglis\ChainParser\Input` namespace.

### Included input data providers

- **TextInput** - reads input data directly from a text string passed as a constructor argument.

- **FileInput** - reads input data from a file (path to the file needs to be passed as a constructor argument).

### How to create your own input data provider

- implement the interface `Szczyglis\ChainParser\Contract\InputInterface`
- optionally extend the abstract helper class `Szczyglis\ChainParser\Helper\AbstractInput`
- attach your input data provider using the `setInput()` method


### Example

```php  
<?php

// MyInput.php

namespace App;

use Szczyglis\ChainParser\Contract\InputInterface;
use Szczyglis\ChainParser\Helper\AbstractInput;

class MyInput extends AbstractInput implements InputInterface
{
  private $input;
  private $dataset = [];

  public function __construct(string $input)
  {
    $this->input = $input;
  }

  public function getInput()
  {
    return $this->input;
  }

  public function getDataset()
  {
    return $this->dataset;
  }
}

```
```php

<?php

// app.php

use Szczyglis\ChainParser\ChainParser;
use App\MyInput;

$parser = new ChainParser();
$parser->setInput(new MyInput('some input data here...'));

// rest of initialization, config, etc...

$parser->run();
$parser->renderOutput();
```
___

## Loggers

Loggers are responsible for logging data from plugins. There are a few base loggers included in the package, but you can easily create your own.

Loggers are located in the `Szczyglis\ChainParser\Logger` namespace.

### Included loggers

- **ArrayLogger** - writes logs directly to a PHP array.

- **PsrLogger** - writes logs to a file using Monolog.

- **ConsoleLogger** - displays logs directly to the console.

### How to create your own logger

- implement the interface `Szczyglis\ChainParser\Contract\LoggerInterface`
- optionally extend the abstract helper class `Szczyglis\ChainParser\Helper\AbstractLogger`
- add a logger using the `addLogger()` method


### Example

```php  
<?php

// MyLogger.php

namespace App;

use Szczyglis\ChainParser\Contract\LoggerInterface;
use Szczyglis\ChainParser\Helper\AbstractLogger;

class MyLogger extends AbstractLogger implements LoggerInterface
{
  private $logs;

  public function addMessage(string $message, array $data)
  {
    $this->logs[] = $message;
  }

  // ...rest of code
}

```
```php

<?php

// app.php

use Szczyglis\ChainParser\ChainParser;
use App\MyLogger;

$parser = new ChainParser();
$parser->addLogger(new MyLogger());

// rest of initialization, config, etc...

$parser->run();
$parser->renderOutput();
```
___

## Options providers

Options providers are responsible for reading, parsing, and serving the plugin's options. There are a few base options providers included in the package, but you can easily create your own.

Options providers are located in the `Szczyglis\ChainParser\Options` namespace.

### Included options providers

- **ArrayOptions** - reads options directly from a PHP array passed as a constructor argument.

- **FormOptions** - parses options passed as text or from an HTML form.

### How to create your own options provider

- implement the interface `Szczyglis\ChainParser\Contract\OptionsInterface`
- optionally extend the abstract helper class `Szczyglis\ChainParser\Helper\AbstractOptions`
- initialize the plugin using your own options provider

### Example

```php  
<?php

// MyOptions.php

namespace App;

use Szczyglis\ChainParser\Contract\OptionsInterface;
use Szczyglis\ChainParser\Helper\AbstractOptions;

class MyOptions extends AbstractOptions implements OptionsInterface
{
  private $options;

  public function __construct(string $options)
  {
    $this->options = $options;
  }

  public function get(string $key)
  {
    if (array_key_exists($key, $this->options)) {
      return $this->options[$key];
    }   
  }

  public function has(string $key)
  {
    if (array_key_exists($key, $this->options)) {
      return true;
    }   
  }

  public function all()
  {
    return $this->options;
  }
}

```
```php
<?php

// app.php

use Szczyglis\ChainParser\ChainParser;
use App\MyOptions;

$parser = new ChainParser();
$parser->add('parser', new MyOptions([
    'foo' => 'bar',
]));

// rest of initialization, config, etc...

$parser->run();
$parser->renderOutput();
```

___

## Option resolvers

Option resolvers are responsible for parsing options, e.g., when options are provided using an HTML form and need to be parsed into an array. There are a few base option resolvers included in the package, but you can easily create your own.

Option resolvers are located in the `Szczyglis\ChainParser\OptionResolver` namespace.

### Included options resolvers

- **SingleLineResolver** - parses options from a single line.

- **MultiLineResolver** - parses more complex syntax from multiple lines.

- **RangeResolver** - parses range-based options, e.g., 1,2,8-10.

### How to create your own option resolver, e.g. for use with own plugin configuration

- implement the interface `Szczyglis\ChainParser\Contract\OptionResolverInterface`
- register the option resolver using `addResolver()` method


### Example

``` php
<?php

// MyResolver.php

namespace App;

use Szczyglis\ChainParser\Contract\OptionResolverInterface;
use Szczyglis\ChainParser\Helper\AbstractInput;

class MyResolver implements OptionResolverInterface
{
  public function resolve(string $key, $value)
  {
    return explode(';', $value);    
  }

  public function getName(): string
  {
    return 'my_resolver';
  }
}
```


```php
<?php

// MyPlugin.php


class MyPlugin ...

public function run(): bool
{
  // ...
}

public function registerOptions(): array
{
    return [
        'my_resolver' => [  // resolver name
            'foo', // option name
        ],
    ];
}
```


```php
<?php

// app.php

use Szczyglis\ChainParser\ChainParser;
use Szczyglis\ChainParser\Options\FormOptions;
use App\MyResolver;

$parser = new ChainParser();
$parser->addResolver(new MyResolver());
$parser->add('parser', new FormOptions([
    'foo' => 'bar1;bar2;bar3', // option "foo" will be parsed with your resolver
]));

// rest of initialization, config, etc...

$parser->run();
$parser->renderOutput();
```

___

## Plugins

Plugins are the core of the application. They are tools that run along the chain and operate on data. Each plugin can work with the raw input and the output from the previous plugin in the chain. There are also special classes called `Workers` that help you organize your code and break it down into different "subprocesses." Workers share the same data set as the main plugin and can quickly exchange their data with the plugin. You can easily register your own workers using the `registerWorker` method.

Plugins are located in the `Szczyglis\ChainParser\Plugin` namespace.

### Included plugins

- **Parser** - The main tool of the application, used to parse data according to specific patterns and rules.

- **Cleaner** - A tool for cleaning the input data, sanitizing and pre-preparing data for further processing.

- **Limiter** - A tool for limiting and removing the amount of generated or received data according to specific patterns and rules.

- **Replacer** - A tool for converting specific batches of data to other formats according to specific patterns and rules.

### How to create your own plugin

- implement the interface `Szczyglis\ChainParser\Contract\PluginInterface`
- extend the abstract helper class `Szczyglis\ChainParser\Helper\AbstractPlugin`
- add the plugin to the chain


### Example
```php
<?php

// MyPlugin.php

namespace App;

use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\LoggableInterface;
use Szczyglis\ChainParser\Helper\AbstractPlugin;

class MyPlugin extends AbstractPlugin implements PluginInterface, LoggableInterface
{
  const NAME = 'my_plugin';

  public function run(): bool
  {
    $dataset = $this->getDataset(); // get previous data or from input

    // do something with data
    
    $this->setDataset($dataset); // return data to next element or to output

    return true;
  }

  public function getName(): string
  {
    return self::NAME;
  }
}
```
```php
<?php

// app.php

use Szczyglis\ChainParser\ChainParser;
use Szczyglis\ChainParser\Input\TextInput;
use Szczyglis\ChainParser\Options\ArrayOptions;
use App\MyPlugin;

$parser = new ChainParser();
$parser->addPlugin(new MyPlugin());
$parser->setInput(new TextInput('foo'));
$parser->add('my_plugin', new ArrayOptions([
    'option' => 'value',
]));

// rest of initialization, config, etc...

$parser->run();
echo $parser->renderOutput(); // returns "Hello foo"

``` 

### Creating own Worker
```php
<?php

// MyWorker.php

namespace App;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;

class MyWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
  public function doSomeJob()
  {
    $var = $this->getVar('foo');
    $var++;
    $this->setVar('foo', $var);
  }
}

```
```php
<?php

// MyPlugin.php

namespace App;

use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\LoggableInterface;
use Szczyglis\ChainParser\Helper\AbstractPlugin;

class MyPlugin extends AbstractPlugin implements PluginInterface, LoggableInterface
{
  const NAME = 'my_plugin';

  public function run(): bool
  {
    $worker = $this->getWorker('my_worker');

    $foo = 10;
    $this->setVar('foo', $foo);

    $worker->doSomeJob();

    $bar = $this->getVar('foo');

    echo $bar; // will display 11

    return true;
  }

  public function registerWorkers(): array
  {
    return [
      'my_worker' => new MyWorker(),
    ];
  }
}
```

A worker is registered and initiated automatically as soon as it is given in the `registerWorkers()` method. As the example above showed, workers and plugins have a common container for temporary data used to exchange variables between the plugin and the worker:
```php
$this->setVar('foo', $bar); // sets var foo 
$foo = $this->getVar('foo'); // gets var foo
```


### Access to input and output data from the plugin and worker level is as follows:

```php
<?php

// MyPlugin.php

class MyPlugin ...

public function run(): bool
{
  $input = $this->getPrev('output'); // output from previous element in chain (or raw input if Plugin is first in chain)
  $prevDataset = $this->getPrev('dataset'); // output data (as `array`, not parsed) from previous element in chan
  $dataset = $this->get('dataset'); // get current dataset or output from previous element

  $rawInput = $this->get('input'); // raw, initial input

  $i = $this->getIteration(); // current iteration index in chain
  $config = $this->getConfig(); // returns config object

  $optionValue = $this->getOption('key'); // returns option value by key
  $allOptions = $this->getOptions(); // returns all options (as key => value array)

  $dataset = $this->getDataset(); // alias for $this->get('dataset');

  return true;
}

```

### Setting output data inside the Tool:

```php
<?php

// MyPlugin.php


class MyPlugin ...

public function run(): bool
{
  $input = $this->getPrev('output');  // get parsed previous output or current input
  $dataset = $this->getDataset(); // get data form previous output or current input

  // do manipulation on data

  $this->setDataset($dataset);  // data will be sent to the next element in the chain as its input

  return true;
}
```

### Helpers inside Plugin and Worker

You can use some included helpers provided by the `AbstractPlugin` and `AbstractWorker` abstract classes. When you extend your class from them, you have access to some useful methods:

`$this->isPattern($pattern): bool` - checks if `$pattern` is a valid regex pattern.

`$this->checkPatterns(array $patterns, string $string): bool` - checks if at least one of the given regex patterns matches a string.

`$this->applyPatterns(array $patterns, string $string): string` - applies any replacement patterns to the given string.

`$this->explode(string $separator, ?string $input): array` - wrapper for `explode()`, allowing explosion using a regular expression as the separator.

`$this->implode(string $joiner, array &$ary): string` - wrapper for `implode()`.

`$this->strReplace($from, $to, $data): string` - wrapper for `str_replace()`.

`$this->stripTags($data, $tags = null): string` - wrapper for `strip_tags()`.

`$this->trim($input): string` - wrapper for `trim()`.

`$this->inRange(array $ranges, int $i): bool` - checks if a number matches the given ranges.

`$this->makeDataset(?string $input, string $sepRowset, string $sepRow, string $sepCol): array` - converts a string input into a 3-dimensional dataset array using the given separators.

`$this->packDataset(array $dataset, string $sepRowset, string $sepRow, string $sepCol): string` - builds a parsed result from the dataset.

`$this->iterateDataset(array $dataset, callable $callback): array` - applies a callback to every block in the dataset and returns the dataset modified by the callback. Example:

```php

  $dataset = $this->iterateDataset($dataset, function($value) {
      return str_replace('A', 'B', $value);
  });
```
Doing this will cause the character A to be replaced with the character B for each element in the dataset.

**It is a good idea to check the code of the plugins included in the package to see live examples.**

### Registering options that require prior parsing

If you need to use options that require the data to be parsed first, e.g., into an `array`, use ready-made option resolvers or create your own resolver. To register an option with the appropriate option resolver, return its name in the array using the `registerOptions()` method. From now on, it will be parsed by the assigned resolver. The array should include the name of the resolver and a list of options assigned to it. Example of use:

```php
<?php

// MyPlugin.php


class MyPlugin ...

public function run(): bool
{
  // ...
}

public function registerOptions(): array
{
    return [
        'multiline' => [  // resolver name
            'my_pattern', // option name
        ],
        'range' => [ // resolver name
            'my_range', // option name
        ],
    ];
}
```

Included resolvers:

- **singleline** - parses single-line parameters, such as `key: value => assignment/replacement`

- **multiline** - parses multi-line parameters, where `key: value => assignment/replacement` is placed on separate lines.

- **range** - parses range parameters, such as: `1,5,10-20,15-,-80`


### Logging messages

From the Plugin and Worker level, you have access to log events using registered loggers. The `$this->log()` method is used for this purpose. To use event logging, you must implement the following interfaces:

- in plugin: `Szczyglis\ChainParser\Contract\LoggableInterface`
- in worker: `Szczyglis\ChainParser\Contract\LoggableWorkerInterface`

#### Example of use

```php
<?php

// MyPlugin.php

namespace App;

use Szczyglis\ChainParser\Contract\PluginInterface;
use Szczyglis\ChainParser\Contract\LoggableInterface;
use Szczyglis\ChainParser\Helper\AbstractPlugin;

class MyPlugin extends AbstractPlugin implements PluginInterface, LoggableInterface
{
  const NAME = 'my_plugin';

  public function run(): bool
  {
    $this->log('some message');

    return true;
  }
}

```
```php
<?php

// MyWorker.php

namespace App;

use Szczyglis\ChainParser\Contract\WorkerInterface;
use Szczyglis\ChainParser\Contract\LoggableWorkerInterface;
use Szczyglis\ChainParser\Helper\AbstractWorker;

class MyWorker extends AbstractWorker implements WorkerInterface, LoggableWorkerInterface
{
  public function doSomeJob()
  {
    $this->log('some message');
  }
}

```
___

## Renderer

Renderers are responsible for displaying the output. There are a few base renderers included in the package, but you can easily create your own.

Renderers are located in the `Szczyglis\ChainParser\Renderer` namespace.

### Included renderers

- **TextRenderer** - parses output into TXT/HTML format

- **ConsoleRenderer** - sends output to the console

### How to create your own renderer

- implement the interface `Szczyglis\ChainParser\Contract\RendererInterface`
- optionally extend the abstract helper class `Szczyglis\ChainParser\Helper\AbstractRenderer`
- set the renderer using `setRenderer()` method


### Example

```php  
<?php

// MyRenderer.php

namespace App;

use Szczyglis\ChainParser\Contract\RendererInterface;
use Szczyglis\ChainParser\Helper\AbstractRenderer;

class MyRenderer extends AbstractRenderer implements RendererInterface
{
  public function renderOutput(?array $options = [])
  {
    foreach ($this->output as $item) {
      dump($item->get('output'));
    }
  }

  public function renderData(?array $options = [])
  {
    foreach ($this->output as $item) {
      dump($item->get('data'));
    }
  }

  public function renderLog(?array $options = [])
  {
    foreach ($this->output as $item) {
      $loggers = $item->getLog();
      foreach ($loggers as $lines) {
        foreach ($lines as $line) {
          dump($line);
        }       
      }
    }
  }
} 
```
```php
<?php

// app.php

use Szczyglis\ChainParser\ChainParser;
use App\MyRenderer;

$parser = new ChainParser();
$parser->preventDefault(); // unregister default renderer
$parser->setRenderer(new MyRenderer()); // set your own

// rest of initialization, config, etc...

$parser->run();
$parser->renderOutput(); // will display dumped output from your renderer
$parser->renderData(); // will display dumped output from your renderer
$parser->renderLog(); // will display dumped output from your renderer

```


## Exporting configuration

You can export the currently running configuration at any time, including the entire chain and its options. To do this, use the config generator:

```php
<?php

// app.php

require __DIR__ . '/vendor/autoload.php';

use Szczyglis\ChainParser\ChainParser;
use Szczyglis\ChainParser\Core\ConfigGenerator;

$parser = new ChainParser;

// initialization, configuration, etc...

$parser->run();

$myConfig = (new ConfigGenerator())->build($parser, 'yaml'); // yaml | json

dump($myConfig); // displays configuration in Yaml format

```

You can reuse an exported and saved configuration by loading it with the configuration loader `Szczyglis\ChainParser\Config\YamlConfig`.
___

## Live Demo: https://szczyglis.dev/ultimate-chain-parser

![src](https://user-images.githubusercontent.com/61396542/164572766-fdb57adf-c661-447f-ae55-f4394de3d3db.png)


# Changelog

**1.0.0** - Published first release. (2022-04-22)

**1.0.4** - Increased limit in demo mode, documentation fixes. (2022-04-22)

**1.2.6** - Full dataset sharing added, eraser and splitter plugins removed (their role is taken over by limiter), added configuration of dataset looks by freely specifying each separator for each dimension (rowset, row, column). (2022-04-23)

**1.2.10** - Updated PHPDoc, updated example config YAML. (2022-04-25)

**1.2.11** - Updated composer.json. (2022-04-28)

**1.2.12** - Improved documentation (2024-08-26)

**1.2.13** - Extended options description in docs and in the example app (2024-08-26)

--- 
**Ultimate Chain Parser is free to use, but if you like it, you can support my work by buying me a coffee ;)**

https://www.buymeacoffee.com/szczyglis

**Enjoy!**

MIT License | 2022 Marcin 'szczyglis' Szczygliński

https://github.com/szczyglis-dev/ultimate-chain-parser

https://szczyglis.dev/ultimate-chain-parser

Contact: szczyglis@protonmail.com
