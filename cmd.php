#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Szczyglis\ChainParser\Command\ParseCommand;

$app = new Application();
$app->add(new ParseCommand());
$app->run();