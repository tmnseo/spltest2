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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'view_all') {
    $variants = Tygh::$app['view']->getTemplateVars('variants');
    $filter_id = !empty($_REQUEST['filter_id']) ? $_REQUEST['filter_id'] : 0;

    if (empty($filter_id)) {
        return;
    }
    $feature_id = db_get_field("SELECT feature_id FROM ?:product_filters WHERE filter_id = ?i",$filter_id);
    
    if (!empty($feature_id)) {
        list($cp_variants, $cp_variants_search) = fn_cp_get_all_variants($feature_id, $_REQUEST);
        
        Tygh::$app['view']->assign('variants', $cp_variants);
        Tygh::$app['view']->assign('search', $cp_variants_search);
    }
    
    
}