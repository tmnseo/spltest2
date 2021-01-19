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

use Tygh\Storage;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

//HOOKS

function fn_cp_vendor_panel_attachments_check_permission_post($request, &$permission)
{
    if (!empty($request['object_type']) && $request['object_type'] == 'vendor_cert' && !empty($request['object_id'])) {
        $cur_vendor = Registry::get('runtime.company_id');
        if (!empty($cur_vendor) && $cur_vendor == $request['object_id'] || empty($cur_vendor)) {
            $permission = true;
        }
    }
}

function fn_cp_vendor_panel_attachment_delete_file($attachment_ids, $object_type, $object_id)
{
    if (!empty($object_type) && $object_type == 'vendor_cert') {
        $data = db_get_array("SELECT * FROM ?:attachments WHERE attachment_id IN (?n) AND object_type = ?s AND object_id = ?i", $attachment_ids, $object_type, $object_id);
        if (!empty($data)) {
            foreach ($data as $entry) {
                Storage::instance('images')->delete('vendor_cert/' . $object_id . '/' . $entry['filename']);
            }
        }
    }
}

function fn_cp_vendor_panel_attachment_add_file($attachment_data, $object_type, $object_id, $type, $files, $attachment_id, $uploaded_data)
{
    if (!empty($object_type) && $object_type == 'vendor_cert' && !empty($object_id)) {
        if ($attachment_id && !empty($uploaded_data[$attachment_id]) && $uploaded_data[$attachment_id]['size']) {
            fn_cp_vp_update_certificate_in_images($attachment_id, $uploaded_data, $object_id);
        }
    }
}

function fn_cp_vendor_panel_attachment_update_file($attachment_data, $attachment_id, $object_type, $object_id, $type, $files, $lang_code, $uploaded_data)
{
    if (!empty($object_type) && $object_type == 'vendor_cert' && !empty($object_id)) {
        fn_cp_vp_update_certificate_in_images($attachment_id, $uploaded_data, $object_id);
    }
}

function fn_cp_vp_update_certificate_in_images($attachment_id, $uploaded_data, $object_id)
{
    if ($attachment_id && !empty($uploaded_data[$attachment_id]) && $uploaded_data[$attachment_id]['size']) {
        $directory = 'vendor_cert/' . $object_id;
        $filename = $uploaded_data[$attachment_id]['name'];
        $old_filename = db_get_field("SELECT filename FROM ?:attachments WHERE attachment_id = ?i", $attachment_id);
        
        if ($old_filename) {
            Storage::instance('images')->delete($directory . '/' . $old_filename);
        }

        list($filesize, $filename) = Storage::instance('images')->put($directory . '/' . $filename, array(
            'file' => $uploaded_data[$attachment_id]['path'],
            'keep_origins' => true
        ));
    }
    return true;
}

function fn_cp_vendor_panel_delete_company($company_id, $result, $storefronts)
{
    if (!empty($company_id) && !empty($result)) {
        db_query("DELETE FROM ?:cp_vp_vendor_warranties WHERE company_id = ?i", $company_id);
        $attachments = db_get_fields("SELECT attachment_id FROM ?:attachments WHERE object_type = ?s AND object_id = ?i", 'vendor_cert', $company_id);

        Storage::instance('attachments')->deleteDir('vendor_cert/' . $company_id);
        Storage::instance('images')->delete('vendor_cert/' . $company_id);
        
        foreach ($attachments as $attachment_id) {
            db_query("DELETE FROM ?:attachments WHERE attachment_id = ?i", $attachment_id);
            db_query("DELETE FROM ?:attachment_descriptions WHERE attachment_id = ?i", $attachment_id);
        }
    }
}
//FUNCTIONS
function fn_cp_vp_update_warranty_cat($category_data, $category_id = 0, $lang_code = CART_LANGUAGE)
{
    if (!empty($category_id)) {
        db_query("UPDATE ?:cp_vp_warranty_categories SET ?u WHERE category_id = ?i", $category_data, $category_id);
        db_query("UPDATE ?:cp_vp_warranty_category_descriptions SET ?u WHERE category_id = ?i AND lang_code = ?s", $category_data, $category_id, $lang_code);
    } else {
        $category_id = db_query("INSERT INTO ?:cp_vp_warranty_categories ?e", $category_data);
        if (!empty($category_id)) {
            $category_data['category_id'] = $category_id;
            foreach (fn_get_translation_languages() as $category_data['lang_code'] => $v) {
                db_query("INSERT INTO ?:cp_vp_warranty_category_descriptions ?e", $category_data);
            }
        }
    }
    return $category_id;
}
function fn_cp_vp_get_warranty_categories($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page,
    );
    $params = array_merge($default_params, $params);
    $fields = array (
        "?:cp_vp_warranty_categories.*",
        "?:cp_vp_warranty_category_descriptions.*",
    );
    $sortings = array (
        'name' => "?:cp_vp_warranty_category_descriptions.category",
        'position' => "?:cp_vp_warranty_categories.position",
        'status' => "?:cp_vp_warranty_categories.status",
    );
    $condition = $join = $group = '';
    
    //$condition .= fn_get_company_condition('?:cp_vp_warranty_categories.company_id');
    
    $statuses = array('A');
   
    if (!empty($params['active'])) {
        $condition .= db_quote(" AND ?:cp_vp_warranty_categories.status IN (?a)", $statuses);
    }
    if (!empty($params['search_query'])) {
        $trimed = trim($params['search_query']);
        if (!empty($trimed)) {
            $condition .= db_quote(" AND ?:cp_vp_warranty_category_descriptions.category LIKE ?l", '%' . $trimed . '%');
        }
    }
    $join .= db_quote(" LEFT JOIN ?:cp_vp_warranty_category_descriptions ON ?:cp_vp_warranty_category_descriptions.category_id = ?:cp_vp_warranty_categories.category_id AND ?:cp_vp_warranty_category_descriptions.lang_code = ?s", $lang_code);
    
    $sorting = db_sort($params, $sortings, 'name', 'asc');
    
    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_vp_warranty_categories $join WHERE 1 $condition $group");
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }

    $categories = db_get_hash_array('SELECT ' . implode(', ', $fields) . " FROM ?:cp_vp_warranty_categories $join WHERE 1 $condition $group $sorting $limit", 'category_id');
    
    return array($categories, $params);
}

