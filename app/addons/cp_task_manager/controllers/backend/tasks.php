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
use Tygh\Cron\CronExpression;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']  == 'POST') {
    fn_trusted_vars('tasks', 'task_data');
    $suffix = '';

    //
    // Delete tasks
    //
    if ($mode == 'm_delete') {
        foreach ($_REQUEST['task_ids'] as $v) {
            fn_cp_task_manager_delete_task_by_id($v);
        }

        $suffix = '.manage';
    }

    //
    // Add/edit tasks
    //
    if ($mode == 'remove_file') {
        if (!empty($_REQUEST['task_id'])) {
            $task = fn_cp_task_manager_get_task_data($_REQUEST['task_id'], DESCR_SL);
            unset($task['task_settings']['import_file']);
            unset($task['task_settings']['url_file']);
            unset($task['task_settings']['uploaded_file']);
            
            fn_cp_task_manager_update_task($task, $_REQUEST['task_id'], DESCR_SL);
            Registry::get('ajax')->assign('force_redirection', fn_url('tasks.update&task_id=' . $_REQUEST['task_id']));
        }
    }
    if ($mode == 'update') {

        $error = false;
        $task_name = trim($_REQUEST['task_data']['task']);
        if ($_REQUEST['task_id'] && empty($task_name)) {
            fn_set_notification('E', __('error'), __('cp_error_task_name_is_empty'));
            $error = true;
        }
        
        if (fn_parse_date($_REQUEST['task_data']['timestamp']) == 0) {
            fn_set_notification('E', __('error'), __('cp_error_timestamp'));
            $error = true;
        }
        if (isset($_REQUEST['task_data']['factory'])) {
        
            if (!preg_match(REGEXP_MINUTES, $_REQUEST['task_data']['factory']['minutes'], $matches)) {
                fn_set_notification('E', __('error'), __('cp_error_minutes'));
                $error = true;
            }

            if (!preg_match(REGEXP_HOURS, $_REQUEST['task_data']['factory']['hours'])) {
                fn_set_notification('E', __('error'), __('cp_error_hours'));
                $error = true;
            }
            if (!preg_match(REGEXP_DAYS, $_REQUEST['task_data']['factory']['days'])) {
                fn_set_notification('E', __('error'), __('cp_error_days'));
                $error = true;
            }
            if (!preg_match(REGEXP_MONTHS, $_REQUEST['task_data']['factory']['months'])) {
                fn_set_notification('E', __('error'), __('cp_error_months'));
                $error = true;
            }
            if (!preg_match(REGEXP_DWS, $_REQUEST['task_data']['factory']['dws'])) {
                fn_set_notification('E', __('error'), __('cp_error_dws'));
                $error = true;
            }
            
            try {
                if (!CronExpression::isValidExpression(implode(' ', $_REQUEST['task_data']['factory']))) {
                    $error = true;
                }
            } catch (Exception $e) {
                fn_set_notification('E', __('error'), $e->getMessage());
                $error = true;
            }
        }
        if ($_REQUEST['task_data']['type'] == TM_DROPBOX && isset($_REQUEST['task_data']['task_settings'][TM_DROPBOX]) 
            && !empty($_REQUEST['task_data']['task_settings'][TM_DROPBOX]['auth_url']) 
            && empty($_REQUEST['task_data']['task_settings'][TM_DROPBOX]['token'])) {
            fn_set_notification('E', __('error'), __('cp_error_dropbox_token'));
            $error = true;
        }
        if ($error) {
            fn_save_post_data('task_data');
            return array(CONTROLLER_STATUS_OK, "tasks.update?task_id=" . $_REQUEST['task_id']);
        }
        
        $task_id = fn_cp_task_manager_update_task($_REQUEST['task_data'], $_REQUEST['task_id'], DESCR_SL);

        $suffix = ".update?task_id=$task_id";
    }

    if ($mode == 'delete') {
        if (!empty($_REQUEST['task_id'])) {
            fn_cp_task_manager_delete_task_by_id($_REQUEST['task_id']);
        }

        $suffix = '.manage';
    }
    if ($mode == 'clear_logs') {
        fn_cp_task_manager_clear_logs();
        $suffix = '.view_logs';
    }
    if ($mode == 'send_email') {
        if (fn_cp_task_manager_send_email($_REQUEST)) {
            fn_set_notification('N', __('notice'), __('cp_log_email_sent'));
        } else {
            fn_set_notification('E', __('error'), __('cp_error_log_email_sent'));
        }
        $suffix = '.view_logs';
    }
    if ($mode == 'set_approve') {
        if (!empty($_REQUEST['task_id']) && !empty($_REQUEST['action'])) {
            fn_cp_aa_set_approve_action($_REQUEST['task_id'], $_REQUEST['action']);
        }
        $suffix = '.manage';
    }
    if ($mode == 'm_approve') {
        if (!empty($_REQUEST['task_ids'])) {
            fn_cp_aa_set_approve_action($_REQUEST['task_ids'], 'A');
        }
        $suffix = '.manage';
    }
    if ($mode == 'm_dapprove') {
        if (!empty($_REQUEST['task_ids'])) {
            fn_cp_aa_set_approve_action($_REQUEST['task_ids'], 'D');
        }
        $suffix = '.manage';
    }

    return array(CONTROLLER_STATUS_OK, 'tasks' . $suffix);
}

