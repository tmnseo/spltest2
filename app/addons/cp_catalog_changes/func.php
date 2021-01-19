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
use Tygh\Enum\ProductFeatures;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_settings_variants_addons_cp_catalog_changes_original_brand()
{
    return fn_cp_catalog_changes_get_features_list();
}

function fn_settings_variants_addons_cp_catalog_changes_original_article()
{
    return fn_cp_catalog_changes_get_features_list();
}

function fn_settings_variants_addons_cp_catalog_changes_manufacturer_article()
{
    return fn_cp_catalog_changes_get_features_list();
}

function fn_cp_catalog_changes_get_features_list()
{
    $subfeture_prefix = ' ---- ';
    
    $feature_list = [];
    
    $params = [
        'get_descriptions' => true,
        'feature_types' => [ProductFeatures::TEXT_SELECTBOX, ProductFeatures::MULTIPLE_CHECKBOX, ProductFeatures::EXTENDED],
        'plain' => false,
    ];
    
    $params = [
        'get_descriptions' => true,
        'feature_types' => [
                            ProductFeatures::TEXT_SELECTBOX,
                            ProductFeatures::MULTIPLE_CHECKBOX,
                            ProductFeatures::TEXT_FIELD,
                            ProductFeatures::TEXT_FIELD,
                            ProductFeatures::NUMBER_FIELD,
                            ],
        'plain' => false,
    ];
    
    list($features, ) = fn_get_product_features($params, 0, DESCR_SL);
    
    if (is_array($features)) {
        foreach ($features as $k => $v) {
            if ($v['feature_type'] == ProductFeatures::GROUP) {
                if (!empty($v['subfeatures'])){
                    $feature_list[$k] = $v['description'] . fn_cp_catalog_changes_format_feature_status_suffix($v['status']);
                    foreach ($v['subfeatures'] as $_k => $_v){
                        $feature_list[$_k] = $subfeture_prefix . $_v['description'] . fn_cp_catalog_changes_format_feature_status_suffix($v['status']);
                    }
                }
            } else {
                $feature_list[$k] = $v['description'] . fn_cp_catalog_changes_format_feature_status_suffix($v['status']);
            }
        }
    }
    
    return $feature_list;
}

function fn_cp_catalog_changes_format_feature_status_suffix($status)
{
    return ($status != 'A') ? ' ' . __("addons.cp_catalog_changes.off") : '';
}

function fn_cp_catalog_changes_get_products_pre(&$params, $items_per_page, $lang_code)
{
    if (AREA != 'C') {
        return;
    }
    
    if (isset($params['q']) && fn_string_not_empty($params['q']) && (empty($params['is_pname_search']) || $params['is_pname_search'] !== 'Y')) {
        $params['art_q'] = $params['q'];
        unset($params['q']);
        unset($params['pcode']);
        
        $params['custom_extend'][] = 'full_description';
        $params['custom_extend'][] = 'product_name';
    }
    
    if (isset($params['search_q']) && fn_string_not_empty($params['search_q']) && (empty($params['is_pname_search']) || $params['is_pname_search'] !== 'Y')) {
        $params['art_q'] = $params['search_q'];
        unset($params['search_q']);
        unset($params['pcode']);
        
        $params['custom_extend'][] = 'full_description';
        $params['custom_extend'][] = 'product_name';
    }

    if (!empty($params['is_pname_search']) && $params['is_pname_search'] === 'Y') {
        unset($params['art_q']);
        unset($params['pcode_from_q']);
        
        $params['pshort'] = 'Y';
        $params['pfull'] = 'Y';
    }
    
    //search_q
    
}

