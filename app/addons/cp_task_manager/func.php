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
use Tygh\Languages\Languages;
use Tygh\DataKeeper;
use Tygh\Cron\CronExpression;
use Tygh\Storage;
use Dropbox\AppInfo;
use Dropbox\WebAuthNoRedirect;
use Dropbox\Client;
use Dropbox\WriteMode;
use Dropbox\Exception_BadRequest;
use Tygh\Http;
use Tygh\Mailer;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_cp_task_manager_cron_url_info()
{
    $cron_pass = Registry::get('addons.cp_task_manager.cron_password');
    $cron_url = fn_url('tasks.process&cron_password=' . $cron_pass);
    
    $hint = __('cp_task_manager_cron_url', array('[http_location]' => $cron_url));
    $admin_ind = Registry::get('config.admin_index');
    $hint .= '<br>php ' .Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=tasks.process --cron_password=' . $cron_pass;
    
    return $hint;
}

//
// Get tasks
//
function fn_cp_task_manager_get_tasks($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    $default_params = array(
        'items_per_page' => $items_per_page,
        'page' => 1
    );

    $params = array_merge($default_params, $params);

    $sortings = array(
        'position' => '?:cp_tasks.position',
        'timestamp' => '?:cp_tasks.timestamp',
        'task' => '?:cp_task_descriptions.task',
        'type' => '?:cp_tasks.type',
        'approved' => '?:cp_tasks.approved',
        'status' => '?:cp_tasks.status',
        'next_run' => '?:cp_tasks.next_run, ?:cp_tasks.status',
    );

    $condition = $limit = '';

    $sorting = db_sort($params, $sortings, 'task', 'asc');

    if (!empty($params['item_ids'])) {
        $condition .= db_quote(' AND ?:cp_tasks.task_id IN (?n)', explode(',', $params['item_ids']));
    }
    if (fn_allowed_for('MULTIVENDOR')) {
        $company_id = Registry::get('runtime.company_id');
        $condition .= fn_get_company_condition('?:cp_tasks.company_id', true, $company_id);
    }
    if (!empty($params['period']) && $params['period'] != 'A') {
        list($params['time_from'], $params['time_to']) = fn_create_periods($params);
        $condition .= db_quote(" AND (?:cp_tasks.timestamp >= ?i AND ?:cp_tasks.timestamp <= ?i)", $params['time_from'], $params['time_to']);
    }

    $fields = array (
        '?:cp_tasks.task_id',
        '?:cp_tasks.type',
        '?:cp_tasks.status',
        '?:cp_tasks.position',
        '?:cp_tasks.factory',
        '?:cp_tasks.timestamp',
        '?:cp_tasks.next_run',
        '?:cp_tasks.task_settings',
        '?:cp_tasks.approved',
        '?:cp_tasks.company_id',
        '?:cp_task_descriptions.task',
        '?:cp_task_descriptions.description',
    );

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(DISTINCT(?:cp_tasks.task_id)) FROM ?:cp_tasks WHERE 1 $condition");
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }
    
    // update next run for all selected tasks
    $selected_tasks = db_get_array("SELECT ?:cp_tasks.task_id, ?:cp_tasks.factory FROM ?:cp_tasks WHERE 1 $condition AND status = 'A'");
    try {
        if (!empty($selected_tasks)) {
            foreach ($selected_tasks as $selected_task) {
                $selected_task['factory'] = unserialize($selected_task['factory']);
                $cron = CronExpression::factory(implode(' ', $selected_task['factory']));
                $next_run = $cron->getNextRunDate();
                db_query("UPDATE ?:cp_tasks SET next_run = ?i WHERE task_id = ?i", $next_run->getTimestamp(), $selected_task['task_id']);
            }
        }
    } catch (Exception $e) {
        fn_set_notification('E', __('error'), $e->getMessage());
    }


    $tasks = db_get_hash_array(
        "SELECT ?p FROM ?:cp_tasks " .
        "LEFT JOIN ?:cp_task_descriptions ON ?:cp_task_descriptions.task_id = ?:cp_tasks.task_id AND ?:cp_task_descriptions.lang_code = ?s" .
        "WHERE 1 ?p ?p ?p",
        'task_id', implode(", ", $fields), $lang_code, $condition, $sorting, $limit
    );
    
    return array($tasks, $params);
}

//
// Get specific task data
//
function fn_cp_task_manager_get_task_data($task_id, $lang_code = CART_LANGUAGE)
{
    // Unset all SQL variables
    $fields = $joins = array();
    $condition = '';

    $fields = array (
        '?:cp_tasks.*',
        '?:cp_task_descriptions.*',
    );

    $joins[] = db_quote("LEFT JOIN ?:cp_task_descriptions ON ?:cp_task_descriptions.task_id = ?:cp_tasks.task_id AND ?:cp_task_descriptions.lang_code = ?s", $lang_code);

    $condition = db_quote("WHERE ?:cp_tasks.task_id = ?i", $task_id);

    $task = db_get_row("SELECT " . implode(", ", $fields) . " FROM ?:cp_tasks " . implode(" ", $joins) ." $condition");
    if (empty($task)) {
        return array();
    }
    
    if (isset($task['task_settings'])) {
        $task['task_settings'] = unserialize($task['task_settings']);
    }
    if (isset($task['factory'])) {
        $task['factory'] = unserialize($task['factory']);
    }
    
    if ($task['type'] == TM_IMPORT) {
        if (!empty($task['task_settings']['import_file'])) {
            $task['task_settings']['uploaded_file'] = array(
                array('file' => fn_basename($task['task_settings']['import_file']), 'name' => fn_basename($task['task_settings']['import_file']))
            );
        } else {
            $task['task_settings']['uploaded_file'] = array();
        }
    }
    
    if ($task['type'] == TM_FTP) {
        if (!empty($task['task_settings']['ftp_file'])) {
            $task['task_settings']['ftp_uploaded_file'] = array(
                array('file' => fn_basename($task['task_settings']['ftp_file']), 'name' => fn_basename($task['task_settings']['ftp_file']))
            );
        }
    }
    try {
        $cron = CronExpression::factory(implode(' ', $task['factory']));
        $task['next_run'] = $cron->getNextRunDate()->getTimestamp();
    } catch (Exception $e) {
        fn_set_notification('E', __('error'), $e->getMessage());
    }

    return $task;
}


/**
* Deletes task and all related data
*
* @param int $task_id Task identificator
*/
function fn_cp_task_manager_delete_task_by_id($task_id)
{
    if (!empty($task_id)) {
        db_query("DELETE FROM ?:cp_tasks WHERE task_id = ?i", $task_id);
        db_query("DELETE FROM ?:cp_task_descriptions WHERE task_id = ?i", $task_id);
    }
}

