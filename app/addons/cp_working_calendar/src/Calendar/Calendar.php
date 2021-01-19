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
use Tygh\Addons\CpWorkingCalendar\Service;
use Tygh\Addons\CpWorkingCalendar\Calendar\DayHelper;
use Tygh\Addons\CpWorkingCalendar\Calendar\CalendarTypes;

class Calendar
{
    private $is_main;
    private $company_id;
    private $calendar_data = [];
    private $start_time;
    private $end_time;

    const FIRST_DAY_IN_WEEK = 'понедельник';
    const DAYS_IN_WEEK = 7;

    /*
    *   $is_main - flag is_main admin calendar or not
    *   $company_id - company id
    */
    function __construct($is_main, $company_id)
    {
        $this->is_main = $is_main;
        $this->company_id = $company_id;
    }

    /*
    *   $calendar_data - array with calendar form data
    *   @return $calendar_id 
    */
    public function setData($calendar_data)
    {
        if ($this->is_main) {
            $calendar_data['company_id'] = 0;
        }
        
        if ((empty($calendar_data['reset_extra_worktime']) || $calendar_data['reset_extra_worktime'] != 'Y') && isset($calendar_data['calendar_id'])) {
            $calendar_data['extra_days_worktime'] = $this->getCalendarExtraWorktime($calendar_data['calendar_id']);
        }

        $calendar_id = db_query("REPLACE INTO ?:cp_working_calendar ?e", $calendar_data);

        /* case if vendor create new calendar */
        if (empty($this->is_main) && empty($calendar_data['calendar_id'])) {
            
            list($main_calendar_id, $main_calendar_days) = $this->getMainCalendarDays();
            if (empty($calendar_data['weekend_days'])) {
                $calendar_data['weekend_days'] = array_keys($this->getWeekendDays($main_calendar_id));
            }
            
            $this->setMainDataForVendorCalendar($calendar_id, $main_calendar_days);

            $this->setMainExtraWorktimeForVendorCalendar($calendar_id);
        }
        
        /* update default weekends */
        if (!empty($calendar_id)) {
            $this->removeDefaultWeekends($calendar_id);

            if (!empty($calendar_data['weekend_days'])) {
                $this->setDefaultWeekends($calendar_data['weekend_days'], $calendar_id);
            }
        }
        
        return $calendar_id;
    }

    /*
    *   $day_data - array with day form data (popup form)
    */

    public function setDayData($day_data)
    {   
        if (empty($day_data['calendar_id'])) {

            unset($day_data['calendar_id']);
            $day_data['calendar_id'] = $this->setData($day_data);
        }
        if (!empty($day_data['reset_day']) && $day_data['reset_day'] == 'Y') {

            DayHelper::reset($day_data['calendar_id'], $day_data['day_timestamp']);
            return;

        }elseif (!empty($day_data['set_default_time']) && $day_data['set_default_time'] == 'Y') {
            
            list($day_data['start_time'], $day_data['end_time']) = $this->getDefultCalendarTime();
        }
        
        db_query("REPLACE INTO ?:cp_working_calendar_days ?e", $day_data);
    }

    /*
    *   $calendar_id - id of calendar
    *   $weekends - array of days keys
    */

    public function setDefaultWeekends($weekends, $calendar_id)
    {
        
        $weekend_data = [
            'calendar_id' => $calendar_id,
            'weekends' => implode(",", $weekends)
        ];
        db_query("REPLACE INTO ?:cp_working_calendar_weekend_days ?e", $weekend_data);
    }

    /*
    *   $month_days - array of month days 
    *   $weekend_days - array of weekends days
    */

    public function setWeekendsForMonthDays(&$month_days, $weekend_days) 
    {

        if (!empty($month_days)) {
            foreach ($month_days as $d_key => $day_data) {
                foreach ($weekend_days as $key => $day_description) {
                    if (mb_strtolower($day_description) === mb_strtolower($day_data['day_description'])) {
                        $month_days[$d_key]['default_weekend'] = true;
                    }
                }
            }
        }
    }

    public function setExtraWorktime($days)
    {   
        foreach ($days as $day_key => $day_data) {

            if ($this->asDefaultWotktime($day_data['start_time'], 'S') && $this->asDefaultWotktime($day_data['end_time'], 'E')) {
                unset($days[$day_key]);
            }
        }

        $serialize_days = serialize($days);
        
        db_query("UPDATE ?:cp_working_calendar SET extra_days_worktime = ?s WHERE company_id = ?i", $serialize_days, $this->company_id);
    }

