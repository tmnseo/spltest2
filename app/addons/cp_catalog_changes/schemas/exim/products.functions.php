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

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Registry;

function fn_cp_catalog_changes_get_exim_product_code(array &$alt_keys, &$object, &$skip_get_primary_object_id)
{   
    if (empty($object['product_code'])) {
        return false;
    }
    
    $company_name =  !empty($object['company']) ? $object['company'] : '';
    
    if (empty($company_name)) {
        $skip_get_primary_object_id = false;
        return false;
    }

    $company_name = trim($company_name);
    
    if (Registry::get('runtime.company_id')) {
        $company_id = Registry::get('runtime.company_id');
    } else {
        $company_id = fn_get_company_id_by_name($company_name);

        if (!$company_id) {
            if (!empty($processed_data)) {
                $processed_data['C']++;
            }

            $company_data = array('company' => $company_name, 'email' => '', 'status' => 'A');
            $company_id = fn_update_company($company_data, 0);
        }
    }
    
    if (!empty($company_id)) {
        $object['product_code'] = $alt_keys['product_code'] = CP_CATALOG_CHANGES_VENDOR_PREFIX . $company_id . '-' . $object['product_code'];
    }
}