function fn_cp_task_manager_update_task($data, $task_id, $lang_code = DESCR_SL)
{
    if (isset($data['timestamp'])) {
        $data['timestamp'] = fn_parse_date($data['timestamp']);
    }
    
    if (isset($data['factory'])) {
        $data['factory'] = serialize($data['factory']);
    }
    
    if (empty($task_id) && empty($data['task'])) {
        $data['task'] = fn_cp_task_manager_type_to_string($data['type']);
    }
    
    if ($data['notify_by_email'] == 'N') {
        $data['notify_email'] = '';
    }
    if (fn_allowed_for('MULTIVENDOR')) {
        $company_id = Registry::get('runtime.company_id');
        if (!empty($company_id)) {
            $data['company_id'] = $company_id;
        }
        if (!empty($data['company_id']) && empty($task_id)) {
            $data['approved'] = 'D';
        }
    } else {
        $data['company_id'] = 0;
    }
    if (isset($data['task_settings'])) {
        foreach ($data['task_settings'] as $type => $settings) {
            if ($type != $data['type']) {
                unset($data['task_settings'][$type]);
            }
        }
        if (!empty($task_id) && $data['type'] == TM_IMPORT) {
            
            if (empty($data['uploaded_file'])) {
                $file = fn_filter_uploaded_data('import_csv_file');
                
                if (!empty($file) && file_exists($file[0]['path'])) { 
                    $dest = Registry::get('config.dir.var') . 'task_manager_files/' . $task_id . '/';
                    fn_mkdir($dest);
                    fn_copy($file[0]['path'], $dest . $file[0]['name']);
                    $data['task_settings'][$data['type']]['import_file'] = $dest . $file[0]['name'];
                } 
                if (isset($_REQUEST['type_import_csv_file'][0]) && $_REQUEST['type_import_csv_file'][0] == 'url') {
                    $data['task_settings'][$data['type']]['url_file'] = $_REQUEST['file_import_csv_file'][0];
                }
            } else {
                unset($data['task_settings'][$data['type']]['uploaded_file']);
            }
        } 
        if (!empty($task_id) && $data['type'] == TM_FTP) {
            $file = fn_filter_uploaded_data('csv_file');
            if (!empty($file) && file_exists($file[0]['path'])) { 
                $dest = Registry::get('config.dir.var') . 'task_manager_ftp_files/' . $task_id . '/';
                fn_mkdir($dest);
                fn_copy($file[0]['path'], $dest . $file[0]['name']);
                $data['task_settings'][$data['type']]['ftp_file'] = $dest . $file[0]['name'];
            } 
        } 
        
        if ($data['type'] == TM_DROPBOX) {
            if (!empty($task_id)) {
                try {
                    $folder = trim($data['task_settings'][$data['type']]['folder']);
                    $splitted_folder = explode('/', $folder);
                    foreach ($splitted_folder as $key => $value) {
                        if (empty($value)) {
                            unset($splitted_folder[$key]);
                        }
                    }
                    $folder = implode('/', $splitted_folder);
                    
                    if (strpos(trim($data['task_settings'][$data['type']]['folder']), '/') === 0) { // starts with '/'
                        $folder = '/' . $folder;
                    }
                    if (!is_dir($folder)) {
                        throw new Exception('<b>' . $folder . '</b> - ' . __("cp_not_valid_path"));
                    }
                    $data['task_settings'][$data['type']]['folder'] = $folder;
                    if ($data['task_settings'][$data['type']]['use_generated_token'] == 'N' && empty($data['task_settings'][$data['type']]['access_token'])) {
                        require_once("app/addons/cp_task_manager/Tygh/Dropbox/autoload.php");
                        $app_info = new AppInfo($data['task_settings'][$data['type']]['key'], $data['task_settings'][$data['type']]['secret']);

                        $web_auth = new WebAuthNoRedirect($app_info, "task_manager");
                        
                        list($access_token, $dropbox_user_id) = $web_auth->finish($data['task_settings'][$data['type']]['token']);

                        $data['task_settings'][$data['type']]['access_token'] = $access_token;
                        $data['task_settings'][$data['type']]['dropbox_user_id'] = $dropbox_user_id;
                    } else {
                        if ($data['task_settings'][$data['type']]['use_generated_token'] == 'Y') {
                            $data['task_settings'][$data['type']]['access_token'] = $data['task_settings'][$data['type']]['token'];
                        }
                    }
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    if (strpos($message, '{') !== false) {
                        $json = explode('{', $message);
                        $message = json_decode('{' . $json[1]);
                        $message = $message->error;
                    } 
                    fn_set_notification('E', __('error'), $message);
                }
            }
        
        }

        if (isset($data['task_settings'][$data['type']])) {
            $data['task_settings'] = serialize($data['task_settings'][$data['type']]);
        } else {
            $data['task_settings'] = '';
        }
    } else {
        $data['task_settings'] = '';
    }
    $from_date = $data['from_date'];
    $to_date = $data['to_date'];

    $data['from_date'] = !empty($from_date) ? fn_parse_date($from_date) : 0;
    $data['to_date'] = !empty($to_date) ? fn_parse_date($to_date, true) : 0;

    if (!empty($data['to_date']) && $data['to_date'] < $data['from_date']) { // protection from incorrect date range (special for isergi :))
        $data['from_date'] = fn_parse_date($to_date);
        $data['to_date'] = fn_parse_date($from_date, true);
    }
    
    if (!empty($task_id)) {
    
    
        db_query("UPDATE ?:cp_tasks SET ?u WHERE task_id = ?i", $data, $task_id);
        db_query("UPDATE ?:cp_task_descriptions SET ?u WHERE task_id = ?i AND lang_code = ?s", $data, $task_id, $lang_code);

    } else {
        $task_id = $data['task_id'] = db_query("REPLACE INTO ?:cp_tasks ?e", $data);

        foreach (Languages::getAll() as $data['lang_code'] => $v) {
            db_query("REPLACE INTO ?:cp_task_descriptions ?e", $data);
        }
    }

    return $task_id;
}


function fn_cp_task_manager_get_auth_url($task_id, $key, $secret)
{
    require_once("app/addons/cp_task_manager/Tygh/Dropbox/autoload.php");
    $app_info = new AppInfo($key, $secret);

    $web_auth = new WebAuthNoRedirect($app_info, "task_manager");   

    $authorize_url = $web_auth->start();
    
    return $authorize_url;

}

function fn_cp_task_manager_type_to_string($type)
{

    if ($type == TM_DB_BACKUP) {
        return __("cp_database_backup");
    } elseif ($type == TM_FILES_BACKUP) {
        return __("cp_files_backup");
    } elseif ($type == TM_EXPORT) {
        return __("export");
    } elseif ($type == TM_IMPORT) {
        return __("import");
    } elseif ($type == TM_CLEAR_CACHE) {
        return __("clear_cache");
    } elseif ($type == TM_CLEAR_TEMPLATES) {
        return __("cp_clear_templates");
    } elseif ($type == TM_THUMBNAILS_REGENERATION) {
        return __("cp_thumbnails_regeneration");
    } elseif ($type == TM_CLEAR_LOGS) {
        return __("cp_clear_logs");
    } elseif ($type == TM_CUSTOM_SCRIPT) {
        return __("cp_custom_script");
    } elseif ($type == TM_REGENERATE_SITEMAP) {
        return __("cp_regenerate_sitemap");
    } elseif ($type == TM_DROPBOX) {
        return __("cp_dropbox");
    } elseif ($type == TM_FTP) {
        return __("ftp");
    } elseif ($type == TM_OPTIMIZE_DATABASE) {
        return __("cp_optimize_database");
     } elseif ($type == TM_DATA_FEED) {
        return __("data_feeds");
    }
}


