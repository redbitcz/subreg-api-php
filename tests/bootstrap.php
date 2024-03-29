<?php

declare(strict_types=1);

if (@!include __DIR__ . '/../vendor/autoload.php') {
    echo 'Install Nette Tester using `composer install`';
    exit(1);
}

Tester\Environment::setup();

date_default_timezone_set('Europe/Prague');
define('TMP_DIR', __DIR__ . '/../temp/tests');

function test(Closure $function): void
{
    $function();
}
