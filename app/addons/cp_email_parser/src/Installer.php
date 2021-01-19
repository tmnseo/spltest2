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

namespace Tygh\Addons\CpEmailParser;

use Tygh\Addons\InstallerInterface;
use Tygh\Core\ApplicationInterface;
 
/**
 * This class describes the instractions for installing and uninstalling the product_variations add-on
 *
 * @package Tygh\Addons\CpEmailParser
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
        $res = db_get_row("SELECT * FROM ?:companies LIMIT 1");
        if(!isset($res['email_for_parser'])){
            db_query(" ALTER TABLE `?:companies`
            ADD COLUMN `email_for_parser` varchar(128) NOT NULL default '';");
        }
        db_query("CREATE TABLE IF NOT EXISTS `?:cp_email_parser_log` (
            `log_id` mediumint(8) unsigned NOT NULL auto_increment,
            `start_time` int(11) unsigned NOT NULL default 0,
            `time` int(11) unsigned NOT NULL default 0,
            `status` char(1) NOT NULL default 'S',
            `data` TEXT NOT NULL default '',
            `type` char(1) NOT NULL default 'M',
            `parent_log_id` int(11) NOT NULL default '0',
            PRIMARY KEY  (`log_id`),
            KEY `time`(time),
            KEY `type`(type)
            ) Engine=MyISAM DEFAULT CHARSET UTF8;");
    }

    /**
     * @inheritDoc
     */
    public function onUninstall()
    {
        $res = db_get_row("SELECT * FROM ?:companies LIMIT 1");

        if(isset($res['email_for_parser'])){
            db_query(" ALTER TABLE `?:companies`
            DROP COLUMN `email_for_parser`;");
        }

        db_query("DROP TABLE IF EXISTS `?:cp_email_parser_log`");
    }

    /**
     * @inheritDoc
     */
    public function onBeforeInstall()
    {

    }
}