function fn_cp_catalog_changes_get_products_before_select (&$params, &$join, &$condition, &$u_condition, &$inventory_join_cond, &$sortings, &$total, &$items_per_page, &$lang_code, &$having)
{
    if (AREA != 'C' || !empty($params['cp_live_search'])) {
        return;
    }
    
    //$join .= db_quote(" LEFT JOIN ?:product_features_values as m_pfv ON m_pfv.product_id = products.product_id AND m_pfv.lang_code = ?s AND m_pfv.feature_id = ?i", $lang_code, $manufacturer_article_id);     
    //$join .= db_quote(" LEFT JOIN ?:product_feature_variant_descriptions as m_pfvd ON m_pfvd.variant_id = m_pfv.variant_id AND m_pfvd.lang_code = ?s", $lang_code);
    if (isset($params['art_q']) && fn_string_not_empty($params['art_q'])) {
            
            $original_brand_id = Registry::get('addons.cp_catalog_changes.original_brand');
            $original_article_id = Registry::get('addons.cp_catalog_changes.original_article');
            $manufacturer_article_id = Registry::get('addons.cp_catalog_changes.manufacturer_article');
        
            /*
            $join .= db_quote(" INNER JOIN ?:product_features_values as pfv ON pfv.product_id = products.product_id AND pfv.lang_code = ?s AND pfv.feature_id = ?i", $lang_code, $feature_id);     
            $join .= db_quote(" LEFT JOIN ?:product_feature_variant_descriptions as pfvd ON pfvd.variant_id = pfv.variant_id AND pfvd.lang_code = ?s", $lang_code);
            */
            
            $join .= db_quote(" LEFT JOIN ?:product_features_values as o_pfv ON o_pfv.product_id = products.product_id AND o_pfv.lang_code = ?s AND o_pfv.feature_id = ?i", $lang_code, $original_article_id);     
            $join .= db_quote(" LEFT JOIN ?:product_feature_variant_descriptions as o_pfvd ON o_pfvd.variant_id = o_pfv.variant_id AND o_pfvd.lang_code = ?s", $lang_code);
            
            $join .= db_quote(" LEFT JOIN ?:product_features_values as m_pfv ON m_pfv.product_id = products.product_id AND m_pfv.lang_code = ?s AND m_pfv.feature_id = ?i", $lang_code, $manufacturer_article_id);     
            $join .= db_quote(" LEFT JOIN ?:product_feature_variant_descriptions as m_pfvd ON m_pfvd.variant_id = m_pfv.variant_id AND m_pfvd.lang_code = ?s", $lang_code);
            
            //$condition .= db_quote(" AND pfvd.variant LIKE ?l", '%' . $params['art_q'] . '%');
            //$condition .= db_quote(" AND pfvd.variant = ?s", $params['art_q']);
            
            $search_q = fn_cp_catalog_changes_convert_search_q($params['art_q']);
            
            if (!empty($params['cp_np_use_like']) || !empty($params['cp_np_search_run'])) {
                $condition .= db_quote(" AND ( o_pfvd.variant LIKE ?l OR lower(o_pfvd.cp_search_variant) LIKE ?l)", '%' . $params['art_q'] . '%', '%' . $search_q . '%');
            } else{
                $condition .= db_quote(" AND ( o_pfvd.variant = ?s OR lower(o_pfvd.cp_search_variant) = ?s)", $params['art_q'], $search_q);
            }
            
            if (!empty($params['cp_product_type'])) {
                if ($params['cp_product_type'] == 'O') {
                    $condition .= db_quote(" AND o_pfvd.variant = m_pfvd.variant");
                }
                if ($params['cp_product_type'] == 'A') {
                    $condition .= db_quote(" AND o_pfvd.variant != m_pfvd.variant");
                }
            }
    }
    
}

function fn_cp_catalog_changes_advanced_import_set_product_features($product_id, $features_list, $variants_delimiter = '///')
{
    if (!$features_list || !is_array($features_list)) {
        return;
    }

    static $features_cache = array();

    /** @var \Tygh\Addons\AdvancedImport\FeaturesMapper $features_mapper */
    $features_mapper = Tygh::$app['addons.advanced_import.features_mapper'];

    $main_lang = $features_mapper->getMainLanguageCode($features_list);
    
    fn_set_hook('advanced_import_set_product_features_before', $product_id, $features_list, $features_mapper, $variants_delimiter, $main_lang);
    
    $features_list = $features_mapper->remap($features_list, $variants_delimiter);

    if ($missing_features = array_diff(array_keys($features_list), array_keys($features_cache))) {
        $features_cache += db_get_hash_array(
            'SELECT feature_id, company_id, feature_type AS type FROM ?:product_features WHERE feature_id IN (?n)',
            'feature_id',
            $missing_features
        );
    }

    foreach ($features_list as $feature_id => &$feature) {
        $feature = array_merge($feature, $features_cache[$feature_id]);
    }
    unset($feature);
    
    if ($features_list) {
        return fn_exim_save_product_features_values($product_id, $features_list, $main_lang, false);
    }

    return [];
}

