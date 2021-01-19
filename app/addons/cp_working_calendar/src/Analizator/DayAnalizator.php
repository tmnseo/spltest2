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

namespace Tygh\Addons\CpWorkingCalendar\Analizator;

use Tygh;
use Tygh\Registry;
use Tygh\Addons\CpWorkingCalendar\Calendar\Calendar;
use Tygh\Addons\CpWorkingCalendar\Calendar\CalendarTypes;

class DayAnalizator
{
    private $calendar_company_id = 0;
    private $calendar_id;
    private $checked_timestamp;
    private $work_day = 1;
    private $formatter;


    function __construct($company_id, $checked_day)
    {   
        $this->formatter = Tygh::$app['formatter'];
        $this->calendar_company_id = $company_id;

        
        $this->checked_timestamp = $checked_day;
        
    }

    /**
    *
    * this method replaces a function 'fn_cp_get_working_day' that is used in several places in this store. This function was returning the opposite * value, so keep that in mind
    *
    * return 1 if work day
    * return 0 if not work day 
    */
    public function isWorkDay($check_worktime = true)
    {   
        $this->setWorkCalendar();

        if (empty($this->calendar_id)) {

            return false;
        }

        if ($this->isDefaultDay()) {
            
            $this->cheсkDefaultWeekends();
        }else {

            $this->cheсkUniqueWeekends();
        }

        if ($this->alredyFindWeekend()) {
            return $this->work_day;
        }

        /* If we need to check the working time of the day */
        if ($check_worktime) {
            $this->checkWorktime();
        }

        return $this->work_day;
    }

    /**
    *
    * Checks whether the current vendor has a calendar, 
    * if not, then sets $calendar_company_id = 0, which corresponds to the main calendar
    *
    * Sets the ID of the calendar that you are working with
    */
    private function setWorkCalendar()
    {   
        $calendar_id = db_get_field("SELECT calendar_id FROM ?:cp_working_calendar WHERE company_id = ?i", $this->calendar_company_id);

        if (empty($calendar_id)) {
            $this->calendar_company_id = 0;
            $calendar_id = db_get_field("SELECT calendar_id FROM ?:cp_working_calendar WHERE company_id = ?i", $this->calendar_company_id);
        }
        
        $this->calendar_id = $calendar_id;
    }

    /**
    *
    * Checking the default days off
    */
    private function cheсkDefaultWeekends()
    {   
        $calendar = new Calendar(false, $this->calendar_company_id);
        $default_weekends = $calendar->getWeekendDays($this->calendar_id);

        if (empty($default_weekends)) {
            return;
        }

        $current_day_description = $this->formatter->asDatetime($this->checked_timestamp, "%A");

        /* Don't use the in_array function to check for a string value more accurately */

        foreach ($default_weekends as $key => $day_description) {
            if (mb_strtolower($day_description) === mb_strtolower($current_day_description)) {

                $this->setAsWeekend();
                break;
                return;
            }
        }
    }

    /**
    *
    * Checking the days off that the vendor could set in addition to the default ones
    */
    private function cheсkUniqueWeekends()
    {
        
        $start_checked_day_time = $this->getCheckingDayStartTime();
        $type = $this->getUniqueDayType($start_checked_day_time);

        if (!empty($type) && $type == CalendarTypes::WEEKEND) {
            $this->setAsWeekend();
        }
    }

    /**
    *
    * Checks whether the store is currently running
    */
    private function checkWorktime()
    {   
        list($start_time, $end_time) = $this->getDayWorktime();

        $current_day_date = $this->formatter->asDatetime($this->checked_timestamp, "%d/%m/%y");
        
        $date_format = date_create_from_format("d/m/y G:i", $current_day_date . ' ' . $start_time);
        $start_working_day_timestamp = strtotime(date_format($date_format, 'm/d/y G:i'));

        unset($date_format);

        $date_format = date_create_from_format("d/m/y G:i", $current_day_date . ' ' . $end_time);
        $end_working_day_timestamp = strtotime(date_format($date_format, 'm/d/y G:i'));

        if ($this->checked_timestamp < $start_working_day_timestamp || $this->checked_timestamp > $end_working_day_timestamp) {
            $this->setAsWeekend();
        }
        
    }

    /**
    *
    * checking whether a day off has already been found
    */
    private function alredyFindWeekend()
    {
        return $this->work_day == 1 ? false : true;
    }

    /**
    *
    * Set day as weekend
    */
    private function setAsWeekend()
    {
       $this->work_day = 0; 
    }

    /**
    *
    * Function checks whether the day follows standard rules or it has an individual setting
    */
    private function isDefaultDay()
    {
        $start_checked_day_time = $this->getCheckingDayStartTime();

        $type = $this->getUniqueDayType($start_checked_day_time);

        return empty($type) ? true : false;
    }

    /**
    *
    * Function returns the timestamp of the start of the day being checked
    */
    private function getCheckingDayStartTime()
    {
        $current_day_date = $this->formatter->asDatetime($this->checked_timestamp, "%d/%m/%y");
        $date_format = date_create_from_format("d/m/y", $current_day_date);
        $start_checked_day_time = strtotime(date_format($date_format, 'm/d/y'));

        return $start_checked_day_time;
    }

    /**
    *
    * Function returns the type of a uniquely configured day , or false if the day works according to the standard settings
    */
    private function getUniqueDayType($start_checked_day_time)
    {
        $type = db_get_field("SELECT type FROM ?:cp_working_calendar_days WHERE day_timestamp = ?i AND calendar_id = ?i", $start_checked_day_time, $this->calendar_id);

        return !empty($type) ? $type : false;
    }

    /**
    *
    * Function returns the start and end time of the working day
    */
    private function getDayWorktime()
    {   
        $fields = [
            'start_time',
            'end_time'
        ];

        $condition = db_quote(" calendar_id = ?i", $this->calendar_id);

        if (($unique_week_day_time = $this->isUniqueWeekDay()) && $this->isDefaultDay()) {
            
            $time = $unique_week_day_time;          
            
        }elseif ($this->isDefaultDay()) {

            $table = "?:cp_working_calendar";

        }else {
            $start_checked_day_time = $this->getCheckingDayStartTime();

            $table = "?:cp_working_calendar_days";
            $condition .= db_quote(" AND day_timestamp = ?i", $start_checked_day_time);
        }

        if (empty($time)) {
            $time = db_get_row("SELECT ?p FROM ?p WHERE ?p", implode(',', $fields), $table, $condition);
        }
        
        return !empty($time) ? [$time['start_time'], $time['end_time']] : [];
    }

    /**
    *
    * The function checks whether the day being checked is the day of the week for which unique working time values have been configured
    */
    private function isUniqueWeekDay()
    {
        $day_start_time = $this->getCheckingDayStartTime();
        
        $day_description = Tygh::$app['formatter']->asDatetime($day_start_time, "%A");
        $day_key = CalendarTypes::getDayKeyByDesc($day_description);

        $extra_days_worktime = db_get_field("SELECT extra_days_worktime FROM ?:cp_working_calendar WHERE calendar_id = ?i", $this->calendar_id);
        
        if (!empty($extra_days_worktime)) {
            $extra_worktime = unserialize($extra_days_worktime);
        }

        if (!empty($extra_worktime[$day_key])) {

            return $extra_worktime[$day_key];
        }else {
            return [];
        }
    }
}
    