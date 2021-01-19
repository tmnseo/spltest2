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

    if ($mode == 'add_brand_locations') {
            
        $company_id = !empty($_REQUEST['company_id']) ? $_REQUEST['company_id'] : 0;
        $section = !empty($_REQUEST['selected_section']) ? $_REQUEST['selected_section'] : '';

        if (!empty($_REQUEST['brand_data'])) {
            LocationManager::addBrandLocations($_REQUEST['brand_data']);
        }

        return array(CONTROLLER_STATUS_OK, 'companies_update&company_id=' . $company_id . '&selected_section=' . $section);
    }

    return; 

}
if ($mode == 'update') {

    if (!empty($_REQUEST['company_id'])) {

        $brand_feature_id = ServiceProvider::brandFeatureId();

        if (!empty($brand_feature_id)) {
            Registry::set('navigation.tabs.cp_brands_locations', array (
                'title' => __('cp_brands_locations.tab_title'),
                'js' => true
            ));

            $raw_destinations = fn_get_destinations();
            $destinations = array_combine(array_column($raw_destinations, 'destination_id'), $raw_destinations);

            $params = $_REQUEST;

            list($cp_brands_locations, $search) = LocationManager::getVendorBrandsLocations($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

            Tygh::$app['view']->assign([
                'destinations' => $destinations,
                'cp_brands_locations' => $cp_brands_locations,
            ]);
        }
    }
}