function fn_cp_task_manager_get_ready_task_ids($time = TIME)
{
    $tasks = db_get_array("SELECT * FROM ?:cp_tasks WHERE status = 'A' AND approved = ?s AND IF(from_date, from_date <= ?i, 1) AND IF(to_date, to_date >= ?i, 1)", 'A', $time, $time);
    
    $task_ids = array();
    if (!empty($tasks)) {
        foreach ($tasks as $task) {
            $task['factory'] = unserialize($task['factory']);
            
            $cron = CronExpression::factory(implode(' ', $task['factory']));
            if ($cron->isDue()) {
                $task_ids[] = $task['task_id'];
            }
        }
    }

    return $task_ids;
}

function fn_cp_task_manager_make_log($task_id, $task, $task_type, $start_time = TIME, $end_time = TIME, $result = 1, $comment = '')
{
    $data = array(
        'task_id' => $task_id, 
        'task' => serialize($task), 
        'type' => $task_type, 
        'start_timestamp' => $start_time, 
        'stop_timestamp' => $end_time, 
        'result' => $result, 
        'comment' => serialize($comment)
    );
    $log_id = db_query("INSERT INTO ?:cp_task_logs ?e", $data);
    if ($task['notify_by_email'] == 'Y') {
        list($log,) = fn_cp_task_manager_get_logs(array('log_ids' => $log_id));
        
        $log = reset($log);
        if (!isset($log['filename'])) {
            $log['filename'] = '';
        }
        fn_cp_task_manager_send_email(array('filename' => $log['filename'], 'log_id' => $log_id, 'email' => $task['notify_email'], 'task' => $task));
    }
    
    return true;
}

function fn_cp_task_manager_process_task_by_id($task_id)
{
    if (!empty($task_id)) {
        $task = fn_cp_task_manager_get_task_data($task_id);
        
        $start_time = TIME();
        $result = array();
        if ($task['type'] == TM_DB_BACKUP) {
            $result = fn_cp_task_manager_process_database_backup($task_id, $task['task_settings']);
        } elseif ($task['type'] == TM_FILES_BACKUP) {
            $result = fn_cp_task_manager_process_files_backup($task_id, $task['task_settings']);
        } elseif ($task['type'] == TM_CLEAR_CACHE) {
            $result = fn_cp_task_manager_process_clear_cache();
        } elseif ($task['type'] == TM_CLEAR_TEMPLATES) {
            $result = fn_cp_task_manager_process_clear_templates();
        } elseif ($task['type'] == TM_THUMBNAILS_REGENERATION) {
            $result = fn_cp_task_manager_process_thumbnails_regeneration();
        } elseif ($task['type'] == TM_CLEAR_LOGS) {
            $result = fn_cp_task_manager_process_clear_logs();
        } elseif ($task['type'] == TM_CUSTOM_SCRIPT) {
            $result = fn_cp_task_manager_process_custom_script($task['task_settings']);
        } elseif ($task['type'] == TM_REGENERATE_SITEMAP) {
            $result = fn_cp_task_manager_process_regenerate_sitemap($task['task_settings']);
        } elseif ($task['type'] == TM_EXPORT) {
            if (!empty($task['company_id'])) {
                $task['task_settings']['company_id'] = $task['company_id'];
            }
            $result = fn_cp_task_manager_process_export($task['task_settings']);
        } elseif ($task['type'] == TM_IMPORT) {
            if (!empty($task['company_id'])) {
                $task['task_settings']['company_id'] = $task['company_id'];
            }
            $result = fn_cp_task_manager_process_import($task_id, $task['task_settings']);
        } elseif ($task['type'] == TM_DROPBOX) {
            $result = fn_cp_task_manager_process_dropbox($task['task_settings']);
        } elseif ($task['type'] == TM_FTP) {
            $result = fn_cp_task_manager_process_ftp($task['task_settings']);
        } elseif ($task['type'] == TM_OPTIMIZE_DATABASE) {
            $result = fn_cp_task_manager_process_optimize_database();
        } elseif ($task['type'] == TM_DATA_FEED) {
            $result = fn_cp_tm_process_data_feeds();
        }
        $end_time = TIME();
        fn_cp_task_manager_make_log($task['task_id'], $task, $task['type'], $start_time, $end_time, reset($result), $result);
    }
    return true;
}
function fn_cp_tm_process_data_feeds()
{
    if (Registry::get('addons.data_feeds.status') == 'A') {
        fn_define('DB_LIMIT_SELECT_ROW', 30);
        $params = array(
            'status' => 'A',
            'cron' => 'Y',
        );

        $datafeeds = fn_data_feeds_get_data($params);

        if (!empty($datafeeds)) {
            foreach ($datafeeds as $datafeed) {
                fn_data_feeds_export($datafeed['datafeed_id']);
            }
        }
        return array(true, 'cp_success');
    } else {
        return array(false, 'failed');
    }
}
function fn_cp_task_manager_process_optimize_database()
{
    $all_tables = db_get_fields("SHOW TABLES");
    $log = array();
    
    foreach ($all_tables as $table) {
        $_log = db_get_row("OPTIMIZE TABLE $table");
        $log[$table] = $_log['Msg_text'];
    }
    return array(true, 'success', $log);
}

function fn_cp_task_manager_process_ftp($task_settings)
{
    if (empty($task_settings['host']) || empty($task_settings['ftp_file'])) {
        return array(false, 'cp_error_log_empty_ftp_credentials', $task_settings);
    }
    
    if (empty($task_settings['port'])) {
        $conn_id = ftp_connect($task_settings['host']);
    } else {
        $conn_id = ftp_connect($task_settings['host'], $task_settings['port']);
    }
    if (!$conn_id) {
        return array(false, 'cp_error_log_ftp_cannot_connect', $task_settings); 
    }
    
    if (@ftp_login($conn_id, $task_settings['username'], $task_settings['password'])) { 
        try {

            if (@ftp_put($conn_id, $task_settings['path'] . fn_basename($task_settings['ftp_file']), $task_settings['ftp_file'], FTP_ASCII)) {
                if (isset($task_settings['remove_file']) && $task_settings['remove_file'] == 'Y') {
                    fn_rm($task_settings['ftp_file']);
                }
                return array(true, 'cp_success', $task_settings['ftp_file']);
            } else {
                return array(false, 'cp_error_log_ftp_failed_uploading', $task_settings);
            }        
        } catch (Exception $e) {
            return array(false, $e->getMessage());
        }
    } else {
        return array(false, 'cp_error_log_ftp_cannot_login', $task_settings); 
    }
    
    ftp_close($conn_id); 
}

function fn_cp_task_manager_process_dropbox($task_settings)
{
    if (empty($task_settings['folder']) || empty($task_settings['key']) || empty($task_settings['secret']) || empty($task_settings['token'])) {
        return array(false, 'cp_error_log_empty_dropbox_credentials', $task_settings);
    }
    
    require_once("app/addons/cp_task_manager/Tygh/Dropbox/autoload.php");
    
    $results = array();
    try {
        $files = fn_get_dir_contents($task_settings['folder'], false, true);
        
        $dbx_client = new Client(isset($task_settings['access_token']) ? $task_settings['access_token'] : $task_settings['token'], "task_manager");
        foreach ($files as $file) {
            $f = @fopen($task_settings['folder'] . '/' . $file, "rb");
            if ($f) {
                $result = $dbx_client->uploadFile("/" . $file, WriteMode::add(), $f);
                $results[$file] = $result;
                
                if (isset($task_settings['remove_file']) && $task_settings['remove_file'] == 'Y') {
                    fn_rm($task_settings['folder'] . '/' . $file);
                }
                fclose($f);
            }
        }
    } catch (Exception $e) {
        return array(false, $e->getMessage());
    }
    return array(true, 'cp_success', $results);
}