function fn_cp_vp_get_war_category_data($category_id, $lang_code = CART_LANGUAGE)
{
    $condition = $avail_cond = $join = '';

    $fields = array(
        '?:cp_vp_warranty_categories.*',
        '?:cp_vp_warranty_category_descriptions.*',
    );
    
    $join .= db_quote(" LEFT JOIN ?:cp_vp_warranty_category_descriptions ON ?:cp_vp_warranty_categories.category_id = ?:cp_vp_warranty_category_descriptions.category_id AND ?:cp_vp_warranty_category_descriptions.lang_code = ?s", $lang_code);

    $condition .= $avail_cond;
    $category_data = db_get_row(
        "SELECT ?p FROM ?:cp_vp_warranty_categories ?p WHERE ?:cp_vp_warranty_categories.category_id = ?i ?p",
        implode(',', $fields), $join, $category_id, $condition
    );
    
    return $category_data;
}

function fn_cp_vp_get_vendor_warranties($company_id, $params = array(), $lang_code = CART_LANGUAGE)
{
    $warranties = array();
    if (!empty($company_id)) {
        $condition = $avail_cond = $join = $group = $sorting = $limit = '';
        $fields = array(
            '?:cp_vp_vendor_warranties.*',
            '?:cp_vp_warranty_category_descriptions.*',
        );
        $sortings = array (
            'name' => "?:cp_vp_warranty_category_descriptions.category",
            'position' => "?:cp_vp_vendor_warranties.position",
            'status' => "?:cp_vp_warranty_categories.status",
        );
        
        $join .= db_quote(" LEFT JOIN ?:cp_vp_warranty_categories ON ?:cp_vp_warranty_categories.category_id = ?:cp_vp_vendor_warranties.category_id ");
        $join .= db_quote(" LEFT JOIN ?:cp_vp_warranty_category_descriptions ON ?:cp_vp_warranty_categories.category_id = ?:cp_vp_warranty_category_descriptions.category_id AND ?:cp_vp_warranty_category_descriptions.lang_code = ?s", $lang_code);
        
        if (AREA == 'C') {
            $condition .= db_quote(" AND ?:cp_vp_warranty_categories.status = ?s", 'A');
        }
        $condition .= db_quote(" AND ?:cp_vp_vendor_warranties.company_id = ?i", $company_id);
        
        if (!empty($params['items_per_page'])) {
            $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_vp_vendor_warranties $join WHERE 1 $condition $group");
            $limit = db_paginate($params['page'], $params['items_per_page']);
        }
        $sorting = db_sort($params, $sortings, 'position', 'asc');
        
        $warranties = db_get_hash_multi_array('SELECT ' . implode(', ', $fields) . " FROM ?:cp_vp_vendor_warranties $join WHERE 1 $condition $group $sorting $limit", array('variant_id','category_id'));
        if (AREA == 'C') {
            foreach($warranties as $variant_id => $cats) {
                $categories = $cats;
                foreach($categories as &$cat_data) {
                    if (!empty($cat_data['warranty_term'])) {
                        $cat_data['warranty_txt'] = fn_cp_vp_generate_warranty_text($cat_data['warranty_term'], $lang_code);
                    } else {
                        $cat_data['warranty_txt'] = __('cp_vp_no_txt');
                    }
                }
                $warranties[$variant_id] = array(
                    'variant_id' => $variant_id,
                    'name' => fn_cp_vp_get_brand_name($variant_id, $lang_code),
                    'categories' => $categories
                );
            }
        }
    }
    return $warranties;
}

