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

namespace Tygh\Addons\CpBrandsLocations;

use Tygh\Addons\InstallerInterface;
use Tygh\Core\ApplicationInterface;
 
/**
 * This class describes the instractions for installing and uninstalling the product_variations add-on
 *
 * @package Tygh\Addons\CpBrandsLocations
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
        db_query("DROP TABLE IF EXISTS ?:cp_brands_locations");

        db_query(
            "CREATE TABLE ?:cp_brands_locations (
                brand_variant_id mediumint(8) unsigned not null default 0,
                company_id mediumint(8) unsigned not null default 0,
                destination_id mediumint(8) unsigned not null default 0,
                PRIMARY KEY (brand_variant_id, company_id, destination_id),
                KEY `company_id` (`company_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=UTF8"
        );
    }

    /**
     * @inheritDoc
     */
    public function onUninstall()
    {
        db_query("DROP TABLE IF EXISTS ?:cp_brands_locations");
    }

    /**
     * @inheritDoc
     */
    public function onBeforeInstall()
    {

    }
}