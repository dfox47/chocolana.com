<?php
if (stripos(php_sapi_name(), 'cli') === false) {
    die();
}

$config = realpath(__DIR__ . '/../../config.php');
if (is_file($config)) {
    require_once($config);
}

if (!defined('DIR_APPLICATION')) {
	exit;
}

$_GET['route'] = 'extension/shipping/econt/refreshDataCron';
require_once(DIR_SYSTEM . 'startup.php');
start('admin_cron_econt');