if ($mode == 'send_email') {
    if (fn_cp_task_manager_send_email($_REQUEST)) {
        fn_set_notification('N', __('notice'), __('cp_log_email_sent'));
    } else {
        fn_set_notification('E', __('error'), __('cp_error_log_email_sent'));
    }
    return array(CONTROLLER_STATUS_REDIRECT, 'tasks.view_logs');
}

if ($mode == 'delete') {
    if (!empty($_REQUEST['task_id'])) {
        fn_cp_task_manager_delete_task_by_id($_REQUEST['task_id']);
    }

    return array(CONTROLLER_STATUS_REDIRECT, 'tasks.manage');
}

if ($mode == 'clear_logs') {
    fn_cp_task_manager_clear_logs();
    return array(CONTROLLER_STATUS_REDIRECT, 'tasks.view_logs');
}    

if ($mode == 'manage') {

    $params = $_REQUEST;
    list($tasks, $search) = fn_cp_task_manager_get_tasks($params, Registry::get('settings.Appearance.elements_per_page'), DESCR_SL);
    
    Registry::get('view')->assign('tasks', $tasks);
    Registry::get('view')->assign('search', $search);
    
    if (fn_allowed_for('MULTIVENDOR')) {
        $vend_id = Registry::get('runtime.company_id');
        if (!empty($vend_id)) {
            Registry::get('view')->assign('cp_aa_is_vendor', true);
        }
    }
    
} elseif ($mode == 'update') {
    $task = fn_cp_task_manager_get_task_data($_REQUEST['task_id'], DESCR_SL);

    $saved_task_data = fn_restore_post_data('task_data');
    if (!empty($saved_task_data)) {
        $task = fn_array_merge($task, $saved_task_data);
    }

    if (empty($task)) {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }
    
    Registry::get('view')->assign('task', $task);
    
    
} elseif ($mode == 'process') {
    $cron_password = Registry::get('addons.cp_task_manager.cron_password');

    if ((!isset($_REQUEST['cron_password']) || $cron_password != $_REQUEST['cron_password']) && (!empty($cron_password))) {
        die(__('access_denied'));
    }
    $task_ids = fn_cp_task_manager_get_ready_task_ids();
    if (!empty($task_ids)) {
        foreach ($task_ids as $task_id) {
            fn_cp_task_manager_process_task_by_id($task_id);
        }
    }
    exit;
    
} 

