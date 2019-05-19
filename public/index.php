<?php
date_default_timezone_set("Europe/Paris");
use Core\Application;

$autoload = '../vendor/autoload.php';

if (!file_exists($autoload)) {
    /* @TODO redirection vers 500 */
    die("autoload not found");
}

require $autoload;
session_start();
$app = new Application;

$app->run();