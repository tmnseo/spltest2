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

if ($mode == 'test') {
    fn_rm(Registry::get('config.dir.cache_templates'));
    echo 'OK';
    exit;
}
if ($mode == 'download') {
    if (!empty($_REQUEST['ekey'])) {
        $log_id = fn_get_object_by_ekey($_REQUEST['ekey'], 'L');

        if (empty($log_id)) {
            return array(CONTROLLER_STATUS_DENIED);
        }

        list($log,) = fn_cp_task_manager_get_logs(array('log_ids' => $log_id));
        $log = reset($log);
        if (isset($log['filename']) && is_file($log['filename'])) {
            fn_get_file($log['filename']);
        } else {
            return array(CONTROLLER_STATUS_NO_PAGE);
        }
    }

    exit;
}
