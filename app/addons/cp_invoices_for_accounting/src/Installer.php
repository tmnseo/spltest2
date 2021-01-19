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

namespace Tygh\Addons\CpInvoicesForAccounting;

use Tygh\Addons\InstallerInterface;
use Tygh\Core\ApplicationInterface;
 
/**
 * This class describes the instractions for installing and uninstalling the product_variations add-on
 *
 * @package Tygh\Addons\CpInvoicesForAccounting
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
        $res = db_get_row("SELECT * FROM ?:orders LIMIT 1");
        if(!isset($res['cp_was_download_for_accounting'])){
            db_query(" ALTER TABLE `?:orders`
            ADD COLUMN `cp_was_download_for_accounting` char(1) NOT NULL default 'N';");
        }
    }

    /**
     * @inheritDoc
     */
    public function onUninstall()
    {
        $res = db_get_row("SELECT * FROM ?:orders LIMIT 1");
        if(isset($res['cp_was_download_for_accounting'])){
            db_query(" ALTER TABLE `?:orders`
            DROP COLUMN `cp_was_download_for_accounting`;");
        }
    }

    /**
     * @inheritDoc
     */
    public function onBeforeInstall()
    {

    }
}