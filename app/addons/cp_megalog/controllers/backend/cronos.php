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

if ($mode == 'recalc_vendor_counts') {
    $cron_pass = Registry::get('addons.cp_megalog.cron_pass');
    if (!empty($_REQUEST['cron_pass']) && $cron_pass == $_REQUEST['cron_pass']) {
        fn_cp_megalog_count_vendor_categories_cron();
    }
    exit;
} elseif ($mode == 'cron_clear_logs') {

    $cron_pass = Registry::get('addons.cp_megalog.cron_pass');
    if (!empty($_REQUEST['cron_pass']) && $cron_pass == $_REQUEST['cron_pass']) {
        $days = Registry::get('addons.cp_megalog.clear_log_days');
        $clear_logs = false;
        $now_time = time();
        if (!empty($days)) {
            db_query("DELETE FROM ?:cp_ml_megalog WHERE timestamp <= ?i", $now_time - $days*24*60*60);
        } else {
            $clear_logs = true;
        }
    }
    exit;
}
