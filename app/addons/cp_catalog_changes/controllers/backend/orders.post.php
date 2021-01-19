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

@set_time_limit(0);

/*
$variant_ids = db_get_fields("SELECT variant_id FROM ?:product_feature_variants WHERE feature_id = ?i", 561);
db_query("DELETE FROM ?:product_feature_variants WHERE variant_id IN (?n)", $variant_ids);
db_query("DELETE FROM ?:product_feature_variant_descriptions WHERE variant_id IN (?n)", $variant_ids);
*/

/*
$company_id = 20;
$product_ids = db_get_fields("SELECT product_id FROM ?:products WHERE company_id = ?i", $company_id);
//fn_print_die(count($product_ids));
if (!empty($product_ids)) {
    foreach ($product_ids as $pid) {
        fn_delete_product($pid);
    }
}

fn_print_die(count($product_ids));
*/
