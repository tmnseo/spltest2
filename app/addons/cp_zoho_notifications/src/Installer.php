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

namespace Tygh\Addons\CpZohoNotifications;

use Tygh\Addons\InstallerInterface;
use Tygh\Core\ApplicationInterface;
 
/**
 * This class describes the instractions for installing and uninstalling the product_variations add-on
 *
 * @package Tygh\Addons\CpMatrixDestinations
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
        db_query("DROP TABLE IF EXISTS ?:cp_order_statuses_log");
        db_query("CREATE TABLE ?:cp_order_statuses_log(
                order_id mediumint(8) unsigned NOT NULL,
                status char(1) NOT NULL,
                update_timestamp int(11) unsigned NOT NULL default '0',
                type char (1) NOT NULL DEFAULT 'Z',
                PRIMARY KEY (order_id, type),
                INDEX status (status)
            ) ENGINE=MyISAM DEFAULT CHARSET UTF8"
        );
    }

    /**
     * @inheritDoc
     */
    public function onUninstall()
    {
        db_query("DROP TABLE IF EXISTS ?:cp_order_statuses_log");
    }

    /**
     * @inheritDoc
     */
    public function onBeforeInstall()
    {

    }
}