function fn_cp_task_manager_process_database_backup($task_id, $task_settings)
{

    if (empty($task_settings['dbdump_tables'])) {
        return array(false, 'cp_error_log_empty_dbdump_tables'); // nothing to back up
    }
    // Calculate database size and fill tables array
    $status_data = db_get_array("SHOW TABLE STATUS");
    $all_tables = array();
    foreach ($status_data as $v) {
        $all_tables[] = $v['Name'];
    }
    
    $filename = (empty($task_settings['dbdump_filename_prefix']) ? 'backup_' : $task_settings['dbdump_filename_prefix']) . date('dMY_His', TIME) . '.sql';
    
    $params = array(
        'db_filename' => $filename,
        'db_tables' => $task_settings['dbdump_tables'],
        'db_schema' => $task_settings['dbdump_schema'] == 'Y',
        'db_data' => $task_settings['dbdump_data'] == 'Y',
        'db_compress' => 'zip',
        'move_progress' => false,
    );

    $dump_file_path = fn_cp_task_manager_backup_database($task_id, $params);

    $result = $dump_file_path;
    
    if (version_compare(PRODUCT_VERSION, '4.3', '<')) {
        $path = Registry::get('config.dir.database');
    } elseif (version_compare(PRODUCT_VERSION, '4.3', '>=')) {
        $path = Registry::get('config.dir.backups');
    }
    $files = fn_get_dir_contents($path, false, true, array(), '', true);
    foreach ($files as $key => $file) {
        if (strpos($file, 'db' . $task_id . '_') !== 0) {
            unset($files[$key]);
        }
        // Unset other dumps
        if (strpos($file, $task_settings['dbdump_filename_prefix']) === false) {
            unset($files[$key]);
        }
    }
    if (count($files) > $task_settings['number_of_db_backups']) {
        $mfiles = array();
        rsort($files, SORT_STRING);
        foreach ($files as $file) {
            $mfiles[filemtime($path . $file)] = $file;
        }
        ksort($mfiles);
        $mfiles = array_slice($mfiles, -$task_settings['number_of_db_backups'], $task_settings['number_of_db_backups']);
        
        foreach ($files as $file) {
            if (!in_array($file, $mfiles)) {
                fn_rm($path . $file);
            }
        }
    }
    return $result;
}

function fn_cp_task_manager_backup_database($task_id, $params = array())
{
    $default_params = array(
        'db_tables' => array(),
        'db_schema' => false,
        'db_data' => false,
        'db_compress' => false,
        'move_progress' => true,
    );

    $params = array_merge($default_params, $params);

    $db_filename = empty($params['db_filename']) ? 'dump_' . date('mdY') . '.sql' : fn_basename($params['db_filename']);
    
    $db_filename = 'db' . $task_id . '_' . $db_filename;

    if (version_compare(PRODUCT_VERSION, '4.3', '<')) {
        $path = Registry::get('config.dir.database');
    } elseif (version_compare(PRODUCT_VERSION, '4.3', '>=')) {
        $path = Registry::get('config.dir.backups');
    }
    if (!fn_mkdir($path)) {
        return array(false, 'text_cannot_create_directory', __('text_cannot_create_directory', array('[directory]' => fn_get_rel_dir($path))));
    }

    $dump_file = $path . $db_filename;

    if (is_file($dump_file)) {
        if (!is_writable($dump_file)) {
            return array(false, 'dump_file_not_writable', $dump_file);
        }
    }
    if (!defined('DB_ROWS_PER_PASS')) {
        define('DB_ROWS_PER_PASS', 5000);
    }
    
    if (!defined('DB_MAX_ROW_SIZE')) {
        define('DB_MAX_ROW_SIZE', 10000);
    }
    $result = db_export_to_file($dump_file, $params['db_tables'], $params['db_schema'], $params['db_data'], true, false, $params['move_progress']);

    if (!empty($params['db_compress'])) {
        $ext = '.zip';
        $result = fn_compress_files($db_filename . $ext, $db_filename, dirname($dump_file));
        unlink($dump_file);

        $dump_file .= $ext;
    }

    if ($result) {
        return array(true, 'cp_success', $dump_file);
    }

    return array(false, 'cp_error_log_database_backup_failed', $dump_file);
}


function fn_cp_task_manager_process_files_backup($task_id, $task_settings)
{
    $filename = (empty($task_settings['dbdump_filename_prefix']) ? 'backup_' : $task_settings['dbdump_filename_prefix']) . date('dMY_His', TIME);
    $params = array(
        'pack_name' => $filename,
        'fs_compress' => 'zip',
        'extra_folders' => empty($task_settings['extra_folders']) ? array() : $task_settings['extra_folders'],
    );

    $dump_file_path = fn_cp_task_manager_backup_files($task_id, $params);
    
    if (!empty($dump_file_path)) {
        $result = array(true, 'cp_success', $dump_file_path);
    } else {
        $result = array(false, 'cp_error_log_files_backup_failed', $dump_file_path);
    }
    
    if (version_compare(PRODUCT_VERSION, '4.3', '<')) {
        $path = Registry::get('config.dir.database');
    } elseif (version_compare(PRODUCT_VERSION, '4.3', '>=')) {
        $path = Registry::get('config.dir.backups');
    }
    $files = fn_get_dir_contents($path, false, true, array(), '', false);
    
    foreach ($files as $key => $file) {
        if (strpos($file, '.sql') !== false) {
            unset($files[$key]);
        }
        if (strpos($file, 'f' . $task_id . '_') !== 0) {
            unset($files[$key]);
        }
        // Unset other dumps
        if (strpos($file, $task_settings['dbdump_filename_prefix']) === false) {
            unset($files[$key]);
        }
    }
    if (count($files) > $task_settings['number_of_file_backups']) {
        $mfiles = array();
        rsort($files, SORT_STRING);
        foreach ($files as $file) {
            $mfiles[filemtime($path . $file)] = $file;
        }
        ksort($mfiles);
        $mfiles = array_slice($mfiles, -$task_settings['number_of_file_backups'], $task_settings['number_of_file_backups']);
        
        foreach ($files as $file) {
            if (!in_array($file, $mfiles)) {
                fn_rm($path . $file);
            }
        }
    }
    return $result;
    
}

