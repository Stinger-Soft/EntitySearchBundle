<?php
// application.php
require __DIR__ . '/../vendor/autoload.php';

use StingerSoft\EntitySearchBundle\Command\ClearIndexCommand;
use StingerSoft\EntitySearchBundle\Command\SyncCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new SyncCommand());
$application->add(new ClearIndexCommand());
$application->run();