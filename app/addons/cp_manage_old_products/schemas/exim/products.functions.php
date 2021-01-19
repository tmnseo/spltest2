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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Addons\CpManageOldProducts\Products\ExportProductManager;
use Tygh\Addons\CpManageOldProducts\Products\ProductManager;
use Tygh\Addons\CpManageOldProducts\Service;
use Tygh\Addons\CpManageOldProducts\ServiceProvider;
use Tygh\Registry;

function fn_cp_manage_old_products_exim_post_processing($primary_object_ids, $preset)
{   
     
    $company_id = !empty($preset['company_id']) ? $preset['company_id'] : null;
    $exported_product_ids = [];

    if (empty($company_id)) {
        return;
    }
    if (Registry::isExist('cp_feature_from_file_name')) {
        Registry::del('cp_feature_from_file_name');
    }

    $product_manager = new ExportProductManager($company_id);
    $product_manager->getNotExportedProductIds();
    $product_manager->setZeroInventoryForNotExportedProducts();
}

function fn_cp_manage_old_products_exim_pre_processing($preset) 
{
    $company_id = !empty($preset['company_id']) ? $preset['company_id'] : null;

    if (empty($company_id)) {
        return;
    }

    $condition = db_quote(" company_id = ?i", $company_id);
    $join = "";

    if (!empty($preset['file'])) {
        $feature = Service::getFeatureFromFileName($preset['file']);
    }

    if (!empty($feature)) {

        $product_manager = ServiceProvider::getProductManager();
        $product_manager->setFeatureCondition($feature, $join, $condition);
    }
    
    db_query("UPDATE ?:products $join SET cp_was_imported = ?s WHERE $condition", 'N');

}

function fn_cp_manage_old_products_exim_process($primary_object_id)
{   
    if (!empty($primary_object_id['product_id'])) {

        db_query("UPDATE ?:products SET cp_was_imported = ?s WHERE product_id = ?i", 'Y', $primary_object_id['product_id']);

        if (Registry::isExist('cp_feature_from_file_name')) {

            $product_manager = ServiceProvider::getProductManager();
            $product_manager->setFeature($primary_object_id['product_id']);
        }
    }
}