function fn_cp_task_manager_backup_files($task_id, $params = array())
{
    $backup_files = array(
        'app',
        'design',
        'js',
        '.htaccess',
        'api.php',
        'config.local.php',
        'config.php',
        'index.php',
        'init.php',
        'robots.txt',
        'var/themes_repository',
        'var/snapshots'
    );

    $backup_files[] = Registry::get('config.admin_index');

    if (fn_allowed_for('MULTIVENDOR')) {
        $backup_files[] = Registry::get('config.vendor_index');
    }

    if (!empty($params['backup_files'])) {
        $backup_files = $params['backup_files'];
    }

    if (!empty($params['extra_folders'])) {
        $params['extra_folders'] = array_map(function ($path) {
            return fn_normalize_path($path);
        }, $params['extra_folders']);

        $backup_files = array_merge($backup_files, $params['extra_folders']);
    }
    
    $pack_name = !empty($params['pack_name']) ? $params['pack_name'] : 'backup_' . date('dMY_His', TIME);
    $pack_name = 'f' . $task_id . '_' . $pack_name;
    
    $destination_path = fn_get_cache_path(false) . 'tmp/backup/_files/' . $pack_name;
    $source_path = Registry::get('config.dir.root') . '/';

    fn_rm($destination_path);
    fn_mkdir($destination_path);
    
    
    foreach ($backup_files as $file) {
        $dir = dirname($destination_path . '/' . $file);

        if ($dir != $destination_path) {
            fn_mkdir($dir);
        }

        fn_copy($source_path . $file, $destination_path . '/' . $file);
    }

    if (version_compare(PRODUCT_VERSION, '4.3', '<')) {
        $path = Registry::get('config.dir.database');
    } elseif (version_compare(PRODUCT_VERSION, '4.3', '>=')) {
        $path = Registry::get('config.dir.backups');
    }
    fn_mkdir($path);
    
    if (!empty($params['fs_compress'])) {

        $ext = '.zip';
        $result = fn_compress_files($pack_name . $ext, $pack_name, fn_get_cache_path(false) . 'tmp/backup/_files/');
        $destination_path = rtrim($destination_path, '/');

        if ($result) {
            fn_rename($destination_path . $ext, $path . $pack_name . $ext);
        }
        fn_rm($destination_path);

        $destination_path .= $ext;
    }
    return $path . $pack_name . '.zip';
}

function fn_cp_task_manager_process_clear_cache()
{
    fn_clear_cache();
    return array(true, 'cp_success');
}

function fn_cp_task_manager_process_clear_templates()
{
    fn_rm(Registry::get('config.dir.cache_templates'));
    return array(true, 'cp_success');
}

function fn_cp_task_manager_process_thumbnails_regeneration()
{
    Storage::instance('images')->deleteDir('thumbnails');
    return array(true, 'cp_success');
}

function fn_cp_task_manager_process_clear_logs()
{
    db_query('TRUNCATE TABLE ?:logs');
    return array(true, 'cp_success');
}

function fn_cp_task_manager_process_custom_script($task_settings)
{
    if (empty($task_settings) || empty($task_settings['custom_script'])) {
        return array(false, 'cp_error_log_empty_custom_script');
    }
    $result = Http::get($task_settings['custom_script']);
    return array(true, 'cp_success', $result);
}

function fn_cp_task_manager_exec_in_background($cmd) 
{
    if (substr(php_uname(), 0, 7) == "Windows"){
        pclose(popen("start /B ". $cmd, "r")); 
    }
    else {
        $output = array();
        $return_var = 0;
        exec($cmd . " > /dev/null &", $output, $return_var);  
    }
} 


function fn_cp_task_manager_process_regenerate_sitemap($task_settings)
{   
    if (Registry::get('addons.google_sitemap.status') != 'A') {
        return array(false, 'cp_error_log_sitemap_addon_not_active');
    }
    
    if (fn_allowed_for('ULTIMATE')) {
        $company_ids = db_get_fields("SELECT company_id FROM ?:companies WHERE status = 'A'");
    } else {
        $company_ids = array();
    }
    
    if (count($company_ids) == 1) {
        $company_ids = array();
    }
    $result = array();
    if (!empty($company_ids)) {
        foreach ($company_ids as $company_id) {
            $result[] = fn_cp_task_manager_regenerate_sitemap($company_id);
        }
    } else {
        $result[] = fn_cp_task_manager_regenerate_sitemap();
    }
    return array(true, 'cp_success', implode(', ', $result));
}

function fn_cp_task_manager_regenerate_sitemap($company_id = '')
{
    if (!defined('ITEMS_PER_PAGE')) {
        define('ITEMS_PER_PAGE', 500);
    }
    if (!defined('MAX_URLS_IN_MAP')) {
        define('MAX_URLS_IN_MAP', 50000); // 50 000 is the maximum for one sitemap file
    }
    if (!defined('MAX_SIZE_IN_KBYTES')) {
        define('MAX_SIZE_IN_KBYTES', 10000); // 10240 KB || 10 Mb is the maximum for one sitemap file
    }
    
    $sitemap_settings = Registry::get('addons.google_sitemap');
    
    if (!empty($company_id)) {
        $sitemap_path = fn_get_files_dir_path(false) . $company_id . '/google_sitemap/';
        $result = Http::get(fn_url('xmlsitemap.generate&switch_company_id=' . $company_id, 'A'));
    } else {
        $sitemap_path = fn_get_files_dir_path(false) . 'google_sitemap/';
        $result = Http::get(fn_url('xmlsitemap.generate', 'A'));
    }
    return $sitemap_path . 'sitemap.xml';
}


function fn_cp_task_manager_google_sitemap_check_counter(&$file, &$link_counter, &$file_counter, $links, $header, $footer, $type)
{
    $stat = fstat($file);
    if ((count($links) + $link_counter) > MAX_URLS_IN_MAP || $stat['size'] >= MAX_SIZE_IN_KBYTES * 1024) {
        fwrite($file, $footer);
        fclose($file);
        $file_counter++;
        $filename = fn_get_files_dir_path() . 'google_sitemap/sitemap' . $file_counter . '.xml';
        $file = fopen($filename, "wb");
        $link_counter = count($links);
        fwrite($file, $header);
    } else {
        $link_counter += count($links);
    }
}



