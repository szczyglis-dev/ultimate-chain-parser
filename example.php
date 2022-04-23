<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Szczyglis\ChainParser\ChainParser;
use Szczyglis\ChainParser\Input\TextInput;
use Szczyglis\ChainParser\Options\FormOptions;
use Szczyglis\ChainParser\Config\ArrayConfig;
use Szczyglis\ChainParser\Core\ConfigGenerator;
use Szczyglis\ChainParser\Core\Html;

$input = '';
$result = '';
$log = '';
$chain = [];
$request = Request::createFromGlobals();
$html = new Html();
$parser = new ChainParser;
$isAll = false;
$isValid = true;
$ignoredOptions = ['is_active', 'is_hidden', 'plugin_name'];
$isNormalSubmit = false;

if (!$html->isDemoMode()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

session_start();

try {

    if ($request->isMethod('POST') && $request->request->has('form')) {
        // check token
        if (!$request->request->has('_token') || !$html->isTokenValid($request->request->get('_token'))) {
            echo 'Invalid token.';
            exit;
        }
        if ($html->isDemoMode() && !$request->isXmlHttpRequest()) {
            echo 'Only ajax submit is allowed.';
            exit;
        }

        $form = $request->request->all('form'); // get form 

        if (isset($form['is_output_all']) && $form['is_output_all'] == 1) {
            $isAll = true;
        }

        // get elements and options from HTML form
        if (isset($form['plugin_name']) && !empty($form['plugin_name'])) {

            // check if element is active
            foreach ($form['plugin_name'] as $i => $name) {
                if (!isset($form['is_active'][$i]) || $form['is_active'][$i] != 1) {
                    continue;
                }

                // get options
                $options = [];
                foreach ($form as $key => $values) {
                    if (!is_array($values)) {
                        continue;
                    }
                    foreach ($values as $j => $value) {
                        if (in_array($key, $ignoredOptions)) {
                            continue;
                        }
                        if ($j === $i) {
                            if ($html->isDemoMode()) {
                                $value = substr($value, 0, $html->getOptionLimit());
                            }
                            $options[$key] = $value;
                        }
                    }
                }
                // add element to chain
                $chain[] = [
                    'name' => $name,
                    'options' => $options,
                ];
            }

            foreach ($chain as $i => $element) {
                if ($html->isDemoMode()) {
                    if ($i > $html->getChainLengthLimit()) {
                        break;
                    }
                }
                $parser->add($element['name'], new FormOptions($element['options'])); // register new chain element
            }
        }

        // set config
        $parser->setConfig(new ArrayConfig([
            'full_output' => $isAll,
        ]));

        $input = (string)$form['input'];
        if ($html->isDemoMode()) {
            $input = substr($input, 0, $html->getInputLimit());
        }

        // set input
        $parser->setInput(new TextInput($input));

        // run
        $parser->run(); // execute

        // render output result
        $result = $parser->renderOutput([
            'delimiter' => "\n",
        ]);

        // render output data
        $data = $parser->renderData([
            'delimiter' => "\n",
        ]);

        // render output log
        $log = $parser->renderLog([
            'delimiter' => "\n",
        ]);

        // build current settings configuration for future use
        $config = (new ConfigGenerator())->build($parser, 'yaml');

        // return results via JSON response if AJAX call
        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse([
                'result' => true,
                'output' => $result,
                'data' => $data,
                'config' => $config,
                'debug' => $log,
                'error' => null,
            ]);
            $response->send();
            exit;
        } else {
            $isNormalSubmit = true;
            dump($chain, $result, $log, $parser); // dump all if not AJAX call
        }
    }

} catch (\Throwable $e) {
    if ($request->isXmlHttpRequest()) {
        if ($html->isDemoMode()) {
            echo 'ERROR: Invalid options or/and incorect input. Please try again.';
        } else {
            echo 'ERROR: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
        }
        exit;
    } else {
        dump($e);
    }
}
if (!$html->isDemoMode() && $isNormalSubmit) {
    echo '<b>OUTPUT:</b><pre>' . $result . '</pre><hr/>';
    echo '<b>LOG:</b><pre>' . $log . '</pre>';
    dump($parser);
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ultimate Chain Parser, v.<?php echo $html->getVersion(); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css"
          integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <style>
        body {
            font-family: monospace;
            font-size: 0.9rem;
            background: #fdfdfd;
        }

        .form-control {
            font-size: 0.8rem;
        }

        .iteration-color {
            width: 20px;
            height: 20px;
            background: red;
            display: inline-block;
        }

        .data-window {
            font-size: 0.8rem;
        }

        .textarea-debug,
        .form-control.textarea-debug,
        .textarea-debug:focus {
            background: #fdfdfd;
            font-size: 0.7rem;
            font-family: monospace;
            height: 140px;
        }

        .help-syntax {
            font-family: monospace;
            font-weight: bold;
            color: #dc3545;
            font-size: 0.7rem;
            display: block;
        }

        .help-example {
            font-family: monospace;
            font-size: 0.7rem;
            display: block;
        }

        .k {
            font-weight: bold;
        }

        .o {
            font-weight: bold;
            padding-right: 0.25rem;
            font-size: 0.85rem;
        }

        .ot {
            border-top: 1px solid #f7f6f6;
            padding-top: 0.5rem;
        }

        .t a {
            color: #000;
        }

        .f {
            color: gray;
            font-size: 0.8rem;
            text-align: center;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .d {
            color: #dc3545 !important;
        }

        .exe {
            display: block;
            width: 100%;
            height: 60px;
        }

        .con {
            font-family: monospace;
            font-size: 0.7rem;
        }

        .add-element-container {
            background: #e4ffc4;
        }

        #result {
            font-weight: bold;
        }

        #tool-select {
            font-size: 1rem;
        }

        .github-corner {
            position: fixed;
            top: 0;
            right: 0;
            z-index: 9999;
        }

        .github-corner:hover .octo-arm {
            animation: octocat-wave 560ms ease-in-out;
        }

        @keyframes octocat-wave {
            0% {
                transform: rotate(0deg);
            }

            20% {
                transform: rotate(-25deg);
            }

            40% {
                transform: rotate(10deg);
            }

            60% {
                transform: rotate(-25deg);
            }

            80% {
                transform: rotate(10deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        @media (max-width: 500px) {
            .github-corner:hover .octo-arm {
                animation: none;
            }

            .github-corner .octo-arm {
                animation: octocat-wave 560ms ease-in-out;
            }
        }
    </style>
    <link href="data:image/x-icon;base64,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"
          rel="icon" type="image/x-icon"/>
    <meta name="description"
          content="Ultimate Chain Parser - free, open source, advanced text data parser working in chain mode, writted in PHP, included tools: parser, replacer, cleaner, limiter, eraser, parser"/>
    <meta name="robots" content="index, follow"/>
    <meta name="og:site_name" content="Ultimate Chain Parser"/>
    <meta name="og:type" content="website"/>
    <meta name="og:title" content="Ultimate Chain Parser"/>
    <meta name="og:image" content="https://gdziebunkier.pl/icon.png"/>
    <meta name="og:description"
          content="Ultimate Chain Parser - free, open source, advanced text data parser working in chain mode, writted in PHP, included tools: parser, replacer, cleaner, limiter, eraser, parser"/>
    <meta name="og:url" content="https://szczyglis.dev/apps/ultimate-chain-parser"/>
</head>
<body>
<div class="version">
    <div class="demo version-section"><a target="_blank" href="<?php echo $html->getGitHubUrl(); ?>"
                                         class="github-corner" aria-label="View source on GitHub">
            <svg width="80" height="80" viewBox="0 0 250 250"
                 style="fill:#151513; color:#fff; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true">
                <path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path>
                <path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2"
                      fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path>
                <path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z"
                      fill="currentColor" class="octo-body"></path>
            </svg>
        </a>
    </div>
</div>
<div class="container-fluid">
    <div class="wrapper p-5">
        <div class="row">
            <div class="col-md-8">
                <h2 class="t"><img
                            src="data:image/x-icon;base64,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"/>

                    <a href="">Ultimate Chain Parser</a>
                    v.<?php echo $html->getVersion(); ?> <?php if ($html->isDemoMode()) {
                        echo '<span class="d">LIVE DEMO</span>';
                    } ?></h2>
                Documentation and newest releases: <a href="<?php echo $html->getGitHubUrl(); ?>"
                                                      target="_blank"><?php echo $html->getGitHubUrl(); ?></a>
            </div>
            <div class="col-md-4 text-right con d-none d-lg-block">
                <h3>INPUT/OUTPUT CONSOLE</h3>
            </div>
        </div>

        <div class="row">
            <div class="col mt-1">
                <textarea name="debug" class="form-control textarea-debug"
                          spellcheck="false">Logger / debugger ...</textarea>
            </div>
        </div>

        <form action="example.php" method="POST" name="form" spellcheck="false">

            <div class="row mt-3">
                <div class="col-12 col-lg-10 form-inline">
                    <span class="hidden-md-down d-none d-lg-block">
                        <b>SWITCH VIEW:</b>
                        <a class="btn btn-primary btn-sm view-switch" data-view="2:10">2:10</a>
                        <a class="btn btn-primary btn-sm view-switch" data-view="10:2">10:2</a>
                        <a class="btn btn-primary btn-sm view-switch" data-view="6-6">6:6</a>
                        <a class="btn btn-primary btn-sm view-switch" data-view="4:8">4:8</a>
                        <a class="btn btn-primary btn-sm view-switch" data-view="8:4">8:4</a>
                        <a class="btn btn-primary btn-sm view-switch" data-view="12:12">12:12</a>
                    </span>
                    <?php
                    if (!$html->isDemoMode()) {
                        echo '<div class="form-check ml-3">
                                <input name="is_ajax" class="form-check-input" type="checkbox" value="1" checked />
                                <label class="form-check-label">
                                    Ajax submit
                                </label>
                            </div>';
                    } else {
                        echo '<input name="is_ajax" type="hidden" value="1" />';
                    }
                    ?>
                    <div class="form-check ml-3">
                        <input name="form[is_output_all]" class="form-check-input" type="checkbox" value="1">
                        <label class="form-check-label">
                            All outputs
                        </label>
                    </div>
                </div>
                <div class="col-12 col-lg-2 text-right">
                    <button type="button" class="btn btn-secondary btn-sm btn-clear">CLEAR FORM</button>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6" id="data-input">
                    <div class="form-group mt-2">
                        <label class="font-weight-bold">INPUT<?php if ($html->isDemoMode()) {
                                echo '<small class="ml-2 text-danger">* limited to ' . $html->getInputLimit() . ' characters in Live Demo mode.</small>';
                            } ?></label>
                        <textarea name="form[input]" class="form-control data-window" spellcheck="false"
                                  rows="10"<?php if ($html->isDemoMode()) {
                            echo ' maxlength="' . $html->getInputLimit() . '"';
                        } ?>>
123
terminator
schwarzenegger

action movie

very good


456
titanic

dicaprio


same director

                  

                  </textarea>
                    </div>
                    <div id="panel-config">
                        <div class="card">
                            <div class="card-header text-center" id="headingOne">
                                <h5 class="mb-0">
                                    <button type="button" class="btn btn-link" data-toggle="collapse"
                                            data-target="#collapseConfig" aria-expanded="false"
                                            aria-controls="collapseConfig">
                                        CONFIG DUMP (yaml)
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseConfig" class="collapse" aria-labelledby="headingOne"
                                 data-parent="#panel-config">
                                <div class="card-body">
                                    <button class="btn btn-secondary btn-sm btn-select-config" type="button">SELECT
                                        ALL
                                    </button>
                                    <textarea name="output_config" class="form-control" spellcheck="false"
                                              rows="10"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" id="data-output">
                    <div class="form-group mt-2">
                        <label class="font-weight-bold">OUTPUT</label>
                        <textarea name="output_render" class="form-control data-window" spellcheck="false"
                                  rows="10"></textarea>
                    </div>
                    <div id="panel-data">
                        <div class="card">
                            <div class="card-header text-center" id="headingOne">
                                <h5 class="mb-0">
                                    <button type="button" class="btn btn-link" data-toggle="collapse"
                                            data-target="#collapseData" aria-expanded="false"
                                            aria-controls="collapseData">
                                        OUTPUT DATASET (json)
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseData" class="collapse" aria-labelledby="headingOne"
                                 data-parent="#panel-data">
                                <div class="card-body">
                                    <button class="btn btn-secondary btn-sm btn-select-data" type="button">SELECT ALL
                                    </button>
                                    <textarea name="output_data" class="form-control" spellcheck="false"
                                              rows="10"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row my-3">
                <div class="col text-center">
                    <button type="submit" class="btn btn-primary exe">EXECUTE!</button>
                    <div id="result" class="mt-4"></div>
                </div>
            </div>
            <div class="row">
                <div class="col font-weight-bold">
                    Execution chain:
                </div>
            </div>

            <div class="row">
                <div class="col my-3" id="chain-container"></div>
            </div>

            <div class="row my-3 border-top pt-2 add-element-container">
                <div class="col">
                    <div class="form-row">
                        <div class="pt-1">
                            <b>[+] Add next element to execution chain:</b>
                        </div>
                        <div class="pt-1 pl-2">
                            <small class="form-text text-muted">
                                ( select plugin from list and click ADD )
                            </small>
                        </div>
                    </div>
                    <div class="form-row mt-1">
                        <div class="form-group pt-2 pr-2">
                            <b>PLUGIN </b>
                        </div>
                        <div class="form-group">
                            <select class="form-control" id="tool-select">
                                <option value="parser" selected>parser</option>
                                <option value="cleaner">cleaner</option>
                                <option value="limiter">limiter</option>
                                <option value="replacer">replacer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-add-element">ADD</button>
                        </div>
                    </div>
                    <?php if ($html->isDemoMode()) {
                        echo '<div class="form-row mb-1"><small class="text-danger">* limit of ' . $html->getChainLengthLimit() . ' elements in chain in Live Demo mode.</small></div>';
                    } ?>
                </div>
            </div>
            <input type="hidden" name="_token" value="<?php echo $html->getCsrfToken(); ?>"/>
        </form>
    </div>
    <footer class="footer">
        <div class="row">
            <div class="col f">
                © <?php echo date('Y'); ?> Ultimate Chain Parser v.<?php echo $html->getVersion(); ?> | by Marcin "szczyglis"
                Szczygliński<br/>
                <a href="<?php echo $html->getGitHubUrl(); ?>"
                   target="_blank"><?php echo $html->getGitHubUrl(); ?></a><br/>
                <a href="<?php echo $html->getWebpagebUrl(); ?>"
                   target="_blank"><?php echo $html->getWebpagebUrl(); ?></a>
            </div>
        </div>
    </footer>
</div>

<div class="row plugin-container d-none border-top pt-2" data-plugin="parser" data-index="" data-initialized="0"
     data-expanded="1">
    <div class="col">
        <?php echo $html->controls(); ?>
        <div class="row plugin-body">
            <div class="col">
                <div class="row">
                    <div class="col-lg-4">
                        <?php echo $html->option('parser', 'regex_match'); ?>
                        <?php echo $html->option('parser', 'replace_field_before'); ?>
                        <?php echo $html->option('parser', 'replace_field_after'); ?>
                        <?php echo $html->option('parser', 'empty_field_placeholder'); ?>
                        <?php echo $html->option('parser', 'is_empty_field_placeholder'); ?>
                        <?php echo $html->option('parser', 'is_debug'); ?>
                    </div>
                    <div class="col-lg-4">
                        <?php echo $html->option('parser', 'regex_ignore_before'); ?>
                        <?php echo $html->option('parser', 'regex_ignore_after'); ?>
                        <?php echo $html->option('parser', 'replace_block_before'); ?>
                        <?php echo $html->option('parser', 'replace_block_after'); ?>
                    </div>
                    <div class="col-lg-4">
                        <?php echo $html->option('parser', 'fields'); ?>
                        <?php echo $html->option('parser', 'output_fields'); ?>
                    </div>
                </div>
                <div class="row">
                    <?php echo $html->options('parser', 'io'); ?>                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row plugin-container d-none border-top pt-2" data-plugin="cleaner" data-index="" data-initialized="0"
     data-expanded="1">
    <div class="col">
        <?php echo $html->controls(); ?>
        <div class="row plugin-body">
            <div class="col-md-6">
                <?php echo $html->option('cleaner', 'trim'); ?>
                <?php echo $html->option('cleaner', 'clean_blocks'); ?>                
            </div>
            <div class="col-md-6">
                <?php echo $html->option('cleaner', 'fix_newlines'); ?>
                <?php echo $html->option('cleaner', 'strip_tags'); ?>
            </div>
            <?php echo $html->options('cleaner', 'io'); ?>
        </div>
    </div>
</div>

<div class="row plugin-container d-none border-top pt-2" data-plugin="limiter" data-index="" data-initialized="0"
     data-expanded="1">
    <div class="col">
        <?php echo $html->controls(); ?>
        <div class="row plugin-body">
            <div class="col-md-6">
                <?php echo $html->option('limiter', 'interval_allow'); ?>
                <?php echo $html->option('limiter', 'range_allow'); ?>
                <?php echo $html->option('limiter', 'regex_allow'); ?>
            </div>
            <div class="col-md-6">
                <?php echo $html->option('limiter', 'interval_deny'); ?>
                <?php echo $html->option('limiter', 'range_deny'); ?>
                <?php echo $html->option('limiter', 'regex_deny'); ?>
            </div>
            <div class="col-12">
                <?php echo $html->options('limiter', 'io'); ?>
                <?php echo $html->option('limiter', 'data_mode'); ?>
            </div>
        </div>
    </div>
</div>

<div class="row plugin-container d-none border-top pt-2" data-plugin="replacer" data-index="" data-initialized="0"
     data-expanded="1">
    <div class="col">
        <?php echo $html->controls(); ?>
        <div class="row plugin-body">
            <div class="col-md-6">
                <?php echo $html->option('replacer', 'regex'); ?>
            </div>
            <div class="col-md-6">
                <?php echo $html->option('replacer', 'interval'); ?>
                <?php echo $html->option('replacer', 'range'); ?>
            </div>
            <div class="col-12">
                <?php echo $html->options('replacer', 'io'); ?>
                <?php echo $html->option('replacer', 'data_mode'); ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF"
        crossorigin="anonymous"></script>

<script>
    const templates = {
        'parser': 'parser',
        'cleaner': 'cleaner',
        'limiter': 'limiter',
        'replacer': 'replacer',
    };

    function restore(json) {
        const data = JSON.parse(json);
        for (const k in data.plugins) {
            addElement(data.plugins[k]);
        }
        for (const k in data.options) {
            $('input[type="text"][name="form[' + data.options[k]['k'] + '][' + data.options[k]['i'] + ']"]').val(data.options[k]['v']);
            $('input[type="hidden"][name="form[' + data.options[k]['k'] + '][' + data.options[k]['i'] + ']"]').val(data.options[k]['v']);
            $('textarea[name="form[' + data.options[k]['k'] + '][' + data.options[k]['i'] + ']"]').val(data.options[k]['v']);
            $('input[type="checkbox"][name="form[' + data.options[k]['k'] + '][' + data.options[k]['i'] + ']"]').prop('checked', true);
            $('input[type="radio"][name="form[' + data.options[k]['k'] + '][' + data.options[k]['i'] + ']"][value="'+data.options[k]['v']+'"]').prop('checked', true);
            addElement(data.options[k]);
        }
        for (const k in data.plugins) {
            if (typeof data.active[k] !== 'undefined') {
                $('input[type="checkbox"][name="form[is_active][' + k + ']"]').prop('checked', true);
            } else {
                $('input[type="checkbox"][name="form[is_active][' + k + ']"]').prop('checked', false);
            }
            if (typeof data.hidden[k] !== 'undefined') {
                $('input[name="form[is_hidden][' + k + ']"]').val('1');
                $('.plugin-container[data-index="' + k + '"][data-initialized="1"] .plugin-body').hide();
                $('.plugin-container[data-index="' + k + '"][data-initialized="1"]').attr('data-expanded', '0');
                $('.plugin-container[data-index="' + k + '"][data-initialized="1"]').find('.btn-element-toggle').text('SHOW');
            }
        }
        if (data.is_ajax == true) {
            $('input[type="checkbox"][name="is_ajax"]').prop('checked', true);
        } else {
            $('input[type="checkbox"][name="is_ajax"]').prop('checked', false);
        }
        if (data.is_all == true) {
            $('input[type="checkbox"][name="form[is_output_all]"]').prop('checked', true);
        } else {
            $('input[type="checkbox"][name="form[is_output_all]"]').prop('checked', false);
        }
        if (data.input !== false) {
            $('textarea[name="form[input]"]').val(data.input);
        }
        console.log('Chain Parser JS Debug', data);
    }

    function scrollBottom(el) {
        if (el.length) {
            el.scrollTop(el[0].scrollHeight - el.height());
        }
    }

    function randomcolor() {
        const x = Math.floor(Math.random() * 256);
        const y = Math.floor(Math.random() * 256);
        const z = Math.floor(Math.random() * 256);
        return 'rgb(' + x + ',' + y + ',' + z + ')';
    }

    function moveUp(i) {
        const j = i - 1;
        const el1 = $('.plugin-container[data-initialized="1"][data-index="' + i + '"]');
        const el2 = $('.plugin-container[data-initialized="1"][data-index="' + j + '"]');
        $(el1).swapWith(el2);
        refreshIndexes();
        redrawControls();
    }

    function moveDown(i) {
        const j = i + 1;
        const el1 = $('.plugin-container[data-initialized="1"][data-index="' + i + '"]');
        const el2 = $('.plugin-container[data-initialized="1"][data-index="' + j + '"]');
        $(el1).swapWith(el2);
        refreshIndexes();
        redrawControls();
    }

    function refreshIndexes() {
        let i = 0;
        $('.plugin-container[data-initialized="1"]').each(function () {
            $(this).attr('data-index', i);
            $(this).find('.index-iteration').text(i);
            $(this).find('input').each(function () {
                const name = 'form[' + $(this).attr('data-option') + '][' + i + ']';
                $(this).attr('name', name);
            });
            $(this).find('textarea').each(function () {
                const name = 'form[' + $(this).attr('data-option') + '][' + i + ']';
                $(this).attr('name', name);
            });
            i++;
        });
    }

    function redrawControls() {
        let check, j;
        $('.plugin-container[data-initialized="1"]').each(function () {
            const i = parseInt($(this).attr('data-index'));
            const upBtn = $(this).find('.btn-element-up');
            const downBtn = $(this).find('.btn-element-down');
            j = i - 1;
            check = $('.plugin-container[data-initialized="1"][data-index="' + j + '"]');
            if (check.length) {
                upBtn.removeClass('d-none');
            } else {
                upBtn.addClass('d-none');
            }
            j = i + 1;
            check = $('.plugin-container[data-initialized="1"][data-index="' + j + '"]');
            if (check.length) {
                downBtn.removeClass('d-none');
            } else {
                downBtn.addClass('d-none');
            }
        });
    }

    function addElement(id) {
        const c = $('.plugin-container[data-initialized="1"]').length;
        <?php if ($html->isDemoMode()) {
        echo "if (c >= " . $html->getChainLengthLimit() . ") {
            alert(\"Limit of maximum " . $html->getChainLengthLimit() . " chain elements in Live Demo mode!\");
            return;
        }
        ";
    }?>
        const el = $('.plugin-container[data-plugin="' + templates[id] + '"][data-initialized="0"]').clone().attr('data-initialized', '1').removeClass('d-none');
        el.find('input[name="form[plugin_name][]"]').val(id);
        el.find('.plugin-name').text(id);
        el.find('.iteration-color').css('background', randomcolor());
        $('#chain-container').append(el);
        refreshIndexes();
        redrawControls();

        if (c == 0) {
            el.find('input[data-option="use_dataset"]').prop('checked', false);
        }
    }

    function removeElement(i) {
        const el = $('.plugin-container[data-index="' + i + '"][data-initialized="1"]');
        el.remove();
        refreshIndexes();
        redrawControls();
    }

    function parse() {
        const data = $('form[name="form"]').serialize();
        $.ajax({
            data: data,
            url: 'example.php',
            dataType: 'json',
            method: 'POST'
        })
            .done(function (data) {
                console.log(data);
                if (data.result == true) {
                    $('textarea[name="output_render"]').val(data.output);
                    $('textarea[name="output_data"]').val(data.data);
                    $('textarea[name="output_config"]').val(data.config);
                    $('#result').addClass('text-success').text('SUCCESS');
                } else {
                    $('#result').addClass('text-danger').text(data.error);
                    console.error(data.error);
                }
                $('textarea[name="debug"]').val(data.debug);
                scrollBottom($('textarea[name="debug"]'));
            })
            .fail(function (err) {
                $('#result').addClass('text-danger').text(err.responseText);
                console.error(err.responseText);
            })
            .always(function () {
                //
            });
    };
    $(document).ready(function () {
        $('body').on('click', '.btn-add-element', function (e) {
            e.preventDefault();
            const id = $('#tool-select').val();
            addElement(id);
        });
        $('body').on('click', '.btn-remove-element', function (e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to remove element?')) {
                return;
            }
            const i = $(this).closest('.plugin-container').attr('data-index');
            removeElement(i);
        });
        $('body').on('click', '.btn-element-up', function (e) {
            e.preventDefault();
            const i = parseInt($(this).closest('.plugin-container').attr('data-index'));
            moveUp(i);
        });
        $('body').on('click', '.btn-element-down', function (e) {
            e.preventDefault();
            const i = parseInt($(this).closest('.plugin-container').attr('data-index'));
            moveDown(i);
        });
        $('body').on('click', '.btn-element-toggle', function (e) {
            e.preventDefault();
            const parent = $(this).closest('.plugin-container');
            const expanded = parseInt(parent.attr('data-expanded'));
            if (expanded == 1) {
                parent.find('.plugin-body').slideUp();
                parent.find('input[data-option="is_hidden"]').val(1);
                parent.attr('data-expanded', 0);
                $(this).text('SHOW');
            } else {
                parent.find('.plugin-body').slideDown();
                parent.find('input[data-option="is_hidden"]').val(0);
                parent.attr('data-expanded', 1);
                $(this).text('HIDE');
            }
        });
        $('body').on('click', '.btn-clear', function (e) {
            e.preventDefault();
            $('form[name="form"] input[type="text"]').val('');
            $('form[name="form"] textarea').not('[name="form[input]"]').not('[name="form[output]"]').val('');
            $('.plugin-container input[type="text"]').val('');
            $('.plugin-container textarea').val('');
            $('.plugin-container input[type="checkbox"]').not('[data-option="is_active"]').prop('checked', false);
        });
        $('body').on('click', '.btn-select-config', function (e) {
            e.preventDefault();
            $('textarea[name="output_config"]').focus().select();
        });
        $('body').on('click', '.btn-select-data', function (e) {
            e.preventDefault();
            $('textarea[name="output_data"]').focus().select();
        });
        $('body').on('click', '.view-switch', function (e) {
            e.preventDefault();
            const v = $(this).attr('data-view');
            const input = $('#data-input');
            const output = $('#data-output');
            input.removeClass();
            output.removeClass();
            console.log(v);
            switch (v) {
                case '6-6':
                    input.addClass('col-md-6');
                    output.addClass('col-md-6');
                    break;
                case '4:8':
                    input.addClass('col-md-4');
                    output.addClass('col-md-8');
                    break;
                case '8:4':
                    input.addClass('col-md-8');
                    output.addClass('col-md-4');
                    break;
                case '2:10':
                    input.addClass('col-md-2');
                    output.addClass('col-md-10');
                    break;
                case '10:2':
                    input.addClass('col-md-10');
                    output.addClass('col-md-2');
                    break;
                case '12:12':
                    input.addClass('col-12');
                    output.addClass('col-12');
                    break;
            }
        });

        var json = <?php echo $html->restore();?>;
        restore(json);
    });

    $('body').on('submit', 'form[name="form"]', function (e) {
        <?php if (!$html->isDemoMode()) { ?>
        if (!$('input[name="is_ajax"]').is(':checked')) {
            return;
        }
        <?php } ?>
        e.preventDefault();
        $('#result').text('Parsing...').removeClass('text-success').removeClass('text-danger');
        parse();
    });

    $.fn.swapWith = function (that) {
        let $this = this;
        let $that = $(that);
        let $temp = $('<div>');
        $this.before($temp);
        $that.before($this);
        $temp.before($that).remove();
        return $this;
    }

    window.onbeforeunload = function () {
        <?php if ($html->isDemoMode()) { ?>
        return "Data in forms will be lost if you leave the page, are you sure?";
        <?php } ?>
    };
</script>
</body>
</html>
