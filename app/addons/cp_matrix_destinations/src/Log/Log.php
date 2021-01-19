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

namespace Tygh\Addons\CpMatrixDestinations\Log;


class Log
{

    //type TYP TYPE LOGS  STATUS
    //
    //
    //
    //
    //

    public static function convertDatas($data_id){
        $convert_types = array();

        $convert_types['start_edost_send'] ='начинаем процесс получения тарифов от едоста для матрицы городов';
        $convert_types['end_edost_send'] ='заканчиваем процесс получения тарифов от едоста для матрицы городов';
        
        
        if(!empty($convert_types[$data_id])){
            return $convert_types[$data_id];
        }
        
        return $data_id;


    }

    public static function addLogRecord($log_data){

       

        db_query('INSERT INTO ?:cp_matrix_logs ?e', $log_data);

    }


    public static function getLogRecords($params,$items_per_page,$lang_code='ru')
    {

        $default_params = array(
            'page' => 1,
            'items_per_page' => $items_per_page
        );

        $params = array_merge($default_params, $params);


        $_conditions = '1';


        $limit = '';
        if (!empty($params['items_per_page'])) {
            $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_matrix_logs WHERE ?p", $_conditions);
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        }

        $logs = db_get_array("SELECT * FROM ?:cp_matrix_logs WHERE ?p ORDER BY `time` DESC $limit", $_conditions);

        foreach ($logs as $key => $log) {

            $logs[$key]['data']   = self::convertDatas($log['data']);
        }

        

        return array($logs, $params);
    }


    
}