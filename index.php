<?php
date_default_timezone_set('Asia/Jakarta');
ini_set('memory_limit', '-1');
set_time_limit(-1);
ini_set('max_input_vars', -1);
if (!isset($_SESSION)) session_start();
// Authorization: Bearer <token>

require_once __DIR__ . '/terminate/autoload.php';

use App\App;

$controller = false;
$method = false;
$params = [];

if (isset($argv)) {
	array_shift($argv);
	if (isset($argv[0])) {
		$controller = $argv[0];
		unset($argv[0]);
	}

	if (isset($argv[1])) {
		$method = $argv[1];
		unset($argv[1]);
	}
	if (!empty($argv)) {
		$params = $argv;
	}
}

$objek = new App($controller, $method, $params);