function fn_cp_task_manager_process_import($task_id, $task_settings)
{
    if (!empty($task_settings['pattern_id']) && $task_settings['pattern_id'] == 'adv_products') { // Advanced import
        if (Registry::get('addons.advanced_import.status') == 'A' && !empty($task_settings['adv_preset_id'])) {
            list($result, $msg) = fn_cp_aa_adv_import_process($task_settings['adv_preset_id']);
            if ($result) {
                return array(true, 'cp_success', $msg);
            } else {
                return array(false, $msg, $result);
            }
        } else {
            return array(false, 'cp_error_log_empty_import_credentials', $task_settings);
        }
    } else {
        if (empty($task_settings) || !isset($task_settings['pattern_id']) || !isset($task_settings['import_file'])) {
            return array(false, 'cp_error_log_empty_import_credentials', $task_settings);
        }
        
        if (is_file(Registry::get('config.dir.addons') . 'cp_task_manager/exim_functions.php')) {
            include_once(Registry::get('config.dir.addons') . 'cp_task_manager/exim_functions.php');
        }
        
        $pattern = fn_cp_task_manager_get_pattern_definition($task_settings['pattern_id'], 'import');
        $result = false;
        
        if (isset($task_settings['url_file'])) {
            $_REQUEST['type_import_csv_file'][0] = 'url';
            $_REQUEST['file_import_csv_file'][0] = $task_settings['url_file'];
            $file = fn_filter_uploaded_data('import_csv_file');
            if (!empty($file) && file_exists($file[0]['path'])) { 
                $dest = Registry::get('config.dir.var') . 'task_manager_files/' . $task_id . '/';
                fn_mkdir($dest);
                fn_copy($file[0]['path'], $dest . $file[0]['name']);
                $task_settings['import_file'] = $dest . $file[0]['name'];
            } 
        }
        
        if (function_exists('fn_get_csv')) {
            if (($data = fn_get_csv($pattern, $task_settings['import_file'], $task_settings['import_options'])) != false) {
                $result = fn_cp_task_manager_import($pattern, $data, $task_settings['import_options']);
            }
        } else {
            if (($data = fn_exim_get_csv($pattern, $task_settings['import_file'], $task_settings['import_options'])) != false) {
                $result = fn_cp_task_manager_import($pattern, $data, $task_settings['import_options']);
            }
        }
    }
    if ($result) {
        return array(true, 'cp_success', $result);
    } else {
        $message = array();
        if (!empty($_SESSION['notifications'])) {
            foreach ($_SESSION['notifications'] as $key => $notification) {
                if ($notification['type'] == 'E') {
                    $message[] = $notification['message'];
                }
                unset($_SESSION['notifications'][$key]);
            }
        }
        return array(false, 'error', empty($message) ? __('error_occured') : implode(', ', $message));
    }
}
function fn_cp_aa_adv_import_process($preset_id)
{
    $back_result = false;
    $back_msg = 'failed';
    if (!empty($preset_id)) {
        /** @var \Tygh\Addons\AdvancedImport\Presets\Manager $presets_manager */
        $presets_manager = Tygh::$app['addons.advanced_import.presets.manager'];
        /** @var \Tygh\Addons\AdvancedImport\Presets\Importer $presets_importer */
        $presets_importer = Tygh::$app['addons.advanced_import.presets.importer'];
        list($presets,) = $presets_manager->find(false, array('ip.preset_id' => $preset_id), false);
        if ($presets) {
            Registry::set('runtime.advanced_import.in_progress', true, true);

            $preset = reset($presets);

            /** @var \Tygh\Addons\AdvancedImport\Readers\Factory $reader_factory */
            $reader_factory = Tygh::$app['addons.advanced_import.readers.factory'];

            $is_success = false;
            try {
                $reader = $reader_factory->get($preset);
                $fields_mapping = $presets_manager->getFieldsMapping($preset['preset_id']);

                $pattern = $presets_manager->getPattern($preset['object_type']);
                $schema = $reader->getSchema();
                $schema->showNotifications();
                $schema = $schema->getData();

                $remapping_schema = $presets_importer->getEximSchema(
                    $schema,
                    $fields_mapping,
                    $pattern
                );
                if ($remapping_schema) {
                    $presets_importer->setPattern($pattern);
                    $result = $reader->getContents(null, $schema);
                    $result->showNotifications();

                    $import_items = $presets_importer->prepareImportItems(
                        $result->getData(),
                        $fields_mapping,
                        $preset['object_type'],
                        true,
                        $remapping_schema
                    );

                    $presets_manager->update($preset['preset_id'], array(
                        'last_launch' => TIME,
                        'last_status' => 'P',
                    ));

                    $preset['options']['preset'] = $preset;
                    unset($preset['options']['preset']['options']);

                    // Sets execution timeout for files getting from remote server
                    Http::setDefaultTimeout(ADVANCED_IMPORT_HTTP_EXECUTION_TIMEOUT);
                    $preset['options']['cp_is_tm'] = true;
                    $is_success = fn_import($pattern, $import_items, $preset['options']);
                }
            } catch (ReaderNotFoundException $e) {
                $back_result = false;
                $back_msg = 'error_exim_cant_read_file';
            } catch (PermissionsException $e) {
                $back_result = false;
                $back_msg = 'advanced_import.cant_load_file_for_company';
            } catch (FileNotFoundException $e) {
                $back_result = false;
                $back_msg = 'advanced_import.cant_load_file_for_company';
            } catch (DownloadException $e) {
                $back_result = false;
                $back_msg = 'advanced_import.cant_load_file';
            }

            $presets_manager->update($preset['preset_id'], array(
                'last_status' => $is_success ? 'S' : 'F',
                'last_result' => Registry::get('runtime.advanced_import.result'),
            ));
            if (!empty($is_success) && !empty($is_success['info'])) {
                $back_msg = $is_success['info'];
                $back_result = true;
            } elseif ($is_success) {
                $back_result = true;
                $back_msg = 'success';
            }
            Registry::set('runtime.advanced_import.in_progress', false, true);

        } else {
            $back_result = false;
            $back_msg = 'advanced_import.preset_not_found';
        }
    }
    return array($back_result, $back_msg);
}
function fn_cp_task_manager_import_post($pattern, $import_data, $options, &$result, $processed_data)
{
    if (!empty($options) && !empty($options['cp_is_tm'])) {
        $final_import_notification = __('text_exim_data_imported', array(
            '[new]' => $processed_data['N'],
            '[exist]' => $processed_data['E'],
            '[skipped]' => $processed_data['S'],
            '[total]' => $processed_data['E'] + $processed_data['N'] + $processed_data['S']
        ));
        $result = array(
            'result' => $result,
            'info' => $final_import_notification
        );
    }
}

function fn_cp_task_manager_process_export($task_settings)
{
    if (!empty($task_settings['pattern_id']) && $task_settings['pattern_id'] == 'data_feed') { // Data feeds
        if (Registry::get('addons.data_feeds.status') == 'A' && !empty($task_settings['data_feed_id'])) {
            fn_define('DB_LIMIT_SELECT_ROW', 30);
            $params = array(
                'status' => 'A',
                'cron' => 'Y',
                'datafeed_id' => $task_settings['data_feed_id']
            );

            $datafeeds = fn_data_feeds_get_data($params);

            if (!empty($datafeeds)) {
                foreach ($datafeeds as $datafeed) {
                    $result = fn_data_feeds_export($datafeed['datafeed_id']);
                }
                if ($result) {
                    return array(true, 'cp_success', 'cp_success');
                } else {
                    return array(false, 'failed', $result);
                }
            } else {
                return array(true, 'cp_success', __('cp_nothing_to_export'));
            }            
        } else {
            return array(false, 'cp_error_log_empty_import_credentials', $task_settings);
        }
    } else {
        if (empty($task_settings) || !isset($task_settings['pattern_id']) || empty($task_settings['layout_id'])) {
            return array(false, 'cp_error_log_empty_export_credentials', $task_settings);
        }
        include_once(Registry::get('config.dir.addons') . 'cp_task_manager/exim_functions.php');
        $pattern = fn_cp_task_manager_get_pattern_definition($task_settings['pattern_id'], 'export');
        
        $layout = db_get_row("SELECT * FROM ?:exim_layouts WHERE layout_id = ?i", $task_settings['layout_id']);
        $export_fields = explode(',', $layout['cols']);
        $options = $task_settings['export_options'];
        
        if (!empty($task_settings['company_id'])) {
            $options['company_id'] = $task_settings['company_id'];
        }
        $result = fn_cp_task_manager_export($pattern, $export_fields, $options);
    }
    
    if ($result) {
        return array(true, 'cp_success', fn_get_files_dir_path() . $options['filename']);
    } else {
        return array(true, 'cp_success', __('cp_nothing_to_export'));
    }
}

