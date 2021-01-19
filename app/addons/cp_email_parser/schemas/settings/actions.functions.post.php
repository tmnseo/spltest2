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


function fn_cp_email_parser_cron_run_info()
{
    $admin_ind = Registry::get('config.admin_index');
    $__params = Registry::get('addons.cp_email_parser');
    if (!empty($__params) && !empty($__params['cron_pass'])) {
        $cron_pass = $__params['cron_pass'];
    } else {
        $cron_pass = '';
    }
    $hint = '<b>' . __("cp_email_parser.cron_info") . ':</b><br /><code>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cp_email_parser.parse_email --cron_pass=' . $cron_pass . '</code>';
    
    return $hint;
}
