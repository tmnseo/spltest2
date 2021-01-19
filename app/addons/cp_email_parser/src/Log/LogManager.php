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

namespace Tygh\Addons\CpEmailParser\Log;

use Tygh\Addons\CpEmailParser\Log\LogTypesEnum;
use Tygh\Addons\CpEmailParser\Log\LogStatusesEnum;
use Tygh\Registry;

class LogManager
{
    public static function getTypeDesc($log_type, $params = array())
    {
        switch ($log_type) {
            case LogTypesEnum::ERROR_CRON_PASS:
                return __("cp_email_parser.error_cron_pass");
                break;
            case LogTypesEnum::ERROR_DIR_CREATE:
                return __("cp_email_parser.error_dir_create");
                break;
            case LogTypesEnum::ERROR_IMAP_CONNECT:
                return __("cp_email_parser.error_imap_connect");
                break;
            case LogTypesEnum::ERROR_FROM_ADDRESS:
                return __("cp_email_parser.error_from_address", ["[mess_id]" => $params['message_id']]);
                break;
            case LogTypesEnum::ERROR_COMPANY_ID:
                return __("cp_email_parser.error_company_id", ["[mess_id]" => $params['message_id'], "[email]" => $params['email']]);
                break;
            case LogTypesEnum::ERROR_NO_ATTACHMENTS:
                return __("cp_email_parser.error_no_attachments", ["[mess_id]" => $params['message_id']]);
                break;
            case LogTypesEnum::ERROR_NO_CREATE_FILE:
                return __("cp_email_parser.error_no_create_file", ["[file]" => $params['file'], "[company]" => fn_get_company_name($params['company_id'])]);
                break;
            case LogTypesEnum::ERROR_UPDATE_PRESET:
                return __("cp_email_parser.error_update_preset", ["[id]" => $params['preset_id']]);
                break;
            case LogTypesEnum::ERROR_IMPORT:
                return __("cp_email_parser.error_import", ["[id]" => $params['preset_id']]);
                break;
            default:
                return __("cp_email_parser.no_data");
                break;
        }
    }
    public static function getOperation($log_type) 
    {
        switch ($log_type) {
            case LogTypesEnum::ERROR_CRON_PASS:
                return __("cp_email_parser.connection_to_mail_server");
                break;
            case LogTypesEnum::ERROR_DIR_CREATE:
                return __("cp_email_parser.connection_to_mail_server");
                break;
            case LogTypesEnum::ERROR_IMAP_CONNECT:
                return __("cp_email_parser.connection_to_mail_server");
                break;
            case LogTypesEnum::ERROR_FROM_ADDRESS:
                return __("cp_email_parser.parsing_mail");
                break;
            case LogTypesEnum::ERROR_COMPANY_ID:
                return __("cp_email_parser.parsing_mail");
                break;
            case LogTypesEnum::ERROR_NO_ATTACHMENTS:
                return __("cp_email_parser.parsing_mail");
                break;
            case LogTypesEnum::ERROR_NO_CREATE_FILE:
                return __("cp_email_parser.parsing_attachments");
                break;
            case LogTypesEnum::ERROR_UPDATE_PRESET:
                return __("cp_email_parser.import_process");
                break;
            case LogTypesEnum::ERROR_IMPORT:
                return __("cp_email_parser.import_process");
                break;
            case LogTypesEnum::SUCCESS_PARSING:
                return __("cp_email_parser.connection_to_mail_server");
                break;
            case LogTypesEnum::MESSAGE:
                return __("cp_email_parser.parsing_mail");
                break;
            case LogTypesEnum::ATTACHMENT:
                return __("cp_email_parser.parsing_attachments");
                break;
            default:
                return __("cp_email_parser.no_data");
                break;
        }
    }
    public static function getLogRecords($params, $items_per_page, $lang_code = 'ru')
    {

        $default_params = array(
            'page' => 1,
            'items_per_page' => $items_per_page
        );

        $params = array_merge($default_params, $params);

        $conditions = '1';

        $limit = '';

        if (!empty($params['items_per_page'])) {

            $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_email_parser_log WHERE ?p", $conditions);
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        }

        $logs = db_get_array("SELECT * FROM ?:cp_email_parser_log WHERE ?p ORDER BY `log_id` DESC $limit", $conditions);
        
        
        if (!empty($logs)) {
            foreach ($logs as &$log_data) {
                $log_data['is_final'] = self::isFinalLog($log_data['log_id']);
                $log_data['type'] = self::getOperation($log_data['type']);
            }
        }
        return array($logs, $params);
    }

    public static function clearLogs()
    {
        db_query('TRUNCATE TABLE ?:cp_email_parser_log');
    }

    public static function clearOldLogs()
    {
        $log_life_time = (int) Registry::get('settings.Logging.log_lifetime');

        if (!$log_life_time) {
            return;
        }

        $conditions = [
            ['start_time', '<=', strtotime(sprintf('-%d days', $log_life_time))]
        ];

        db_query('DELETE FROM ?:cp_email_parser_log WHERE ?w', $conditions);
    }

    public static function getChainOfEvents($log_id)
    {   
        
        $log_events = self::getLogEvents($log_id);
        
        $chains[] = array(
            'time' => $log_events['start_time'],
            'process' => self::getOperation($log_events['type']),
            'message' => $log_events['data'],
            'status' => $log_events['status']
        );


        while (isset($log_events['parent_log'])) {

            $chains[] = array(
                'time' => $log_events['parent_log']['start_time'],
                'process' => self::getOperation($log_events['parent_log']['type']),
                'message' => $log_events['parent_log']['data'],
                'status' => $log_events['parent_log']['status']
            );

            $log_events = $log_events['parent_log'];
        }

        return array_reverse($chains);
    }

    public static function getLogEvents($log_id)
    {   
        $current = self::getLogById($log_id);
        
        if (!empty($current['parent_log_id'])) {
            $current['parent_log'] =  self::getLogEvents($current['parent_log_id']);
        }

        return $current;
    }

    private static function getLogById($id)
    {
        return db_get_row("SELECT * FROM ?:cp_email_parser_log WHERE log_id = ?i", $id);
    }

    private static function isFinalLog($id)
    {
        $result = db_get_field("SELECT log_id FROM ?:cp_email_parser_log WHERE parent_log_id = ?i LIMIT 1", $id);

        return !empty($result) ? false : true;
    }
}