function fn_cp_catalog_changes_advanced_import_set_product_features_before($product_id, &$features_list, $features_mapper, $variants_delimiter, $main_lang)
{
    $original_brand_id = Registry::get('addons.cp_catalog_changes.original_brand');
    $original_article_id = Registry::get('addons.cp_catalog_changes.original_article');
    $manufacturer_article_id = Registry::get('addons.cp_catalog_changes.manufacturer_article');
    
    if (empty($features_list[$main_lang][$original_brand_id])) {
        $features_list[$main_lang][$original_brand_id] = __('addons.cp_catalog_changes.not_set');
    }
    else {
        $variants = explode($variants_delimiter, $features_list[$main_lang][$original_brand_id]);
        foreach ($variants as &$v) {
            $v = trim($v);
        }
        $features_list[$main_lang][$original_brand_id] = implode($variants_delimiter, $variants);
    }
    
    if (empty($features_list[$main_lang][$original_article_id])) {
        if (!empty($features_list[$main_lang][$manufacturer_article_id])) {
            $features_list[$main_lang][$original_article_id] = $features_list[$main_lang][$manufacturer_article_id];
        }
    }
    else {
        $variants = explode($variants_delimiter, $features_list[$main_lang][$original_article_id]);
        foreach ($variants as &$v) {
            $v = trim($v);
        }
        
        // add manufacturer code to analog feature list
        if (!empty($features_list[$main_lang][$manufacturer_article_id])) {
            $m_variants = explode($variants_delimiter, $features_list[$main_lang][$manufacturer_article_id]);
            foreach ($m_variants as &$v) {
                $v = trim($v);
            }
            $variants = array_merge($variants, $m_variants);
            $variants = array_unique($variants);
        }
        
        $features_list[$main_lang][$original_article_id] = implode($variants_delimiter, $variants);
    }
}

function fn_cp_catalog_changes_get_products($params, &$fields, $sortings, &$condition, &$join, $sorting, &$group_by, $lang_code, $having)
{      
    if (AREA == 'C' && !empty($params['cp_np_use_like']) && isset($params['art_q']) && fn_string_not_empty($params['art_q'])) {
        $fields[] = 'o_pfvd.variant as cp_np_manuf_code';
    }
    return false;
    
    if (AREA != 'C' || empty($params['cp_live_search'])) {
        return false;
    }
    
    if (isset($params['art_q']) && fn_string_not_empty($params['art_q'])) {
            
            $original_brand_id = Registry::get('addons.cp_catalog_changes.original_brand');
            $original_article_id = Registry::get('addons.cp_catalog_changes.original_article');
            $manufacturer_article_id = Registry::get('addons.cp_catalog_changes.manufacturer_article');
            
            $fields[] = 'o_pfvd.variant as cp_original_article';
            
            $join .= db_quote(" LEFT JOIN ?:product_features_values as o_pfv ON o_pfv.product_id = products.product_id AND o_pfv.lang_code = ?s AND o_pfv.feature_id = ?i", $lang_code, $original_article_id);     
            $join .= db_quote(" LEFT JOIN ?:product_feature_variant_descriptions as o_pfvd ON o_pfvd.variant_id = o_pfv.variant_id AND o_pfvd.lang_code = ?s", $lang_code);
            
            $join .= db_quote(" LEFT JOIN ?:product_features_values as m_pfv ON m_pfv.product_id = products.product_id AND m_pfv.lang_code = ?s AND m_pfv.feature_id = ?i", $lang_code, $manufacturer_article_id);     
            $join .= db_quote(" LEFT JOIN ?:product_feature_variant_descriptions as m_pfvd ON m_pfvd.variant_id = m_pfv.variant_id AND m_pfvd.lang_code = ?s", $lang_code);
                        
            $search_q = fn_cp_catalog_changes_convert_search_q($params['art_q']);
            $condition .= db_quote(" AND ( o_pfvd.variant LIKE ?l OR lower(o_pfvd.cp_search_variant) LIKE ?l)", '%' . $params['art_q'] . '%', '%' . $search_q . '%');
    }
}

function fn_cp_catalog_changes_add_feature_variant_pre($feature_id, &$variant)
{
    $original_article_id = Registry::get('addons.cp_catalog_changes.original_article');
    
    if ($feature_id == $original_article_id) {
        
        $variant['cp_search_variant'] = fn_cp_catalog_changes_convert_search_q($variant['variant']);
    }
}

function fn_cp_catalog_changes_update_product_feature_variant_pre($feature_id, $feature_type, &$variant, $lang_code)
{
    $original_article_id = Registry::get('addons.cp_catalog_changes.original_article');
    
    if ($feature_id == $original_article_id) {
        
        $variant['cp_search_variant'] = fn_cp_catalog_changes_convert_search_q($variant['variant']);
    }
}

function fn_cp_catalog_changes_convert_search_q($q)
{
    $c_q = '';
    
    if (!empty($q)) {
        $c_q = strtolower($q);
        $c_q = preg_replace('/[^a-z0-9]/ui', '', $c_q);
    }

    return $c_q;
}