    public function getData()
    {
        if ($this->is_main && !empty($company_id)) {
            return [];
        }
        /* This flag show is viewing admin calendar before create perconal calendar by vendor */
        $is_view_admin_calendar = false;

        $this->calendar_data = $this->getCalendarData();

        if (empty($this->calendar_data)) {
            $this->calendar_data = $this->getAdminCalendarData();
            $is_view_admin_calendar = true;
        }

        $this->calendar_data['days'] = $this->getDaysData();
        $this->calendar_data['weekend_days'] = $this->getWeekendDays();

        if ($is_view_admin_calendar && !empty($this->calendar_data['calendar_id'])) {
            unset($this->calendar_data['calendar_id']);
        }

        return $this->calendar_data;
    }

    /*
    *
    * $timestamp - int of selected month
    */
    public function getMonthDays($timestamp)
    {   
        list($days, $month_descriprion, $current_day_start_time) = $this->getDaysArray($timestamp);

        return [$days, $month_descriprion, $current_day_start_time];
    }

    public function removeDefaultWeekends($calendar_id)
    {
        db_query("DELETE FROM ?:cp_working_calendar_weekend_days WHERE calendar_id = ?i", $calendar_id);
    }

    public function getAdjacentTimes($month_days)
    {
        $last_day = end($month_days)['start_time'];

        // we can't use function 'current' because $month_days can contain empty days
        foreach ($month_days as $key => $day) {
            if (!isset($day['is_empty_day'])) {
                $first_day = $day['start_time'];
                break;
            }
        }

        if (!empty($first_day) && !empty($last_day)) {

            return [$first_day - SECONDS_IN_DAY, $last_day + SECONDS_IN_DAY];
        }
        return [];

    }

    public function getWeekendDays($calendar_id = 0)
    {   
        $calendar_id = !empty($calendar_id) ? $calendar_id : (!empty($this->calendar_data['calendar_id']) ? $this->calendar_data['calendar_id'] : 0); 
        if (!empty($calendar_id)) {

            $data = db_get_field("SELECT weekends FROM ?:cp_working_calendar_weekend_days WHERE calendar_id = ?i", $calendar_id);

            if (!empty($data)) {
                $data = explode(',', $data);
                foreach ($data as $day_key) {
                    $day_data[$day_key] = CalendarTypes::getDayDescByKey($day_key);
                }

            }
        }

        return !empty($day_data) ? $day_data : [];
    }
    
    private function setMainDataForVendorCalendar($calendar_id, $main_calendar_days)
    {
        if (!empty($calendar_id) && !empty($main_calendar_days)){

            foreach ($main_calendar_days as $day) {
                $day['calendar_id'] = $calendar_id;
                db_query("REPLACE INTO ?:cp_working_calendar_days ?e", $day);
            }
        }
        
    }
    private function setMainExtraWorktimeForVendorCalendar($calendar_id)
    {
        $extra_days_worktime = db_get_field("SELECT extra_days_worktime FROM ?:cp_working_calendar WHERE company_id = ?i", 0);

        if (!empty($extra_days_worktime)) {
            db_query("UPDATE ?:cp_working_calendar SET extra_days_worktime = ?s WHERE calendar_id = ?i", $extra_days_worktime, $calendar_id);
        }
    }

    private function getDefultCalendarTime()
    {
        $data = db_get_row("SELECT start_time, end_time FROM ?:cp_working_calendar WHERE company_id = ?i", $this->company_id);
        
        return [$data['start_time'], $data['end_time']];
    }

    private function getMainCalendarDays()
    {   
        $days = [];

        $main_calendar_id = db_get_field("SELECT calendar_id FROM ?:cp_working_calendar WHERE company_id = ?i", 0);

        if (!empty($main_calendar_id)) {
            $days = db_get_array("SELECT * FROM ?:cp_working_calendar_days WHERE calendar_id = ?i", $main_calendar_id);    
        }

        return [$main_calendar_id, $days];
    }

    private function getCalendarData()
    {   
        $data = db_get_row("SELECT * FROM ?:cp_working_calendar WHERE company_id = ?i", $this->company_id);

        if (!empty($data['extra_days_worktime'])) {
            $data['extra_days_worktime'] = unserialize($data['extra_days_worktime']);
        }

        return !empty($data) ? $data : [];
    }

    private function getAdminCalendarData()
    {   
        $data = db_get_row("SELECT * FROM ?:cp_working_calendar WHERE company_id = ?i", 0);
        
        return !empty($data) ? $data : [];
    }