function fn_cp_vp_generate_warranty_text($warranty, $lang_code = CART_LANGUAGE)
{
    $txt = $warranty;
    if (!empty($warranty)) {
        if ($warranty % 12 == 0) {
            $txt = $years = $warranty/12;
            $last_char = substr($years, -1);
        } else {
            $last_char = substr($warranty, -1);
        }
        if ($lang_code == 'ru') {
            if ($last_char == 1 && substr($txt, -2) != 11) {
                $use_lang = !empty($years) ? __('cp_vp_year_txt') : __('cp_vp_month_txt');
            } elseif (in_array($last_char, array(2,3,4)) && substr($txt, -2, 1) != 1) {
                $use_lang = !empty($years) ? __('cp_vp_years_txt') : __('cp_vp_months_txt');
            } else {
                $use_lang = !empty($years) ? __('cp_vp_years2_txt') : __('cp_vp_months2_txt');
            }
        } else {
            if ($txt == 1) {
                $use_lang = !empty($years) ? __('cp_vp_year_txt') : __('cp_vp_month_txt');
            } else {
                $use_lang = !empty($years) ? __('cp_vp_years_txt') : __('cp_vp_months_txt');
            }
        }
        $txt .= ' ' . $use_lang . ' ' . __('cp_vp_of_warranty_txt');
    }
    return $txt; 
}

function fn_cp_vp_add_vendor_warranties($company_id, $variant_id, $categories)
{
    if (!empty($company_id) && !empty($variant_id) && !empty($categories)) {
        $exists_wars = db_get_hash_array("SELECT category_id FROM ?:cp_vp_vendor_warranties WHERE company_id = ?i AND variant_id = ?i", 'category_id', $company_id, $variant_id);
        $put_data = array(
            'company_id' => $company_id,
            'variant_id' => $variant_id
        );
        foreach($categories as $cat_data) {
            if (!empty($cat_data['category_id']) && (empty($exists_wars) || (!empty($exists_wars) && empty($exists_wars[$cat_data['category_id']])))) {
                $put_data['category_id'] = $cat_data['category_id'];
                $put_data['warranty_term'] = trim($cat_data['warranty_term']);
                $put_data['position'] = trim($cat_data['position']);
                db_replace_into('cp_vp_vendor_warranties', $put_data);
            }
        }
    }
    return true;
}

function fn_cp_vp_update_vendor_warranties($company_id, $data)
{
    if (!empty($company_id) && !empty($data)) {
        foreach($data as $variant_id => $categories) {
            if (!empty($categories)) {
                $prev_categories = db_get_fields("SELECT category_id FROM ?:cp_vp_vendor_warranties WHERE company_id = ?i AND variant_id = ?i", $company_id, $variant_id);
                $new_categories = array();
                foreach($categories as $cat_data) {
                    if (!empty($cat_data['category_id'])) {
                        $put_data = array(
                            'company_id' => $company_id,
                            'variant_id' => $variant_id,
                            'category_id' => $cat_data['category_id'],
                            'position' => !empty($cat_data['position']) ? $cat_data['position'] : 0,
                            'warranty_term' => !empty($cat_data['warranty_term']) ? trim($cat_data['warranty_term']) : ''
                        );
                        db_replace_into('cp_vp_vendor_warranties', $put_data);
                        $new_categories[] = $cat_data['category_id'];
                    }
                }
                $for_delete = array_diff($prev_categories, $new_categories);
                if (!empty($for_delete)) {
                    db_query("DELETE FROM ?:cp_vp_vendor_warranties WHERE company_id = ?i AND variant_id = ?i AND category_id IN (?n)", $company_id, $variant_id, $for_delete);
                }
            }
        }
    }
    return true;
}

function fn_cp_vp_get_brand_name($variant_id, $lang_code = CART_LANGUAGE)
{
    $brand = '';
    if (!empty($variant_id)) {
        $brand = db_get_field("SELECT variant FROM ?:product_feature_variant_descriptions WHERE variant_id = ?i AND lang_code = ?s", $variant_id, $lang_code);
    }
    return $brand;
}

function fn_cp_vp_delete_vendor_warranty($company_id, $category_id)
{
    if (!empty($company_id) && !empty($category_id)) {
        db_query("DELETE FROM ?:cp_vp_vendor_warranties WHERE company_id = ?i AND category_id = ?i", $company_id, $category_id);
    }
    return true;
}

function fn_cp_vp_delete_vendor_warranty_brand($company_id, $variant_id)
{
    if (!empty($company_id) && !empty($variant_id)) {
        db_query("DELETE FROM ?:cp_vp_vendor_warranties WHERE company_id = ?i AND variant_id = ?i", $company_id, $variant_id);
    }
    return true;
}

function fn_cp_vp_delete_warranty_category($category_ids)
{
    if (!empty($category_ids)) {
        db_query("DELETE FROM ?:cp_vp_vendor_warranties WHERE category_id IN (?n)", $category_ids);
        db_query("DELETE FROM ?:cp_vp_warranty_categories WHERE category_id IN (?n)", $category_ids);
        db_query("DELETE FROM ?:cp_vp_warranty_category_descriptions WHERE category_id IN (?n)", $category_ids);
    }
    return true;
}