function fn_cp_aa_get_adv_presets()
{
    $presets = array();
    $presets_manager = Tygh::$app['addons.advanced_import.presets.manager'];
    if (!empty($presets_manager)) {
        $params = array(
            'page'              => 1,
            'items_per_page'    => 99999,
            'object_type'       => 'products',
            'preview_preset_id' => 0,
        );

        list($presets, $search) = fn_get_import_presets($params);
        /** @var \Tygh\Addons\AdvancedImport\Presets\Manager $presets_manager */
        if ($presets) {
            list($modifiers_presense,) = $presets_manager->find(
                false,
                array(
                    array('modifier', '<>', ''),
                    'ipf.preset_id' => array_keys($presets),
                ),
                array(
                    array(
                        'table'     => array('?:import_preset_fields' => 'ipf'),
                        'condition' => array('ip.preset_id = ipf.preset_id'),
                    ),
                    array(
                        'table'     => array('?:import_preset_descriptions' => 'ipd'),
                        'condition' => array('ip.preset_id = ipd.preset_id'),
                    ),
                ),
                array(
                    'COUNT(ipf.field_id)' => 'has_modifiers',
                )
            );
            $presets = fn_array_merge($presets, $modifiers_presense);
        }
    }
    return $presets;
}
function fn_cp_task_manager_get_pattern_definition($pattern_id, $get_for = '', $folder = 'exim')
{
    // First, check basic patterns
    if (fn_allowed_for('ULTIMATE') && Registry::get('runtime.companies_available_count') == 1) {
        //Registry::set('runtime.company_id', Registry::get('runtime.company_data.company_id'));
    }
    
    $schema = fn_get_schema($folder, $pattern_id);

    if (empty($schema)) {
        return false;
    }

    if ((!empty($schema['export_only']) && $get_for == 'import') || (!empty($schema['import_only']) && $get_for == 'export')) {
        return array();
    }

    $has_alt_keys = false;

    foreach ($schema['export_fields'] as $field_id => $field_data) {
        if (!empty($field_data['table'])) {
            // Table exists in export fields, but doesn't exist in references definition
            if (empty($schema['references'][$field_data['table']])) {
                return false;
            }
        }

        // Check if schema has alternative keys to import basic data
        if (!empty($field_data['alt_key'])) {
            $has_alt_keys = true;
        }

        if ((!empty($field_data['export_only']) && $get_for == 'import') || (!empty($field_data['import_only']) && $get_for == 'export')) {
            unset($schema['export_fields'][$field_id]);
        }
    }

    if ($has_alt_keys == false) {
        return false;
    }

    return $schema;
}

function fn_cp_task_manager_get_patterns($section, $get_for)
{
    // Get core patterns
    $files = fn_get_dir_contents(Registry::get('config.dir.schemas') . 'exim', false, true, '.php');
    $addon_path = Registry::get('config.dir.addons');
    $schema_dirsss = $addon_path . 'advanced_import/schemas/advanced_import';

    foreach (Registry::get('addons') as $addon_name => $addon_data) {
        if ($addon_data['status'] != 'A') {
            continue;
        }
        
        $schema_dir = $addon_path . $addon_name . '/schemas/exim';
        if (is_dir($schema_dir)) {
            $_files = fn_get_dir_contents($schema_dir, false, true, '.php');
            foreach ($_files as $key => $filename) {
                if (strpos($filename, '.post.php') !== false) {
                    unset($_files[$key]);
                }
            }

            if (!empty($_files)) {
                $files = fn_array_merge($files, $_files, false);
            }
        }
    }

    $patterns = array();
    $sections = array();

    foreach ($files as $schema_file) {
        if (strpos($schema_file, '.functions.') !== false) { // skip functions schema definition
            continue;
        }

        $pattern_id = str_replace('.php', '', $schema_file);
        $pattern = fn_cp_task_manager_get_pattern_definition($pattern_id, $get_for);
        if (empty($pattern)) {
            continue;
        }


        $sections[$pattern['section']] = array (
            'title' => __($pattern['section']),
            'href' => 'exim.' . Registry::get('runtime.mode') . '?section=' . $pattern['section'],
        );
        if ($pattern['section'] == $section) {
            $patterns[$pattern_id] = $pattern;
        }
    }

    if (Registry::get('runtime.company_id')) {
        $schema = fn_get_permissions_schema('vendor');
        // Check if the selected section is available
        if (isset($schema[$get_for]['sections'][$section]['permission']) && !$schema[$get_for]['sections'][$section]['permission']) {
            return array('', '');
        }
        //disable not available exim sections for vendors
        if (fn_allowed_for('MULTIVENDOR') && isset($schema['controllers']['exim']) && isset($schema['controllers']['exim']['modes']) && isset($schema['controllers']['exim']['modes'][$get_for]) 
            && isset($schema['controllers']['exim']['modes'][$get_for]['param_permissions']) && !empty($schema['controllers']['exim']['modes'][$get_for]['param_permissions']['section'])) {
            foreach($schema['controllers']['exim']['modes'][$get_for]['param_permissions']['section'] as $sect_name => $sect_id) {
                if (empty($sect_id) && isset($sections[$sect_name])) {
                    unset($sections[$sect_name]);
                }
            }
        }

        if (!empty($schema[$get_for]['sections'])) {
            foreach ($schema[$get_for]['sections'] as $section_id => $data) {
                if (isset($data['permission']) && !$data['permission']) {
                    unset($sections[$section_id]);
                }
            }
        }

        if (!empty($schema[$get_for]['patterns'])) {
            foreach ($schema[$get_for]['patterns'] as $pattern_id => $data) {
                if (isset($data['permission']) && !$data['permission']) {
                    unset($patterns[$pattern_id]);
                }
            }
        }
    }

    ksort($sections, SORT_STRING);
    uasort($patterns, 'fn_cp_task_manager_sort_patterns');

    return array($sections, $patterns);
}

function fn_cp_task_manager_sort_patterns($a, $b)
{
    $s1 = isset($a['order']) ? $a['order'] : $a['pattern_id'];
    $s2 = isset($b['order']) ? $b['order'] : $b['pattern_id'];
    if ($s1 == $s2) {
        return 0;
    }

    return ($s1 < $s2) ? -1 : 1;
}


