
# Ultimate Chain Parser - advanced chain-flow based parser

PHP 7.2.5+, current release: **1.2.5** build 2022-04-23

**"Ultimate Chain Parser" is a modular package for chain processing text data and converting it into a structured output.
The concept of application is based on processing in subsequent iterations using configurable data processing modules in a configured manner. Each such element in the execution chain accesses the output of the previous element in the chain as input.**

**Install with composer:**
```
composer require szczyglis/ultimate-chain-parser
``` 
**For which purposes the Ultimate Chain Parser can be used?**

- processing to standarized format (e.g. CSV) any data broken into inconsistent rows or columns
- reparsing data according to a specific complex patterns
- creating datasets easy-to-put in the database or to import into software like Excel.
- complex text manipulation
- ...and for many other tasks.

## Live Demo: https://szczyglis.dev/ultimate-chain-parser

![parser2](https://user-images.githubusercontent.com/61396542/164573563-e034b324-37e2-4742-a120-fd8a90324708.png)

# Features:

- reparsing bad-arranged data into structured clean datasets, e.g. CSV  
- parsing bad-arranged or broken data copied from websites, Word documents or PDFs  
- running pre-configured tools (plugins) in the chain
- complex manipulation on text data
- complex data parsing using programmable regular expressions run one after another in a defined sequence
- easy to use and powerful configuration system
- the flow of action based on the splitting into smaller separate tools, each of which performs a different batch of tasks in cooperation with the rest
- tools included in the package that can work separately or together: parser, cleaner, limiter and replacer
- modular structure based on the plug-in system, in addition, each element of the application can be extended or completely replaced with a self-created one - each element of the application has its own interface for the programmer that allows for any extension of functionality or replacement of existing ones
- multiple extendable components: configuration providers, input data readers, data parsers, renderers, loggers, etc.
- HTML/Ajax based configurator application included - you can test and configure the chain in real-time
- command line tool included
- easy to integrate with modern frameworks (like Symfony)


# Requirements:

  - PHP 7.2.5+
  - Composer - https://getcomposer.org/


# An example of an action:

Sample text data that requires processing:

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

**Ugly, right? Ultimate Chain Parser can transform such inconsistently arranged data into a structured format like CSV, JSON, raw PHP array and any other schema defined easily by user:**

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

The above CSV and JSON data has been generated completely automatically using only a few configuration options given in the parser input. The main concept behind the operation is to run a set of processing tools (called Plugins) in a chain. Each successively started process accesses the output from the previous process in the chain. Each of these chain elements can be freely configured with different options. Configuration can be done in many ways by running Chain Parser directly from your code, loading configuration from an external file and running from command line, or completely live using the Ajax web form-based configurator included in the package. Ultimate Chain Parser can also directly return a ready (not parsed) dataset prepared from analyzed data (in the form of a PHP array or JSON data).


# Installation:

**Composer / packagist:**

```
composer require szczyglis/ultimate-chain-parser
``` 

**Manual installation:**

  - download zip package and extract it.
  - run `composer install` in project directory to install dependencies
  - include composer autoloader in your application and instatiate ChainParser object.


# Example of use:

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

## Live example:

Go to https://szczyglis.dev/ultimate-chain-parser and run online demo or run **example.php** included in package to open AJAX-based demo with chain configurator in real-time mode. On the page that you see on the screen, you will see the options described - each of them you will be able to use when manually configuring the chain:

### Adding elements to chain:

**Manualy adding elements to chain is very easy:**

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
The above code adds 3 new elements (iterations) with defined tools (named Plugins) to the chain.
Each of these elements will operate on the output of the previous element.
The options are passed as the second argument, wrapped in the option provider class. You can combine the elements in any order and amount until you get the result you want.

**Elements do not have to be added manually as above. For this purpose, you can use a predefined, prepared configuration that will build the defined chain itself in a completely programmatic way.**


## Configuration, options and usage

### Tool: parser

The main tool of the application, used to parse data according to specific patterns and rules.

**Options:**

**- use_dataset** - `boolean` Enables operation on a dataset prepared by the previous element, instead of its parsed output, allows to transfer between elements an already ready set of prepared data. Don't use in first element of chain when starting from raw input (because at the beginning there is no prepared dataset from the previous element)

**- regex_match** - `array` A set of regular expressions to match the data found with the corresponding fields. You can add more than one pattern for each field, if more are given, it is enough that one of them is matched (the logical OR operation is performed). The option can be given in text (a separate field on a separate line), or directly in the array.

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
**- regex_ignore_before** - `array` A list of regular expressions that, if matched( before apply "replace_filter_before"), will skip the matched data block. Is used to ignore blocks matching the given pattern. You can enter many, in text form each expression on a new line, or directly in the array.

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
**- regex_ignore_after** - `array` A list of regular expressions that, if matched (after apply "replace_filter_before"), will skip the matched data block. Is used to ignore blocks matching the given pattern. You can enter many, in text form each expression on a new line, or directly in the array.

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
**- replace_field_before** - `array` A list of regular expressions used to replace or precondition a data block with another, run before each attempt to match a given field. Can be used to pre-filter the data before each match attempt. You can enter many, in text form each expression on a new line, or directly in the array.

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
**- replace_field_after** - `array` A list of regular expressions to replace an already matched field with another text string. It can be used for post-processing of already matched fields. You can enter many, in text form each expression on a new line, or directly in the array.

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
**- replace_block_before** - `array` A list of regular expressions to replace or pre-prepare a data block with another, run over the entire data block before trying to match. Can be used to pre-filter the data before each match attempt. You can enter many, in text form each expression on a new line, or directly in the array.
  
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
**- replace_block_after** - `array` A list of regular expressions to replace an already matched block with another text string. Run for the entire block of data after a match is made. Can be used for post-processing of already matched data. You can enter many, in text form each expression on a new line, or directly in the array.

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
**- fields** - `array` A list of fields to be matched, enter here the names of the fields into which you want to divide the parsed data, e.g. id, name, actor, description. It should be entered in one line, separated by a comma (,), or as an array of fields.

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
**- output_fields** - `array` A list of fields to match from the list above to be displayed in the output, e.g. id, name, actor. It should be entered in one line, separated by a comma (,), or as an array of fields.

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
**- sep_input_rowset** - `string` Separator for rowsets for input when stripping into rowset, used depending on the expected output distribution, e.g. \n

**- sep_input_row** - `string` Row separator for input when stripping into rows, used depending on the expected output distribution, e.g. \n

**- sep_input_column** - `string` Separator for columns for input data when stripping into columns, used depending on the expected output distribution, e.g. coma (,)


**- sep_output_rowset** - `string` Separator for rowsets for output when joining result from rowsets, used depending on the expected output look, e.g. \n

**- sep_output_row** - `string` Row separator for output when joining result from rows, used depending on the expected output look, e.g. \n

**- sep_output_column** - `string` Separator for columns for output data when joining result from columns, used depending on the expected output look, e.g. coma (,)

**- empty_field_placeholder** - `string` Placeholder to replace the given field if the matched field is empty. Leave blank if you don't want to put any placeholders.

**- is_debug** - `boolean`, If TRUE, then append debugger information to each line of output

**- is_empty_field_placeholder** - `boolean` If TRUE, then replaces empty spaces in the matched fields with the string specified in the `empty_placeholder` option

___

## Tool: cleaner

A tool for cleaning the input data, sanitizing and pre-preparing data for further processing

**Options:**

**- use_dataset** - `boolean`, Enables operation on a dataset prepared by the previous element, instead of its parsed output, allows to transfer between elements an already ready set of prepared data. Don't use in first element of chain when starting from raw input (because at the beginning there is no prepared dataset from the previous element)

**- trim** - `boolean` Applies function trim() to every block

**- clean_blocks** - `boolean` Removes empty blocks

**- fix_newlines** - `boolean` Replaces all \r\n with \n

**- strip_tags** - `boolean` Applies function strip_tags() to all

**- sep_input_rowset** - `string` Separator for rowsets for input when stripping into rowset, used depending on the expected output distribution, e.g. \n

**- sep_input_row** - `string` Row separator for input when stripping into rows, used depending on the expected output distribution, e.g. \n

**- sep_input_column** - `string` Separator for columns for input data when stripping into columns, used depending on the expected output distribution, e.g. coma (,)


**- sep_output_rowset** - `string` Separator for rowsets for output when joining result from rowsets, used depending on the expected output look, e.g. \n

**- sep_output_row** - `string` Row separator for output when joining result from rows, used depending on the expected output look, e.g. \n

**- sep_output_column** - `string` Separator for columns for output data when joining result from columns, used depending on the expected output look, e.g. coma (,)

___


## Tool: limiter

A tool for limiting the amount of generated or received data according to specific patterns and rules, it can be used to delete data also.

**Options:**

**- use_dataset** - `boolean` Enables operation on a dataset prepared by the previous element, instead of its parsed output, allows to transfer between elements an already ready set of prepared data. Don't use in first element of chain when starting from raw input (because at the beginning there is no prepared dataset from the previous element)

**- data_mode** - `string` `[rowset|row|column]` - Selects the dimension on which the other options are to operate

**- interval_allow** - `integer` Restricts output to blocks that match the given interval, default: 1

**- range_allow** - `array` Limits output to blocks that match specified ranges, leave empty to allow all blocks, or specify range(s) separated by coma, indexing is from 0

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
**- regex_allow** - `array` Restricts output to blocks that match the given regular expressions. You can enter a lot in new lines.

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
**- interval_deny_** - `integer` Restricts output to blocks that do not match the given interval, default: 1
**- range_deny_** - `array`, Limits blocks in output to blocks that do not match specified ranges, leave empty to allow all blocks, or specify range(s) separated by coma, indexing is from 0

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
**- regex_deny** - `array` Restricts output to blocks that do not match the given regular expressions. You can enter a lot in new lines.

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
**- sep_input_rowset** - `string` Separator for rowsets for input when stripping into rowset, used depending on the expected output distribution, e.g. \n

**- sep_input_row** - `string` Row separator for input when stripping into rows, used depending on the expected output distribution, e.g. \n

**- sep_input_column** - `string` Separator for columns for input data when stripping into columns, used depending on the expected output distribution, e.g. coma (,)


**- sep_output_rowset** - `string` Separator for rowsets for output when joining result from rowsets, used depending on the expected output look, e.g. \n

**- sep_output_row** - `string` Row separator for output when joining result from rows, used depending on the expected output look, e.g. \n

**- sep_output_column** - `string` Separator for columns for output data when joining result from columns, used depending on the expected output look, e.g. coma (,)
___

## Tool: replacer

A tool for converting specific batches of data to others according to specific patterns and rules.

**Options:**

**- use_dataset** - `boolean` Enables operation on a dataset prepared by the previous element, instead of its parsed output, allows to transfer between elements an already ready set of prepared data. Don't use in first element of chain when starting from raw input (because at the beginning there is no prepared dataset from the previous element) 

**- data_mode** - `string` `[rowset|row|column]` Selects the dimension on which the other options are to operate

**- regex** - `array` Regular expressions to replace the appropriate strings. You can enter several patterns on new lines.

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

**- interval** - `integer` Limits replacing only to a specific interval, default: 1

**- range** - `array` Limits blocks to replace to specified ranges, leave empty to replace all, or specify range(s) separated by coma, indexing is from 0

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

**- sep_input_rowset** - `string` Separator for rowsets for input when stripping into rowset, used depending on the expected output distribution, e.g. \n

**- sep_input_row** - `string` Row separator for input when stripping into rows, used depending on the expected output distribution, e.g. \n

**- sep_input_column** - `string` Separator for columns for input data when stripping into columns, used depending on the expected output distribution, e.g. coma (,)


**- sep_output_rowset** - `string` Separator for rowsets for output when joining result from rowsets, used depending on the expected output look, e.g. \n

**- sep_output_row** - `string` Row separator for output when joining result from rows, used depending on the expected output look, e.g. \n

**- sep_output_column** - `string` Separator for columns for output data when joining result from columns, used depending on the expected output look, e.g. coma (,)
___

# Running in command line (PHP CLI):

Package includes symfony command in `Command` directory.
You can run the command with the script cmd.php:

**Usage in CLI:**

  ```
  ./cmd.php chainparser /path/to/data /path/to/config.yaml [--options]
  ```

**Arguments:**

  /path/to/data = path to file with text data to parse.
  
  /path/to/config.yaml = path to file with Yaml config.

**Options:**

  --log=0   - disable log output

  --data=0  - disable raw data output

Package contains 2 example files:

  - example.txt

  - example.yaml

You can use this example files for test:

  ```
  ./cmd.php chainparser ./example.txt ./example.yaml
  ```

Disable log and data output (it leaves only parse result output):

  ```
  ./cmd.php chainparser ./example.txt ./example.yaml --log=0 --data=0
  ```

Store output result to file (output.txt):

  ```
  ./cmd.php chainparser ./example.txt ./example.yaml --log=0 --data=0 > output.txt
  ```


# Config options

You can use included config provider or write your own.
At this moment, 2 config providers are included:

**ArrayConfig** - works on the configuration provided directly from PHP array

**YamlConfig** - works on the configuration readed from the YAML file

### Usage:
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

### Available configuration options:

**- full_output** -- `boolean`, default: false, if true then all outputs from all chain elements are rendered at output, if false then only last result is rendered

**- log_file** -- `string`, absolute path to logfile if using PsrLogger (Monolog)


**- no_log** -- bool, if true then disables logging


# Loggers

You can use included Loggers or write your own.
The package includes 3 different Loggers:

**ArrayLogger**  -- logs everything to PHP array

**ConsoleLogger** -- logs output to CLI console

**PsrLogger** -- stores logs in file using Monolog Logger


### Usage:
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

You can register multiple Loggers at once, the output will be sent to all of them at once.

Accessing the generated logs is very simple:

```php  
  ...
$parser->run();

$log = $parser->renderLog();

dump($log);
```
Or if you want get raw (not rendered/parsed) log data, use:
```php
$parser->run();

$output = $parser->getOutput();
$log = $output->get('logs');
dump($log);
```

___

# Extending Chain Parser:

The concept is based on fully modularity and extending the package with its own parsers and features.
Each element can be adapted to your needs and to solve your problems.


## Configuration providers

Configuration providers are responsible for reading and parsing the configuration.
There are a few base configuration providers included in the package, but you can create yours very easily.

Configuration providers are located in: `Szczyglis\ChainParser\Config` namespace.

### Included configuration providers:

**ArrayConfig** - reads configuration from array passed as constructor argument

**YamlConfig** - reads configuration from Yaml file (path to file needs to be passed as constructor argument)

### How to create your own configuration provider:

- implement interface `Szczyglis\ChainParser\Contract\ConfigInterface`
- attach your config provider using the `setConfig()` method


### Example:

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

Input data providers are responsible for reading input data.
There are a few base input data providers included in the package, but you can create yours very easily.

Input data providers are located in: `Szczyglis\ChainParser\Input` namespace.

### Included input data providers:

**TextInput** - reads input data directly from string passed as constructor argument

**FileInput** - reads input data from file (path to file needs to be passed in constructor argument)

### How to create your own input data provider:

- implement interface `Szczyglis\ChainParser\Contract\InputInterface`
- optionally extend abstract helper class `Szczyglis\ChainParser\Helper\AbstractInput`
- attach your input data provider using the `setInput()` method


### Example:

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

Loggers are responsible for logging data from plugins.
There are a few base loggers included in the package, but you can create yours very easily.

Loggers are located in: `Szczyglis\ChainParser\Logger` namespace.

### Included loggers:

**ArrayLogger** - writes logs directly to the array

**PsrLogger** - writes logs into file using Monolog

**ConsoleLogger** - displays logs directly to the console

### How to create your own logger:

- implement interface `Szczyglis\ChainParser\Contract\LoggerInterface`
- optionally extend abstract helper class `Szczyglis\ChainParser\Helper\AbstractLogger`
- add logger using the `addLogger()` method


### Example:

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

Options providers are responsible for reading, parsing and serving plugin's options.
There are a few base options providers included in the package, but you can create yours very easily.

Options provider are located in: `Szczyglis\ChainParser\Options` namespace.

### Included options providers:

**ArrayOptions** - reads options directly from array passed as constructor argument

**FormOptions** - parses options passed as text or from html form

### How to create your own options provider:

- implement interface `Szczyglis\ChainParser\Contract\OptionsInterface`
- optionally extend abstract helper class `Szczyglis\ChainParser\Helper\AbstractOptions`
- initialize plugin using your own options provider


### Example:

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

Option resolvers are responsible for parsing options, e.g. when options are given using a HTML form and need to be parsed into an array. There are a few base option resolvers included in the package, but you can create yours very easily.

Option resolvers are located in: `Szczyglis\ChainParser\OptionResolver` namespace.

### Included options resolvers:

  **SingleLineResolver** - parses options from single line

  **MultiLineResolver** - parses more complex syntax from multilines

  **RangeResolver** - parses range-based options, eg. 1,2,8-10

### How to create your own option resolver, e.g. for use with own plugin configuration:

- implement interface `Szczyglis\ChainParser\Contract\OptionResolverInterface`
- register option resolver using `addResolver()` method


### Example:

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

Plugins are the core of the application.
They are tools that run along the chain and operates on data.
Each plugin can work with the raw input and output from the previous plugin made in the chain.
There are also special classes called `Workers` that help you organize your code and break it down into different "subprocesses". Workers share the same data set as the main plugin, they can also quickly exchange their data with the plugin. You can easily register your own workers using the `registerWorker` method.

Plugins are located in `Szczyglis\ChainParser\Plugin` namespace.

### Included plugins:

**Parser** - The main tool of the application, used to parse data according to specific patterns and rules.

**Cleaner** - A tool for cleaning the input data, sanitizing and pre-preparing data for further processing

**Limiter** - A tool for limiting and removing the amount of generated or received data according to specific patterns and rules.

**Replacer** - A tool for converting specific batches of data to others according to specific patterns and rules.

### How to create your own plugin:

- implement interface `Szczyglis\ChainParser\Contract\PluginInterface`
- extend abstract helper class `Szczyglis\ChainParser\Helper\AbstractPlugin`
- add a plugin to the chain


### Example:
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
    $data = $this->getPrev('output'); // gets output from the previous element in chain
    $data = 'Hello '.$data;
    $this->set('output', $data); // returns the output data and passes it to the next element in the chain

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

### Creating own Worker:
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
  ---
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

Worker is registered and initiated automatically as soon as it is given in the `registerWorkers()` method. As the example above showed - Workers and Plugins have a common container for temporary data used to exchange variables between the Plugin and the Worker:

```php
$this->setVar('foo', $bar); // sets var foo 
$foo = $this->getVar('foo'); // gets var foo
```


### Access to input and output data from the plug-in and worker level is as follows:

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

You can use some included helpers providing by AbstractPlugin and AbstractWorker abstract classes. 
When you extend your class from them then you have access to some useful methods:


`$this->isPattern($pattern): bool` -- checks if $pattern is valid regex pattern

`$this->checkPatterns(array $patterns, string $string): bool` -- checks if at least one of the given regex patterns matches a string

`$this->applyPatterns(array $patterns, string $string): string` -- applies any replacement patterns to the given string

`$this->explode(string $separator, ?string $input): array` -- wrapper for explode(), allow exploding by regular expression as separator

`$this->implode(string $joiner, array &$ary): string` -- wrapper for implode()

`$this->strReplace($from, $to, $data): string` -- wrapper for str_replace()

`$this->stripTags($data, $tags = null): string` -- wrapper for strip_tags()

`$this->trim($input): string` -- wrapper for trim()

`$this->inRange(array $ranges, int $i): bool` -- checks if number matches given ranges

`$this->makeDataset(?string $input, string $sepRowset, string $sepRow, string $sepCol): array` -- makes 3-dimensional dataset array from string input and given separators

`$this->packDataset(array $dataset, string $sepRowset, string $sepRow, string $sepCol): string` -- builds parsed result from dataset

`$this->iterateDataset(array $dataset, callable $callback): array` -- applies callback on every block in dataset and returns modified by callback dataset, example:

```php

  $dataset = $this->iterateDataset($dataset, function($value) {
      return str_replace('A', 'B', $value);
  });
```
Doing this will cause the character A to be replaced with the character B for each element in the dataset.


**It it good idea to check code of plugins included in the package to see live examples.**


### Registering options that require prior parsing:

If you need to use options that require the data to be parsed first, e.g. into an `array`, use ready-made Option Resolvers, or create your own Resolver. To register an option in the appropriate Option Resolvers, return its name in the array using the registerOptions() method. From now on, it will be parsed by the assigned Resolver. The array should include the name of the Resolver and a list of options assigned to it. Example of use:


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

Built-in resolvers:

**singleline** - parses single line parameters, like "key: value => assignement/replacement"

**multiline** - parses multiline line parameters, like "key: value => assignement/replacement places in many lines"

**range** - parses range parameters, like: "1,5,10-20,15-,-80"


### Logging messages:

From the Plugin and Worker level, you have access to log events using registered loggers. The `$this->log()` method is used for this. To use event logging, you must implement the following interfaces:

- in plugin: `Szczyglis\ChainParser\Contract\LoggableInterface`;
- in worker: `Szczyglis\ChainParser\Contract\LoggableWorkerInterface`;

#### Example of use:

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

Renderers are responsible for displaying the output.
There are a few base renderers included in the package, but you can create yours very easily.

Renderers are located in `Szczyglis\ChainParser\Renderer` namespace.

### Included renderers:

**TextRenderer** - parses output into txt/html format

**ConsoleRenderer** - sends output to console

### How to create your own renderer:

- implement interface `Szczyglis\ChainParser\Contract\RendererInterface`
- optionally extend abstract helper class `Szczyglis\ChainParser\Helper\AbstractRenderer`
- set renderer using `setRenderer()` method


### Example:

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

You can export the currently running configuration at any time, including the entire chain and its options.
To do this, use the config generator:

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
**- 1.0.0** - Published first release. (2022-04-22)

**- 1.0.4** - Increased limit if demo mode, documentation fixes. (2022-04-22)

**- 1.2.5** - Full dataset sharing added, eraser and splitter plugins are removed (their role is taken over by limiter), added configuration of dataset looks by freely specifying each separator for each dimension (rowset, row, column).  (2022-04-23)

# Credits
 
### Ultimate Chain Parser is free to use but if you liked then you can donate project via BTC: 

**14X6zSCbkU5wojcXZMgT9a4EnJNcieTrcr**

or by PayPal:
 **[https://www.paypal.me/szczyglinski](https://www.paypal.me/szczyglinski)**


**Enjoy!**

MIT License | 2022 Marcin 'szczyglis' Szczygliński

https://github.com/szczyglis-dev/ultimate-chain-parser

Contact: szczyglis@protonmail.com