if ($mode == 'update' || $mode == 'add') {

    // settings for database backup
    $status_data = db_get_array("SHOW TABLE STATUS");
    $database_size = 0;
    $all_tables = array();
    foreach ($status_data as $v) {
        $database_size += $v['Data_length'] + $v['Index_length'];
        $all_tables[] = $v['Name'];
    }
    Registry::get('view')->assign('all_tables', $all_tables);
    
    // setting for export
    if (fn_allowed_for('MULTIVENDOR')) {
        $vend_id = Registry::get('runtime.company_id');
        if (!empty($vend_id)) {
            Registry::get('view')->assign('cp_aa_is_vendor', true);
        }
    }
    list($sections, $patterns) = fn_cp_task_manager_get_patterns('products', 'export');
    
    foreach ($sections as $section_key => $section) {
        list(, $_patterns) = fn_cp_task_manager_get_patterns($section_key, 'export');
        $patterns = array_merge($patterns, $_patterns);
    }
    // Get available layouts
    if (empty($task)) {
        $pattern_id = 'products';
    } elseif (isset($task['task_settings']) && isset($task['task_settings']['pattern_id'])) {
        $pattern_id = $task['task_settings']['pattern_id'];
    } else {
        $pattern_id = 'products';
    }
    if (!empty($pattern_id) && $pattern_id == 'adv_products') {
    
    } elseif (!empty($pattern_id) && $pattern_id == 'data_feed' && Registry::get('addons.data_feeds.status') == 'A') {
        $feed_id = 0;
        if (!empty($task['task_settings']) && !empty($task['task_settings']['data_feed_id'])) {
            $feed_id = $task['task_settings']['data_feed_id'];
        }
        $df_params = array(
            'status' => 'A',
            'cron' => 'Y',
        );
        $data_feeds = fn_data_feeds_get_data($df_params, DESCR_SL);
    } else {
        $layouts = db_get_array("SELECT * FROM ?:exim_layouts WHERE pattern_id = ?s", $pattern_id);
        
        $selected_layout = reset($layouts);
        if (isset($task['task_settings']) && isset($task['task_settings']['layout_id'])) {
            $layout_id = $task['task_settings']['layout_id'];
        } else {
            $layout_id = $selected_layout['layout_id'];
        }

        // Extract columns information
        foreach ($layouts as $k => $v) {
            $layouts[$k]['cols'] = explode(',', $v['cols']);
            $layouts[$k]['options'] = unserialize($v['options']);

            if ($v['active'] == 'Y' && !empty($v['options'])) {
                foreach ($layouts[$k]['options'] as $option => $value) {
                    if (isset($patterns[$pattern_id]['options'][$option])) {
                        $patterns[$pattern_id]['options'][$option]['default_value'] = $value;
                    }
                }
            }
            if ($layout_id == $v['layout_id']) {
                $selected_layout = $v;
            }
        }
        $selected_layout['cols'] = explode(',', $selected_layout['cols']);
        Registry::get('view')->assign('selected_layout', $selected_layout);
        Registry::get('view')->assign('export_pattern', $patterns[$pattern_id]);
        Registry::get('view')->assign('layouts', $layouts);
    }
    // Export languages
    foreach (fn_get_translation_languages() as $lang_code => $lang_data) {
        $export_langs[$lang_code] = $lang_data['name'];
    }
    if (Registry::get('addons.data_feeds.status') == 'A') {
        $patterns['data_feed'] = array(
            'section' => 'products',
            'name' => __('cp_aa_data_feed'),
            'pattern_id' => 'data_feed'
        );
    }
    Registry::get('view')->assign('export_langs', $export_langs);
    Registry::get('view')->assign('export_patterns', $patterns);
        
    // settings for import
    list($sections, $patterns) = fn_cp_task_manager_get_patterns('products', 'import');
    foreach ($sections as $section_key => $section) {
        list(, $_patterns) = fn_cp_task_manager_get_patterns($section_key, 'import');
        $patterns = array_merge($patterns, $_patterns);
    }

    $cpv1 = ___cp('c2VsZWX0ZWRfcHJlc2V0P2lk');
    $cpv2 = ___cp('c2VsZWX0ZWRfcGF0P2lk');
    $cpv3 = ___cp('YWR2P2ltcG9ydF9wcmVzZPRz');
    $cpv4 = ___cp('YWRkb25zLmFkdmFuY2VkP2ltcG9ydC5zdGF0dPM');
    if (empty($task)) {
        $pattern_id = 'products';
    } elseif (isset($task['task_settings']) && isset($task['task_settings']['pattern_id'])) {
        $pattern_id = $task['task_settings']['pattern_id'];
    }
    if (!empty($pattern_id) && $pattern_id == 'adv_products') {
        $preset_id = 0;
        if (!empty($task['task_settings']) && !empty($task['task_settings']['adv_preset_id'])) {
            $preset_id = $task['task_settings']['adv_preset_id'];
        }
        $presets = fn_cp_aa_get_adv_presets();
        
        Registry::get('view')->assign($cpv1, $preset_id);
        Registry::get('view')->assign($cpv2, $pattern_id);
        Registry::get('view')->assign($cpv3, $presets);
    } elseif (!empty($pattern_id) && $pattern_id == 'data_feed') {
    
        Registry::get('view')->assign('selected_feed_id', $feed_id);
        Registry::get('view')->assign($cpv2, $pattern_id);
        Registry::get('view')->assign('data_feeds', $data_feeds);
    } else {
        Registry::get('view')->assign('import_pattern', $patterns[$pattern_id]);
    }
    foreach ($patterns as $k => $v) {
        unset($patterns[$k]['options']['lang_code']);
    }
    if (Registry::get($cpv4) == 'A') {
        $patterns['advanced_import_products'] = array(
            'section' => 'products',
            'name' => __('cp_aa_adv_impr_product'),
            'pattern_id' => 'adv_products'
        );
    }
    Registry::get('view')->assign('import_patterns', $patterns);
} 

