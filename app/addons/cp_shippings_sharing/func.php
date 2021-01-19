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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/*HOOKS*/
/*HOOKS*/
function fn_cp_get_used_shippings($company_id)
{
    $used_shippings = db_get_field("SELECT cp_used_shippings FROM ?:companies WHERE company_id = ?i",$company_id);

    return !empty($used_shippings) ? explode(',', $used_shippings) : null;
}
function fn_cp_change_shipping_used_status($params, $company_id)
{
    if ($params['status'] == 'Y') {
        $result = db_query("UPDATE ?:companies SET cp_used_shippings = ?p WHERE company_id = ?i", fn_add_to_set('cp_used_shippings', $params['storefront_id']), $company_id);
    }elseif ($params['status'] == 'N') {
        $result = db_query("UPDATE ?:companies SET cp_used_shippings = ?p WHERE company_id = ?i", fn_remove_from_set('cp_used_shippings', $params['storefront_id']), $company_id);
    }
    
    return !empty($result) ? $result : null; 
}