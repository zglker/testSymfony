/** console.php **/
#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use Symfony\Component\Console\Application;
use Console\ProcessCommand;


$app = new Application('Console App', 'v1.0.0');
$app->add(new ProcessCommand(new \Console\Services\GenerateFileService(),new \Console\Services\SendEmailService()));
$app->run();