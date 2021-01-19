<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }
//HOOKS
function fn_cp_vend_city_filter_get_product_filter_fields (&$filters) {
    $filters['X'] = array(
        'db_field' => 'company_id',
        'table' => 'products',
        'description' => 'cp_cf_vendor_city',
        'condition_type' => 'F',
        'variant_name_field' => 'companies.city'
    );
}
function fn_cp_vend_city_filter_generate_filter_field_params (&$params, $filters, $selected_filters, $filter_fields, $filter, $structure) {
    if (AREA == 'C' && !empty($filter) && !empty($filter['field_type']) && $filter['field_type'] == 'X') {
        if (!empty($params) && !empty($params['filter_params']) && !empty($params['filter_params']['company_id'])) {
            $params['cp_vcf_check_this'] = $params['filter_params']['company_id'];
            unset($params['filter_params']['company_id']);
        }
    }
}
function fn_cp_vend_city_filter_get_products ($params, $fields, $sortings, &$condition, $join, $sorting, $group_by, $lang_code, $having) {
    if (AREA == 'C' && !empty($params['cp_vcf_check_this'])) {
        $cities = db_get_fields("SELECT DISTINCT city FROM ?:companies WHERE company_id IN (?n)", $params['cp_vcf_check_this']);
        if (!empty($cities)) {
            $condition .= db_quote(' AND companies.city IN (?a)', $cities);
        }
    }
}
function fn_cp_vend_city_filter_get_filters_products_count_post ($params, $lang_code, &$filters, $selected_filters) {
    if (AREA == 'C' && !empty($filters)) {
        foreach($filters as &$filter_data) {
            if (!empty($filter_data['field_type']) && $filter_data['field_type'] == 'X' && !empty($filter_data['variants'])) {
                $exists = array();
                if (!empty($filter_data['selected_variants'])) {
                    foreach($filter_data['selected_variants'] as $sv_key => $sv_val) {
                        if (!in_array($sv_val['variant'], $exists)) {
                            $exists[] = $sv_val['variant'];
                        }
                    }
                }
                foreach($filter_data['variants'] as $v_key => $v_val) {
                    if (!empty($v_val['variant'])) {
                        if (!in_array($v_val['variant'], $exists)/* && (empty($filter_data['selected_variants']) || (!empty($filter_data['selected_variants'] && !empty($filter_data['selected_variants'][$v_key]))) ) */) {
                            $exists[] = $v_val['variant'];
                        } else {
                            unset($filter_data['variants'][$v_key]);
                        }
                    } else {
                        unset($filter_data['variants'][$v_key]);
                    }
                }
            }
        }
    }
}