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
use Tygh\Addons\CpWorkingCalendar\Calendar\Calendar;
use Tygh\Addons\CpWorkingCalendar\Calendar\DayHelper;
use Tygh\Addons\CpWorkingCalendar\Calendar\CalendarHelper;
use Tygh\Addons\CpWorkingCalendar\Calendar\CalendarTypes;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $suffix = '';

    if ($mode == 'update') {
        
        $calendar_data = !empty($_REQUEST['calendar_data']) ? $_REQUEST['calendar_data'] : [];

        if (!empty($calendar_data)) {

            $calendar_data['main_calendar'] = !empty($calendar_data['main_calendar']) ? $calendar_data['main_calendar'] : 0;

            $calendar = new Calendar($calendar_data['main_calendar'], $calendar_data['company_id']);
            $calendar_id = $calendar->setData($calendar_data);
        }

        if (!empty($calendar_data['main_calendar'])) {
            $suffix .= ".update?main_calendar=1";
        }elseif (!empty($calendar_data['company_id'])) {
            $suffix .= ".update?company_id=" . $calendar_data['company_id'];
        }

    }elseif ($mode == 'update_day') {

        $day_data = !empty($_REQUEST['day']) ? $_REQUEST['day'] : [];
        
        if (!empty($day_data)) {

            $day_data['main_calendar'] = !empty($day_data['main_calendar']) ? $day_data['main_calendar'] : 0;

            $calendar = new Calendar($day_data['main_calendar'], $day_data['company_id']);
            $calendar->setDayData($day_data);
        }

        if (!empty($day_data['main_calendar'])) {
            $suffix .= ".update?main_calendar=1";
        }elseif (!empty($day_data['company_id'])) {
            $suffix .= ".update?company_id=" . $day_data['company_id'];
        }

    }elseif ($mode == 'reset_days') {

        if (!empty($_REQUEST['calendar_id'])) {
            DayHelper::reset($_REQUEST['calendar_id']);
        }

    }elseif ($mode == 'delete') {

        if (!empty($_REQUEST['calendar_id'])) {
            CalendarHelper::delete($_REQUEST['calendar_id']);
        }

        if (!empty(Registry::get('runtime.company_id'))) {
            $suffix .= '.update';
        }

    }elseif ($mode == 'update_extra_worktime') {
        
        $params = $_REQUEST;

        if (!empty($params['calendar_id']) && isset($params['company_id']) && !empty($params['days'])) {

            $params['main_calendar'] = !empty($params['main_calendar']) ? $params['main_calendar'] : 0;
            
            $calendar = new Calendar($params['main_calendar'], $params['company_id']);
            $calendar->setExtraWorktime($params['days']);       
        }
    }

    /* redirect params */
    if (!empty($_REQUEST['main_calendar'])) {

        $suffix .= ".update?main_calendar=1";

    }elseif (!empty($_REQUEST['company_id'])) {
        
        $suffix .= ".update?company_id=" . $_REQUEST['company_id'];
    }

    if (empty($suffix)) {
        $suffix = '.manage';
    }

    if (!empty($_REQUEST['selected_month_time'])) {
        $suffix .= "&selected_month_time=" . $_REQUEST['selected_month_time'];
    }

    return [CONTROLLER_STATUS_OK, 'cp_working_calendar' . $suffix];
}

if ($mode == 'manage') {

    $company_id = Registry::get('runtime.company_id');

    if (!empty($company_id)) {
        return [CONTROLLER_STATUS_DENIED];
    }

    $params = $_REQUEST;

    list($calendars, $search) = CalendarHelper::getVendorCalendars($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    
    Tygh::$app['view']->assign([
        'calendars' => $calendars,
        'weekdays' => CalendarTypes::getWeekDays(),
        'search' => $search
    ]);

}elseif ($mode == 'update') {

    $main_calendar = false;
    $company_id = Registry::get('runtime.company_id');
    $params = $_REQUEST;

    if (!empty($company_id) && !empty($params['company_id']) && $company_id != $params['company_id']) {
        return [CONTROLLER_STATUS_DENIED];
    }   

    $params['company_id'] = !empty($params['company_id']) ? $params['company_id'] : $company_id;

    if (!empty($params['main_calendar'])) {
        $main_calendar = true;   
    }

    $calendar = new Calendar($main_calendar, $params['company_id']);
    $calendar_data = $calendar->getData();
    
    $timestamp = !empty($params['selected_month_time']) ? $params['selected_month_time'] : time();

    list($month_days, $month_description, $current_day_start_time) = $calendar->getMonthDays($timestamp);

    $week_days = CalendarTypes::getWeekDays();

    if (!empty($calendar_data['weekend_days'])) {
        $week_days = array_diff($week_days, $calendar_data['weekend_days']);
        $calendar->setWeekendsForMonthDays($month_days, $calendar_data['weekend_days']);
    }

    list($prev_month_time, $next_month_time) = $calendar->getAdjacentTimes($month_days);

    Tygh::$app['view']->assign([
        'company_id' => $company_id,
        'main_calendar' => $main_calendar,
        'calendar_data' => $calendar_data,
        'current_month_days' => $month_days,
        'month_description' => $month_description,
        'current_day_start_time' => $current_day_start_time,
        'week_days' => $week_days,
        'prev_month_time' => $prev_month_time,
        'next_month_time' => $next_month_time,
        'month_time' => $timestamp
    ]);
}elseif ($mode == 'day_popup') {

    $params = $_REQUEST;
    
    $day_data = DayHelper::getDayData($params['day_start_time'], $params['calendar_id']);

    Tygh::$app['view']->assign('params', $params);
    Tygh::$app['view']->assign('selected_day_data', $day_data);
    Tygh::$app['view']->display('addons/cp_working_calendar/components/calendar_day_popup.tpl');

    exit;

}elseif ($mode == 'extra_time_popup') {

    $params = $_REQUEST;

    if (!empty($params['calendar_id'])) {
        $calendar_data = CalendarHelper::getWorkTime($params['calendar_id']);

        Tygh::$app['view']->assign('calendar_data', $calendar_data);
        Tygh::$app['view']->assign('params', $params);
        Tygh::$app['view']->assign('week_days', CalendarTypes::getWeekDays());
        Tygh::$app['view']->display('addons/cp_working_calendar/components/extra_time_popup.tpl');
    }

    exit;
}