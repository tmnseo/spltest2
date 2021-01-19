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

namespace Tygh\Addons\CpManageOldProducts\Products;

use Tygh\Addons\CpManageOldProducts\ServiceProvider;
use Tygh\Registry;
use Tygh\Languages\Languages;

class ProductManager 
{   
    private $deadline_time = 0;
    private $old_product_ids = [];

    public $count_removed_products = 0;

    public function __construct()
    {
        $this->deadline_time = time() - (ServiceProvider::productsDaysLifetime() * SECONDS_IN_DAY);
    }

    public function getOldProductIds()
    {   
        $this->old_product_ids = db_get_fields("SELECT product_id FROM ?:products WHERE updated_timestamp < ?i", $this->deadline_time);
    }

    public function removeOldProductIds()
    {
        foreach ($this->old_product_ids as $_pid) {
            if (fn_delete_product($_pid)) {
                $this->count_removed_products++ ;
            }

        }
    }

    public function setFeatureCondition($feature_variant, &$join, &$condition)
    {
        $feature_id = ServiceProvider::typeOfPartFeatureId();
        
        $variant_id = $this->getVariantByDescr($feature_variant);

        // If variant  does not exist we create condition 'variant_id = 0 . No product will be reset to zero

        if (!empty($feature_id)) {
            $join .= db_quote(" LEFT JOIN ?:product_features_values ON ?:products.product_id = ?:product_features_values.product_id");
            $condition .= db_quote(" AND ?:product_features_values.feature_id = ?i AND ?:product_features_values.variant_id = ?i", $feature_id, $variant_id);

            Registry::set('cp_feature_from_file_name', $feature_variant);
        } 
    }

    public function setFeature($product_id)
    {   
        $feature_id = ServiceProvider::typeOfPartFeatureId();
        
        $variant_id = $this->getVariantByDescr(Registry::get('cp_feature_from_file_name'));

        db_query("DELETE FROM ?:product_features_values WHERE feature_id = ?i AND product_id = ?i", $feature_id, $product_id);

        $feature_data = [
            'variant_id' => $variant_id,
            'product_id' => $product_id,
            'feature_id' => $feature_id  
        ];

        foreach (Languages::getAll() as $feature_data['lang_code'] => $v) {
            db_query("REPLACE INTO ?:product_features_values ?e ", $feature_data);
        }
        
    }

    private function getVariantByDescr($descr)
    {
        $variant_id = db_get_field("SELECT variant_id FROM ?:product_feature_variant_descriptions WHERE variant = ?s AND lang_code = ?s", $descr, CART_LANGUAGE);

        return !empty($variant_id) ? $variant_id : 0;
    }

}