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
use Tygh\Addons\CpBrandsLocations\ServiceProvider;
use Tygh\Addons\CpBrandsLocations\Location\LocationManager;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    return; 

}
if ($mode == 'get_current_locations') {
    
    if (defined('AJAX_REQUEST')) {
        if (!empty($_REQUEST['company_id']) && !empty($_REQUEST['variant_id'])) {

            $raw_destinations = fn_get_destinations();
            $destinations = array_combine(array_column($raw_destinations, 'destination_id'), $raw_destinations);

            $cp_selected_destinations = LocationManager::getVendorDestinationsForBrand($_REQUEST['company_id'], $_REQUEST['variant_id']);

            Tygh::$app['view']->assign([
                'destinations' => $destinations,
                'cp_selected_destinations' => $cp_selected_destinations,
            ]);

            Tygh::$app['view']->display('views/companies/update.tpl');
        }

        exit;
    }
}
