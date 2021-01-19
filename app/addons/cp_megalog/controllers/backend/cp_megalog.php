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

if ($mode == 'manage') {
    
    $params = $_REQUEST;
    
    list($logs, $search) = fn_cp_megalog_ml_get_logs($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    
    list($all_controllers, $all_modes) = fn_cp_megalog_get_logs_controllers_modes();
    
    $cron_text = 'php '.Registry::get('config.dir.root') .'/' . Registry::get('config.admin_index') . ' --dispatch=cp_megalog.cron_clear_logs --cron_pass=' . Registry::get('addons.cp_megalog.cron_pass');
    
    $types = fn_get_schema('cp_ml', 'types');
    
    Tygh::$app['view']->assign('ml_types', $types);
    Tygh::$app['view']->assign('cron_text', $cron_text);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('logs', $logs);
    Tygh::$app['view']->assign('all_controllers', $all_controllers);
    Tygh::$app['view']->assign('all_modes', $all_modes);
    
} elseif ($mode == 'clear_logs') {

    db_query("TRUNCATE TABLE ?:cp_ml_megalog");
    $types = fn_get_schema('cp_ml', 'types');
    if (!empty($types) && !empty($types['cp_megalog']) && isset($types['cp_megalog']['clear_logs'])) {
        $req_data = array(
            'label' => __('cp_ml_logs_cleared')
        );
        $put_data = array(
            'controller' => 'cp_megalog',
            'mode' => 'clear_logs',
            'method' => 'get',
            'timestamp' => time(),
            'user_id' => !empty($auth['user_id']) ? $auth['user_id'] : 0,
            'request' => json_encode($req_data)
        );
        fn_cp_megalog_ml_add_log($put_data);
    }
    return array(CONTROLLER_STATUS_REDIRECT, 'cp_megalog.manage');

} elseif ($mode == 'cron_clear_logs') {

    $cron_pass = Registry::get('addons.cp_megalog.cron_pass');
    if (!empty($_REQUEST['cron_pass']) && $cron_pass == $_REQUEST['cron_pass']) {
        $days = Registry::get('addons.cp_megalog.clear_log_days');
        if (!empty($days)) {
            fn_cp_ml_clear_cron_logs($days);
        }
    }
    exit;
}
