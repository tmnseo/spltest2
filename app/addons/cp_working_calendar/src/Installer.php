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

namespace Tygh\Addons\CpWorkingCalendar;

use Tygh\Addons\InstallerInterface;
use Tygh\Core\ApplicationInterface;
 
/**
 *
 * @package Tygh\Addons\CpWorkingCalendar
 */
class Installer implements InstallerInterface
{
    /**
     * @inheritDoc
     */
    public static function factory(ApplicationInterface $app)
    {
        return new self();
    }

    /**
     * @inheritDoc
     */
    public function onInstall()
    {
        db_query("DROP TABLE IF EXISTS ?:cp_working_calendar");
        db_query("DROP TABLE IF EXISTS ?:cp_working_calendar_days");
        db_query("DROP TABLE IF EXISTS ?:cp_working_calendar_weekend_days");
        db_query("
            CREATE TABLE ?:cp_working_calendar (
                `calendar_id` int(11) auto_increment,
                `company_id` mediumint(8) NOT NULL default '0',
                `start_time` varchar (16) NOT NULL default '0',
                `end_time` varchar (16) NOT NULL default '0',
                `extra_days_worktime` text NOT NULL DEFAULT '',
                PRIMARY KEY  (`calendar_id`),
                UNIQUE KEY (`company_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET UTF8;"
        );
        db_query("
            CREATE TABLE ?:cp_working_calendar_days (
                `day_timestamp` int(11) NOT NULL default '0',
                `calendar_id` mediumint(8) NOT NULL default '0',
                `start_time` varchar (16) NOT NULL default '0',
                `end_time` varchar (16) NOT NULL default '0',
                `type` char(1) NOT NULL default 'W',
                PRIMARY KEY  (`day_timestamp`,`calendar_id`),
                KEY (`calendar_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET UTF8;"
        );
        db_query("
            CREATE TABLE ?:cp_working_calendar_weekend_days (
                `calendar_id` mediumint(8) NOT NULL default '0',
                `weekends` varchar (128) NOT NULL default '',
                PRIMARY KEY  (`calendar_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET UTF8;"
        );
    }

    /**
     * @inheritDoc
     */
    public function onUninstall()
    {
        db_query("DROP TABLE IF EXISTS ?:cp_working_calendar");
        db_query("DROP TABLE IF EXISTS ?:cp_working_calendar_days");
        db_query("DROP TABLE IF EXISTS ?:cp_working_calendar_weekend_days");
    }

    /**
     * @inheritDoc
     */
    public function onBeforeInstall()
    {

    }
}