<?php
/*****************************************************************************
*                                                        © 2013 Cart-Power   *
*           __   ______           __        ____                             *
*          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
*      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
*     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
*    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
*                                                                            *
*                                                                            *
* -------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license *
* and  accept to the terms of the License Agreement can install and use this *
* program.                                                                   *
* -------------------------------------------------------------------------- *
* website: https://store.cart-power.com                                      *
* email:   sales@cart-power.com                                              *
******************************************************************************/

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_cp_vendor_o_statuses_install()
{
	$status_ids = db_get_fields("SELECT status_id FROM ?:statuses WHERE type = 'O'");
	$data = array(
		'param' => 'cp_for_vendors',
		'value' => 'Y'
	);

	foreach ($status_ids as $status_id) {
		$data['status_id'] = $status_id;
		db_query("INSERT INTO ?:status_data ?e", $data);
	}
}

function fn_cp_vendor_o_statuses_uninstall()
{
	db_query("DELETE FROM ?:status_data WHERE param = 'cp_for_vendors'");
}

function fn_cp_get_allowed_order_statuses($statuses, $order_status, $unallowed_statuses)
{
	$unallowed_statuses = array_diff($unallowed_statuses, [$order_status]);
	$unallowed_statuses = array_flip($unallowed_statuses);

	return array_diff_key($statuses, $unallowed_statuses);
}

/***********************************[hooks]***********************************/
function fn_cp_vendor_o_statuses_change_order_status(&$status_to, $status_from, $order_info, $force_notification, $order_statuses, $place_order)
{
	if (
		!empty(Registry::get('runtime.company_id')) 
		&& !empty($order_statuses[$status_to]['params']['cp_for_vendors']) 
		&& $order_statuses[$status_to]['params']['cp_for_vendors'] == 'N'
	) {
		$status_to = $status_from;
	}
}

function fn_cp_vendor_o_statuses_get_status_params_definition(&$status_params, $type)
{
	if ($type == STATUSES_ORDER) {
		$status_params['cp_for_vendors'] = array(
            'type' => 'checkbox',
            'label' => 'cp_vendor_o_statuses_setting',
            'default_value' => 'Y'
		);
	}
}

function fn_cp_vendor_o_statuses_get_statuses(
	$join,
    &$condition,
    $type,
    $status_to_select,
    $additional_statuses,
    $exclude_parent,
    $lang_code,
    $company_id,
    $order
) {
	$auth = Tygh::$app['session']['auth'];

	if (
		$auth['user_type'] != 'V'
		|| $type != STATUSES_ORDER
		|| empty($_REQUEST['order_id'])
	) {
		return;
	}

	$order_status = db_get_field(
		"SELECT status"
		. ' FROM ?:orders'
		. ' WHERE order_id = ?i',
		$_REQUEST['order_id']
	);

	$condition .= db_quote(
		' AND (?:statuses.status_id IN ('
			. "SELECT status_id"
			. ' FROM ?:status_data'
			. ' WHERE param = ?s AND value = ?s'
		. ') OR ?:statuses.status = ?s)',
		'cp_for_vendors', 'Y', $order_status
	);
}