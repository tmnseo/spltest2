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
use Tygh\Addons\CpWorkingCalendar\Calendar\CalendarTypes;

class DayHelper
{
    public static function getDayData($day_start_time, $calendar_id)
    {
        
        $data = [];
        
        if (empty($calendar_id)) {
            // current calendar no exsist get default calendar data
            $data = db_get_row("SELECT start_time, end_time FROM ?:cp_working_calendar WHERE company_id = ?i", 0);
        }elseif (!empty($calendar_id) && !empty($day_start_time)) {
            $data = db_get_row("SELECT start_time, end_time, type FROM ?:cp_working_calendar_days WHERE day_timestamp = ?i AND calendar_id = ?i", $day_start_time, $calendar_id);
        }

        if (empty($data) && !empty($calendar_id)) {

            $extra_days_worktime = db_get_field("SELECT extra_days_worktime FROM ?:cp_working_calendar WHERE calendar_id = ?i", $calendar_id);
            
            if (!empty($extra_days_worktime)) {
                $extra_worktime = unserialize($extra_days_worktime);
            }

            $day_description = Tygh::$app['formatter']->asDatetime($day_start_time, "%A");
            $day_key = CalendarTypes::getDayKeyByDesc($day_description);
            

            if (!empty($extra_worktime[$day_key])) {
                
                $data = $extra_worktime[$day_key];
            }else {

                $data = db_get_row("SELECT start_time, end_time FROM ?:cp_working_calendar WHERE calendar_id = ?i", $calendar_id);    
            }
        }

        return $data;
    }

    public static function reset($calendar_id, $day_timestamp = NULL)
    {
        if (!empty($calendar_id) && !empty($day_timestamp)) {
            db_query("DELETE FROM ?:cp_working_calendar_days WHERE day_timestamp = ?i AND calendar_id = ?i", $day_timestamp, $calendar_id);

        }elseif (!empty($calendar_id) && empty($day_timestamp)) {
            db_query("DELETE FROM ?:cp_working_calendar_days WHERE calendar_id = ?i", $calendar_id);
        }
    }
}