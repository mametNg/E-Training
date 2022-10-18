<?php

$app = require_once 'config/app.php';
$db = require_once 'config/database.php';


define('DB_HOST', $db['db_host']);
define('DB_USER', $db['db_user']);
define('DB_PASS', $db['db_pass']);
define('DB_NAME', $db['db_name']);
define("BASE_URL", $app['url']);