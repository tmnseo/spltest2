<?php
if (!defined('AREA')) die('Access denied');

if (1 == 2) {
/*
    if ($_SERVER['REQUEST_URI'] == '/cscart/') {
		echo '<br>'.$_SERVER['REQUEST_URI'];
		echo '<br><b>develop:</b> <pre style="font-size: 12px">'.print_r($_SESSION['EDOST']['develop'], true).'</pre>';
		die();
	}
*/
//	define('DEBUG_MODE', true); // вывод дебаг панели
	define('DEVELOPMENT', true);
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	ini_set('display_startup_errors', true);
	$config['tweaks'] = array('disable_block_cache' => true);
}

include_once 'lib/edost_cms.php';

fn_register_hooks(
	'shippings_get_shippings_list_post',
	'prepare_checkout_payment_methods',
    'calculate_cart_taxes_pre',
    'pickup_point_variable_init',
	'place_order',
	'calculate_cart_items'
);