if ($mode == 'select_export_pattern') {
    if (empty($_REQUEST['pattern_id'])) {
        exit;
    }
    if (!defined('AJAX_REQUEST')) {
        exit;
    }
    $pattern_id = $_REQUEST['pattern_id'];
    if ($pattern_id == 'data_feed' && Registry::get('addons.data_feeds.status') == 'A') {
        $pattern = $layouts = $selected_layout = array();
        $df_params = array(
            'status' => 'A',
            'cron' => 'Y',
        );
        $data_feeds = fn_data_feeds_get_data($df_params, DESCR_SL);
        
        Registry::get('view')->assign('selected_pat_id', $pattern_id);
        Registry::get('view')->assign('data_feeds', $data_feeds);
    } else {
        $pattern = fn_cp_task_manager_get_pattern_definition($pattern_id, 'export');
        // Get available layouts
        $layouts = db_get_array("SELECT * FROM ?:exim_layouts WHERE pattern_id = ?s", $pattern_id);
        if (empty($_REQUEST['layout_id'])) {
            $default_layout = reset($layouts);
            $layout_id = $default_layout['layout_id'];
        } else {
            $layout_id = $_REQUEST['layout_id'];
        }
        // Extract columns information
        $selected_layout = reset($layouts);
        foreach ($layouts as $k => $v) {
            $layouts[$k]['cols'] = explode(',', $v['cols']);
            $layouts[$k]['options'] = unserialize($v['options']);

            if ($v['layout_id'] == $layout_id && !empty($v['options'])) {
                $selected_layout = $v;
                foreach ($layouts[$k]['options'] as $option => $value) {
                    if (isset($pattern['options'][$option])) {
                        $pattern['options'][$option]['default_value'] = $value;
                    }
                }
            }
        }
        $selected_layout['cols'] = explode(',', $selected_layout['cols']);
    }
    $task = fn_cp_task_manager_get_task_data($_REQUEST['task_id'], DESCR_SL);
    Registry::get('view')->assign('task', $task);
    
    
    // Export languages
    foreach (fn_get_translation_languages() as $lang_code => $lang_data) {
        $export_langs[$lang_code] = $lang_data['name'];
    }
    Registry::get('view')->assign('export_langs', $export_langs);
  
    Registry::get('view')->assign('export_pattern', $pattern);
    Registry::get('view')->assign('layouts', $layouts);
    Registry::get('view')->assign('selected_layout', $selected_layout);
    
    Registry::get('view')->display('addons/cp_task_manager/views/tasks/components/export_settings.tpl');
    exit;
}

if ($mode == 'select_import_pattern') {
    if (empty($_REQUEST['pattern_id'])) {
        exit;
    }
    if (!defined('AJAX_REQUEST')) {
        exit;
    }
    $pattern_id = $_REQUEST['pattern_id'];
    
    if ($pattern_id == 'adv_products' && Registry::get('addons.advanced_import.status') == 'A') {
    
        $pattern = $presets = array();
        $presets = fn_cp_aa_get_adv_presets();
        
        Registry::get('view')->assign('selected_pat_id', $pattern_id);
        Registry::get('view')->assign('adv_import_presets', $presets);
    } else {
        $path = 'exim';
        $pattern = fn_cp_task_manager_get_pattern_definition($pattern_id, 'import', 'exim');
        unset($pattern['options']['lang_code']);
    }
    
    $task = fn_cp_task_manager_get_task_data($_REQUEST['task_id'], DESCR_SL);
    Registry::get('view')->assign('task', $task);
    
    $selected_layout['cols'] = explode(',', $selected_layout['cols']);
    // Export languages
    foreach (fn_get_translation_languages() as $lang_code => $lang_data) {
        $export_langs[$lang_code] = $lang_data['name'];
    }
  
    Registry::get('view')->assign('import_pattern', $pattern);
    Registry::get('view')->display('addons/cp_task_manager/views/tasks/components/import_settings.tpl');
    exit;
}

if ($mode == 'get_auth_token') {
    if (empty($_REQUEST['key']) || empty($_REQUEST['secret']) || empty($_REQUEST['task_id'])) {
        fn_set_notification('E', __('error'), __('cp_dropbox_bad_request'));
        exit;
    }
    $auth_url = fn_cp_task_manager_get_auth_url($_REQUEST['task_id'], $_REQUEST['key'], $_REQUEST['secret']);
    Registry::get('view')->assign('auth_url', $auth_url);
    Registry::get('view')->display('addons/cp_task_manager/views/tasks/components/dropbox_settings.tpl');
    exit;
    
}

if ($mode == 'view_logs') {

    list($logs, $search) = fn_cp_task_manager_get_logs($_REQUEST, Registry::get('settings.Appearance.elements_per_page'), DESCR_SL);
    Registry::get('view')->assign('logs', $logs);
    Registry::get('view')->assign('search', $search);
    
}

if ($mode == 'get_file' && !empty($_REQUEST['filename'])) {
    $file = fn_basename($_REQUEST['filename']);
    fn_get_file($_REQUEST['filename']);
}

if ($mode == 'get_server_time') {

    Registry::get('view')->assign('server_time', time());
    Registry::get('view')->display('addons/cp_task_manager/hooks/index/actions.post.tpl');
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
        if (isset($log['comment'])) {
            fn_get_file($log['comment']);
        }
    }

    exit;
}