function fn_cp_task_manager_get_logs($params, $items_per_page = 0, $lang_code = DESCR_SL)
{
    $default_params = array(
        'items_per_page' => $items_per_page,
        'page' => 1,
    );

    $params = array_merge($default_params, $params);

    $sortings = array(
        'start_timestamp' => '?:cp_task_logs.start_timestamp',
        'stop_timestamp' => '?:cp_task_logs.stop_timestamp',
        'task' => '?:cp_task_logs.task_id',
        'type' => '?:cp_task_logs.type',
        'result' => '?:cp_task_logs.result',
    );

    $condition = $limit = '';

    $sorting = db_sort($params, $sortings, 'start_timestamp', 'desc');

    if (!empty($params['task_ids'])) {
        $condition .= db_quote(' AND ?:cp_task_logs.task_id IN (?n)', explode(',', $params['task_ids']));
    }
    
    if (!empty($params['log_ids'])) {
        $condition .= db_quote(' AND ?:cp_task_logs.log_id IN (?n)', explode(',', $params['log_ids']));
    }

    if (!empty($params['period']) && $params['period'] != 'A') {
        list($params['time_from'], $params['time_to']) = fn_create_periods($params);
        $condition .= db_quote(" AND (?:cp_task_logs.start_timestamp >= ?i AND ?:cp_task_logs.start_timestamp <= ?i)", $params['time_from'], $params['time_to']);
    }
    
    if (!empty($params['type'])) {
        $condition .= db_quote(" AND ?:cp_task_logs.type = ?s", $params['type']);
    }

    $fields = array (
        '?:cp_task_logs.*',
    );

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_task_logs WHERE 1 $condition");
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }
    
    $logs = db_get_array(
        "SELECT ?p FROM ?:cp_task_logs " .
        "LEFT JOIN ?:cp_tasks ON ?:cp_task_logs.task_id = ?:cp_tasks.task_id " .
        "WHERE 1 ?p ?p ?p",
        implode(", ", $fields), $condition, $sorting, $limit
    );
    
    foreach ($logs as $key => $log) {
        $logs[$key]['task'] = unserialize($log['task']);
        $comment = unserialize($log['comment']);
        if (is_array($comment)) {
            $logs[$key]['result'] = ($comment[0]) ?  __('cp_success') : __('cp_error');
            if ($log['result']) {
                if (isset($comment[2])) {
                    if ($log['type'] == TM_DB_BACKUP || $log['type'] == TM_FILES_BACKUP || $log['type'] == TM_EXPORT || $log['type'] == TM_REGENERATE_SITEMAP) { // should be a file
                    
                        if (is_array($comment[2]) && isset($comment[2][2])) {
                            $comment[2] = $comment[2][2];
                        } 
                        if (file_exists($comment[2])) {
                            $file = $comment[2];
                            $logs[$key]['comment'] = fn_basename($comment[2]);
                            if (Registry::get('config.http_host') != 'demo.cart-power.com') {
                                $logs[$key]['download_link'] = fn_url('tasks.get_file?filename=' . $comment[2]);
                            }
                            $logs[$key]['filename'] = $file;
                        } else {
                            $logs[$key]['comment'] = fn_basename($comment[2]);
                        }
                    } elseif ($log['type'] == TM_IMPORT) {
                        $logs[$key]['comment'] = $comment[2];
                    } elseif ($log['type'] == TM_CUSTOM_SCRIPT) {
                        $logs[$key]['comment'] = $logs[$key]['result'];
                        $logs[$key]['sub_comment'] = fn_cp_task_manager_convert_array_to_string($comment[2]);
                    } elseif ($log['type'] == TM_DROPBOX) {
                        $logs[$key]['comment'] = $logs[$key]['result'];
                        $logs[$key]['sub_comment'] = fn_cp_task_manager_convert_array_to_string($comment[2]);
                    } elseif ($log['type'] == TM_OPTIMIZE_DATABASE) {
                        $logs[$key]['comment'] = $logs[$key]['result'];
                        $logs[$key]['sub_comment'] = fn_cp_task_manager_convert_array_to_string($comment[2]);
                    } else {
                        $logs[$key]['comment'] = var_export($comment, true);
                    }
                } else {
                    if ($log['type'] == TM_CLEAR_CACHE || $log['type'] == TM_CLEAR_TEMPLATES || $log['type'] == TM_THUMBNAILS_REGENERATION || $log['type'] == TM_CLEAR_LOGS || $log['type'] == TM_DATA_FEED) {
                        $logs[$key]['comment'] = $logs[$key]['result'];
                    }
                }
            } elseif (!$log['result']) {
                $logs[$key]['comment'] = __($comment[1]);
                if (isset($comment[2]))  {
                    $logs[$key]['sub_comment'] = fn_cp_task_manager_convert_array_to_string($comment[2]);
                }
            }
            
        }
    }
    
    return array($logs, $params);

}


function fn_cp_task_manager_convert_array_to_string($array = array())
{   
    $result = '<pre>';
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $result .= $key . ': ';
            if (is_array($value)) {
                $result .= fn_cp_task_manager_convert_array_to_string($value);
            } else {
                $result .= $value;
            }
            $result .= '</br>';
        }
    } else {
        $result .= $array;
    }
    $result .= '</pre>';
    return $result;
    
}

function fn_cp_task_manager_send_email($params)
{
    if (empty($params['log_id'])) {
        return false;
    }
    
    if (empty($params['task'])) {
        list($log,) = fn_cp_task_manager_get_logs(array('log_ids' => $params['log_id']));
        $log = reset($log);
        $params['task'] = $log['task'];
    }
    
    if (Registry::get('config.http_host') != 'demo.cart-power.com') {
        $email = '';

        // Create access key
        $ekey = fn_generate_ekey($params['log_id'], 'L', SECONDS_IN_DAY * 30);
        $result = Mailer::sendMail(array(
            'to' => empty($params['email']) ? 'company_site_administrator' : $params['email'],
            'from' => 'company_site_administrator',
            'data' => array(
                'access_key' => $ekey,
                'log_id' => $params['log_id'],
                'filename' => fn_basename($params['filename']),
                'task_url' => "<a href=\"" . fn_url("tasks.update?task_id=" . $params['task']['task_id']) . "\">" . " #" . $params['task']['task_id'] . ": " . $params['task']['task'] . "</a>",
                'task' => $params['task'],
            ),
            'tpl' => 'addons/cp_task_manager/log_email.tpl',
            'company_id' => 0,
        ), 'A', DESCR_SL);
        if ($result) {
            return true;
        } else {
            return false;
        }
    } 
    return false;
}

function fn_cp_task_manager_clear_logs()
{
    db_query("TRUNCATE ?:cp_task_logs");
    return true;
}

function fn_cp_aa_check_vendor_permissions_on_tasks()
{
    if (!fn_allowed_for('MULTIVENDOR')) {
        return true;
    }

    $current_company_id = Registry::get('runtime.company_id');
    $vendor_plans_addon_status = Registry::get('addons.vendor_plans.status');
    if (empty($current_company_id) || $vendor_plans_addon_status != 'A') {
        return true;
    }

    $is_allowed = db_get_field("SELECT cp_aa_tasks FROM ?:vendor_plans LEFT JOIN ?:companies USING (plan_id) WHERE company_id = ?i", $current_company_id);

    return $is_allowed == 'Y' ? true : false;
}
function fn_cp_aa_set_approve_action($task_ids, $action)
{
    if (!empty($task_ids) && !empty($action)) {
        if (!is_array($task_ids)) {
            $task_ids = (array) $task_ids;
        }
        db_query("UPDATE ?:cp_tasks SET approved = ?s WHERE task_id IN (?n) AND company_id > ?i", $action, $task_ids, 0);
    }
    return true;
}
function fn_cp_aa_install_func()
{
    if (version_compare(PRODUCT_VERSION, '4.9.3', '>')) {
        db_query("UPDATE ?:privileges SET group_id = ?s WHERE privilege = ?s", 'cp_aa_privil', 'view_tasks');
        db_query("UPDATE ?:privileges SET group_id = ?s WHERE privilege = ?s", 'cp_aa_privil', 'manage_tasks');
    }
    if (fn_allowed_for("MULTIVENDOR")) {
        $vendor_plans_status = Registry::get('addons.vendor_plans.status');
        if (!empty($vendor_plans_status)) {
            fn_cp_aa_add_field_for_vp();
        }
    }
    return true;
}

function fn_cp_aa_uninstall()
{
    if (fn_allowed_for("MULTIVENDOR")) {
        $vendor_plans_status = Registry::get('addons.vendor_plans.status');
        if (!empty($vendor_plans_status)) {
            db_query("ALTER TABLE ?:vendor_plans DROP cp_aa_tasks");
        }
    }
    return true;
}

function fn_cp_aa_add_field_for_vp()
{
    db_query("ALTER TABLE ?:vendor_plans ADD cp_aa_tasks char(1) NOT NULL default 'N'");
    return true;
}