    private function getDaysData()
    {
        if (!empty($this->calendar_data['calendar_id'])) {

            $fields = [
                'day_timestamp',
                'start_time as work_start',
                'end_time as work_end',
                'type' 
            ];

            $data = db_get_hash_array("SELECT " . implode(',', $fields) . " FROM ?:cp_working_calendar_days WHERE calendar_id = ?i", 'day_timestamp', $this->calendar_data['calendar_id']);
        }

        return !empty($data) ? $data : [];   
    }

    private function getDaysArray($timestamp)
    {   
        $days = [];
        $month_descriprion = "";
        $formatter = Tygh::$app['formatter'];
        $current_day_start_time = 1;

        $number_of_days = cal_days_in_month(CAL_GREGORIAN, $formatter->asDatetime($timestamp, "%m"), $formatter->asDatetime($timestamp, "%Y"));
        $month_descriprion = $formatter->asDatetime($timestamp, "%B %Y");

        if (!empty($number_of_days) && is_int($number_of_days)) {

            $current_day_number = $formatter->asDatetime($timestamp, "%d");
            $current_day_date = $formatter->asDatetime($timestamp, "%d/%m/%y");

            if (($formatter->asDatetime($timestamp, "%m/%y") == $formatter->asDatetime(time(), "%m/%y")))
            {
                $current_day_number = $formatter->asDatetime(time(), "%d");
                $current_day_date = $formatter->asDatetime(time(), "%d/%m/%y");

            }

            for ($i=1; $i <= $number_of_days ; $i++) {

                $date_format = date_create_from_format("d/m/y", Service::strReplaceOnce($current_day_number, $i, $current_day_date));
                $start_day_time = strtotime(date_format($date_format, 'm/d/y'));

                /* We check that the current day is equal to the iterator, while we view the current year and month */
                if ((int) $current_day_number === $i && ($formatter->asDatetime($timestamp, "%m/%y") == $formatter->asDatetime(time(), "%m/%y"))) {
                    $current_day_start_time = $start_day_time;
                }

                $day_description = $formatter->asDatetime($start_day_time, "%A");
                
                $days[$start_day_time] = [
                    'start_time' => $start_day_time, 
                    'day_number' => $formatter->asDatetime($start_day_time, "$i %b"),
                    'day_description' => $day_description,
                    'day_key' => CalendarTypes::getDayKeyByDesc($day_description)
                ];
                
                unset($start_day_time,$date_format);
            }

            $days = $this->addEmptyDaysBeforeMonday($days);
        }


        return [$days, $month_descriprion, $current_day_start_time];
    }

    private function getCalendarExtraWorktime($calendar_id) 
    {
        $extra_days_worktime = db_get_field("SELECT extra_days_worktime FROM ?:cp_working_calendar WHERE calendar_id = ?i", $calendar_id);

        return !empty($extra_days_worktime) ? $extra_days_worktime : '';
    }

    private function addEmptyDaysBeforeMonday($days)
    {
        $first_day_in_month = current($days);

        if (mb_strtolower($first_day_in_month['day_description']) !== self::FIRST_DAY_IN_WEEK) {
            $i = 0;
            foreach ($days as $day_data) {
                if (mb_strtolower($day_data['day_description']) === self::FIRST_DAY_IN_WEEK) {
                    break;
                }else {
                    $i++;
                }
            }

            $need_empty_days = self::DAYS_IN_WEEK - $i;
            $empty_days = [];

            for ($j=1; $j <= $need_empty_days ; $j++) { 
                $empty_days[$j] = [
                    'day_description' => __("cp_working_calendar.empty_day"),
                    'is_empty_day' => true
                ];
            }

            $days = $empty_days + $days;
        }

        return $days;
    }

    private function asDefaultWotktime($worktime, $type)
    {
        if ($type == 'S') {

            if (empty($this->start_time)) {

                $this->start_time = $default_time = db_get_field('SELECT start_time FROM ?:cp_working_calendar WHERE company_id = ?i', $this->company_id);

            }else {

                $default_time = $this->start_time;
            }

            

        }elseif ($type == 'E') {

            if (empty($this->end_time)) {

                $this->end_time = $default_time = db_get_field('SELECT end_time FROM ?:cp_working_calendar WHERE company_id = ?i', $this->company_id);
            }else {

                $default_time = $this->end_time;
            }
        }
        
        if ($worktime !== $default_time) {
            return false;
        }

        return true;
    }

}