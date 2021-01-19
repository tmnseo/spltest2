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
 
class CalendarTypes
{
    const WORK = 'W';
    const WEEKEND = 'O';

    public static function getWeekDays()
    {
        return [
            'M' => __("cp_working_calendar.monday"),
            'T' => __("cp_working_calendar.tuesday"),
            'W' => __("cp_working_calendar.wednesday"),
            'H' => __("cp_working_calendar.thursday"),
            'F' => __("cp_working_calendar.friday"),
            'S' => __("cp_working_calendar.saturday"),
            'A' => __("cp_working_calendar.sunday"),
        ];
    }

    public static function getDayDescByKey($day_key)
    {
        foreach (self::getWeekDays() as $key => $description) {
            if ($key == $day_key) {
                return $description;
            }
        }
    }

    public static function getDayKeyByDesc($day_desc)
    {
        foreach (self::getWeekDays() as $key => $description) {
            if (mb_strtolower($day_desc) == mb_strtolower($description)) {
                return $key;
            }
        }
    }
}