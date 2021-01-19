<?php
if (!defined('BOOTSTRAP')) die('Access denied');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (!empty($_REQUEST['addon']) && $_REQUEST['addon'] === 'rus_edost2' && !empty($_REQUEST['module']) && function_exists('edost_config')) edost_config('save');
}