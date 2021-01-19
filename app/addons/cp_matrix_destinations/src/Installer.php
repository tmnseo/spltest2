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

namespace Tygh\Addons\CpMatrixDestinations;

use Tygh\Addons\InstallerInterface;
use Tygh\Core\ApplicationInterface;
use Tygh\Addons\CpMatrixDestinations\Service;


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
        
        $res = db_get_row("SELECT * FROM ?:warehouses_products_amount LIMIT 1");
        if(!isset($res['city_id'])){
            db_query(" ALTER TABLE `?:warehouses_products_amount`
            ADD COLUMN `city_id` int(11) unsigned NOT NULL DEFAULT '0',
            ADD KEY `city_id` (`city_id`);");
        }
        
        $matrix_model = ServiceProvider::getMatrix();
        $matrix_model->updateWarehouseAmountsetCityId(0);
        
        $city_model = ServiceProvider::getCity();
        $city_model->installDemoData();


        $settings_data['settings_id'] ='cp_edost_counter';
        $settings_data['value'] = 0;
        Settings::installSettingsData($settings_data);
        
    }

    /**
     * @inheritDoc
     */
    public function onUninstall()
    {

    }

    /**
     * @inheritDoc
     */
    public function onBeforeInstall()
    {

    }
}
