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
use Tygh\Addons\CpMatrixDestinations\ServiceProvider;
use Tygh\Addons\CpMatrixDestinations\Model;
use Tygh\Addons\CpMatrixDestinations\Settings\Settings;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
}

else{
    if($mode == 'install'){
        $settings_data['settings_id'] ='cp_edost_counter';
        $settings_data['value'] = 0;
        Settings::installSettingsData($settings_data);
        exit('done');
        
    }

    if($mode == 'manage'){
        Tygh::$app['view']->assign([
            'cp_matrix_settings'=> Settings::getSettings()
        ]);
    }


    if($mode =='installfirstdata'){

        $service = ServiceProvider::getService();

        $service->installFirstdata();

        exit('done');

    }
}