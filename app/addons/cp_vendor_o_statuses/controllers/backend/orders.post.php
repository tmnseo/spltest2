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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	return;
}

if ($mode == 'manage') {
	$unallowed_statuses = db_get_fields(
		"SELECT status"
		. ' FROM ?:statuses'
		. ' WHERE status_id NOT IN ('
			. "SELECT status_id"
			. ' FROM ?:status_data'
			. ' WHERE param = ?s AND value = ?s'
		. ')'
		. ' AND type = ?s',
		'cp_for_vendors', 'Y', 'O'
	);

	Tygh::$app['view']->assign('cp_unallowed_statuses', $unallowed_statuses);
}