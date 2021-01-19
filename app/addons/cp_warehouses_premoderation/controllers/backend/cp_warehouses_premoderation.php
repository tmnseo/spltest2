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
use Tygh\Addons\Warehouses\ServiceProvider;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $return_url = !empty($_REQUEST['return_url']) ? urldecode($_REQUEST['return_url']) : "store_locator.manage";

    if ($mode == 'approve') {
        
        if (!empty($_REQUEST['location_id'])) {
            $result = fn_cp_approve_warehouse($_REQUEST['location_id']);
        }

        if (!empty($result)) {
            fn_set_notification('N', __('notice'), __('successful'));
        }else {
            fn_set_notification('E', __('error'), __('error'));
        }

        $return_url = "cp_warehouses_premoderation.manage";

    }elseif ($mode == 'decline') {

        if (!empty($_REQUEST['cp_disapproval_data'])) {

            $result = fn_cp_disapprove_warehouse($_REQUEST['cp_disapproval_data']);
        }

        if (!empty($result)) {
            fn_set_notification('N', __('notice'), __('successful'));
        }else {
            fn_set_notification('E', __('error'), __('error'));
        }

        $return_url = "cp_warehouses_premoderation.manage";

    }elseif ($mode == 'store_locator_update') {

        $store_location_id = fn_cp_update_store_location($_REQUEST['store_location_data'], $_REQUEST['store_location_id'], DESCR_SL);

        if (empty($store_location_id)) {
            $suffix = ".manage";
        } else {
            $suffix = ".update?store_location_id=$store_location_id";
        }

        $return_url = 'cp_warehouses_premoderation' . $suffix;

    }elseif ($mode == 'delete') {
        if (!empty($_REQUEST['store_location_id'])) {
            fn_delete_store_location($_REQUEST['store_location_id']);
        }
    }

    if ($mode === 'm_delete') {
        if (!empty($_REQUEST['store_locator_ids'])) {
            foreach ($_REQUEST['store_locator_ids'] as $store_location_id) {
                fn_delete_store_location($store_location_id);
            }
        }
    }
    
    return array(CONTROLLER_STATUS_OK, $return_url);
}
if ($mode == 'manage') {
    
    $params = $_REQUEST;
    $params['store_status'] = 'P';
    if ($company_id = Registry::get('runtime.company_id')) {
        $params['company_id'] = Registry::get('runtime.company_id');
    }

    list($store_locations, $search) = fn_cp_get_store_locations($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    $raw_destinations = fn_get_destinations();
    $destinations = array_combine(array_column($raw_destinations, 'destination_id'), $raw_destinations);
    
    Tygh::$app['view']->assign([
        'sl_settings'     => fn_get_store_locator_settings(),
        'store_locations' => $store_locations,
        'destinations'    => $destinations,
        'search'          => $search,
    ]);
    /*warehouses add-on*/
    Tygh::$app['view']->assign('store_types', ServiceProvider::getStoreTypes());
    /*warehouses add-on*/
}elseif ($mode == 'update') {

    $store_location = fn_cp_get_store_location($_REQUEST['store_location_id'], DESCR_SL);
    if (empty($store_location)) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    if (isset($_REQUEST['add_all_destinations'])) {
        list($objects,) = fn_warehouses_get_destinations_for_picker(
            [
                'store_location_id' => $_REQUEST['store_location_id'],
                'destination_id'    => 0,
            ]);
        unset($store_location['shipping_destinations_ids']);
        foreach ($objects as $key => $object) {
            $store_location['shipping_destinations_ids'][$key] = $object['id'];
        }
    }


    Tygh::$app['view']->assign('store_location', $store_location);

    Registry::set('navigation.tabs', [
        'detailed' => [
            'title' => __('general'),
            'js' => true
        ],
        'addons' => [
            'title' => __('addons'),
            'js' => true
        ],
        'pickup' => [
            'title' => __('store_locator.pickup'),
            'js' => true
        ],
    ]);

    $destinations = fn_get_destinations(DESCR_SL);

    Tygh::$app['view']->assign([
        'destinations' => $destinations,
        'sl_settings'  => fn_get_store_locator_settings(),
        'states'       => fn_get_all_states(true, DESCR_SL),
    ]);

    /*warehouses*/
    Registry::set('navigation.tabs.pickup.title', __('warehouses.settings'));
    Tygh::$app['view']->assign('store_types', ServiceProvider::getStoreTypes());
    /*warehouses*/
}elseif ($mode == 'disapprove_manage') {

    $params = $_REQUEST;
    $params['store_status'] = 'F';
    if ($company_id = Registry::get('runtime.company_id')) {
        $params['company_id'] = Registry::get('runtime.company_id');
    }

    list($store_locations, $search) = fn_cp_get_store_locations($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    $raw_destinations = fn_get_destinations();
    $destinations = array_combine(array_column($raw_destinations, 'destination_id'), $raw_destinations);
    
    Tygh::$app['view']->assign([
        'sl_settings'     => fn_get_store_locator_settings(),
        'store_locations' => $store_locations,
        'destinations'    => $destinations,
        'search'          => $search,
    ]);
    /*warehouses add-on*/
    Tygh::$app['view']->assign('store_types', ServiceProvider::getStoreTypes());
    /*warehouses add-on*/
}
