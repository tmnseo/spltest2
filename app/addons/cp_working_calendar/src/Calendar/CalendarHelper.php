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

namespace Tygh\Addons\CpWorkingCalendar\Calendar;

use Tygh;
use Tygh\Registry;

class CalendarHelper
{
    public static function getVendorCalendars($params, $items_per_page, $lang_code = CART_LANGUAGE)
    {

        $default_params = array(
            'page' => 1,
            'items_per_page' => $items_per_page
        );

        $params = array_merge($default_params, $params);

        $fields = [
            '?:cp_working_calendar.calendar_id',
            '?:cp_working_calendar.company_id',
            'CONCAT(start_time, "-", end_time) as worktime',
            'company',
            'weekends'
        ];

        $join = "
                LEFT JOIN ?:companies as c ON c.company_id = ?:cp_working_calendar.company_id 
                LEFT JOIN ?:cp_working_calendar_weekend_days as weekend_days ON weekend_days.calendar_id = ?:cp_working_calendar.calendar_id";

        $conditions = db_quote('1 AND ?:cp_working_calendar.company_id <> ?i', 0);

        $limit = '';

        if (!empty($params['items_per_page'])) {

            $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_working_calendar ?p WHERE ?p", $join, $conditions);
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        }

        $calendars = db_get_array("SELECT " . implode(',', $fields) . " FROM ?:cp_working_calendar ?p WHERE ?p ORDER BY `calendar_id` DESC $limit", $join, $conditions);

        if (!empty($calendars)) {
            foreach ($calendars as &$calendar_data) {
                if (!empty($calendar_data['weekends'])) {
                    $calendar_data['weekends'] = explode(',', $calendar_data['weekends']);
                }
            }
        }
        return array($calendars, $params);
    }

    public static function delete($calendar_id)
    {
        $tables = [
            '?:cp_working_calendar',
            '?:cp_working_calendar_days',
            '?:cp_working_calendar_weekend_days'
        ];

        foreach ($tables as $table_name) {
            db_query("DELETE FROM " . $table_name . " WHERE calendar_id = ?i", $calendar_id);
        }
        
    }

    public static function getWorkTime($calendar_id)
    {   
        $calendar_day_data_worktime = [];

        $calendar_data = db_get_row("SELECT start_time,  end_time, extra_days_worktime FROM ?:cp_working_calendar WHERE calendar_id = ?i", $calendar_id);
        
        $week_days = CalendarTypes::getWeekDays();

        $extra_days_worktime = !empty($calendar_data['extra_days_worktime']) ? unserialize($calendar_data['extra_days_worktime']) : '';

        foreach ($week_days as $day_key => $day_description) {

            $calendar_day_data_worktime[$day_key] = [
                'start_time' => !empty($extra_days_worktime[$day_key]['start_time']) ? $extra_days_worktime[$day_key]['start_time'] : $calendar_data['start_time'],
                'end_time' => !empty($extra_days_worktime[$day_key]['end_time']) ? $extra_days_worktime[$day_key]['end_time'] : $calendar_data['end_time']
            ]; 
        }

        return $calendar_day_data_worktime; 
    }
}