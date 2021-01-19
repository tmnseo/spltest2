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

if ($mode == 'search') {

    $params = $_REQUEST;
    fn_add_breadcrumb(__('search_results'));
    
    if (!empty($params['search_performed']) || !empty($params['features_hash'])) {

        $params = $_REQUEST;
        $params['extend'] = array('description');

        if ($items_per_page = fn_change_session_param(Tygh::$app['session']['search_params'], $_REQUEST, 'items_per_page')) {
            $params['items_per_page'] = $items_per_page;
        }
        if ($sort_by = fn_change_session_param(Tygh::$app['session']['search_params'], $_REQUEST, 'sort_by')) {
            $params['sort_by'] = $sort_by;
        }
        if ($sort_order = fn_change_session_param(Tygh::$app['session']['search_params'], $_REQUEST, 'sort_order')) {
            $params['sort_order'] = $sort_order;
        }
        
        $params['cp_product_type'] = 'O';
        list($original_products, $original_search) = fn_get_products($params, Registry::get('settings.Appearance.products_per_page'));
        
        $params['cp_product_type'] = 'A';
        list($analog_products, $analog_search) = fn_get_products($params, Registry::get('settings.Appearance.products_per_page'));
        
        if (defined('AJAX_REQUEST') && (!empty($params['features_hash']) && !($original_products || $analog_products))) {
            fn_filters_not_found_notification();
            exit;
        }
        if (!empty($original_products)) {
            fn_gather_additional_products_data($original_products, array(
                'get_icon' => true,
                'get_detailed' => true,
                'get_additional' => true,
                'get_options'=> true
            ));
        }
        
        if (!empty($analog_products)) {
            fn_gather_additional_products_data($analog_products, array(
                'get_icon' => true,
                'get_detailed' => true,
                'get_additional' => true,
                'get_options'=> true
            ));
        }
        
        if (!empty($original_products) || !empty($analog_products)) {
            Tygh::$app['session']['continue_url'] = Registry::get('config.current_url');
        }

        $selected_layout = fn_get_products_layout($params);

        Tygh::$app['view']->assign('original_products', $original_products);
        Tygh::$app['view']->assign('analog_products', $analog_products);
        Tygh::$app['view']->assign('original_search', $original_search);
        Tygh::$app['view']->assign('analog_search', $analog_search);
        Tygh::$app['view']->assign('selected_layout', $selected_layout);
        Tygh::$app['view']->assign('total_search_product_count', count($original_products) + count($analog_products));
    }
}

if ($mode == 'cp_live_search') {
    if (!empty($_REQUEST['q'])) {
        $q = $_REQUEST['q'];
        
        if (defined('AJAX_REQUEST')) {
            Registry::get('ajax')->assign('highlight', $q);
        }
        
        $settings = Registry::get('addons.cp_live_search');

        $company_id = Registry::get('runtime.company_id');
        $lang_code = CART_LANGUAGE;
        
        $params = array (
            'q' => $q,
            'match' => $settings['search_by_exact_phrase'],
            'pname' => 'Y',
            'pcode_from_q' => 'Y',
            'search_by_product_code' => $settings['search_by_product_code'],
            'pshort' => $settings['search_in_short_description'],
            'pfull' => $settings['search_in_full_description'],
            'pkeywords' => $settings['search_in_keywords'],
            'search_in_features' => $settings['searchinfeatures'],
            'search_in_options' => $settings['searchinoptions'],
            'search_by_categories' => $settings['search_by_categories'],
            'page' => isset($_REQUEST['page']) ? $_REQUEST['page'] : 1,
            'is_pname_search' => !empty($_REQUEST['is_pname_search']) ? $_REQUEST['is_pname_search'] : 'N'
        );
        
        if (fn_allowed_for('MULTIVENDOR') && !empty($_REQUEST['company_id'])) {
            $params['company_id'] = $_REQUEST['company_id'];
        }

        $params['cp_live_search'] = true;
        
        $table_exists = fn_cp_get_search_cache_table($company_id, $lang_code);
        if ($settings['use_cache'] == 'Y' && !empty($table_exists)) {
            $params['q'] = '';
            $params['search_q'] = $q;
            
            if ($settings['order_results'] == 'weight') {
                $params['sort_by'] = 'cp_search_weight';
                $params['sort_order'] = 'desc';
            } elseif ($settings['order_results'] == 'newest') {
                $params['sort_by'] = 'timestamp';
                $params['sort_order'] = 'desc';
            } elseif ($settings['order_results'] == 'price_asc') {
                $params['sort_by'] = 'price';
                $params['sort_order'] = 'asc';
            } elseif ($settings['order_results'] == 'price_desc') {
                $params['sort_by'] = 'price';
                $params['sort_order'] = 'desc';            
            } elseif ($settings['order_results'] == 'product') {
                $params['sort_by'] = 'product';
                $params['sort_order'] = 'asc';
            } else {
                $params['sort_by'] = 'popularity';
                $params['sort_order'] = 'desc';
            }
        }

        // Featured products
        if ($settings['show_f_products_result'] == 'Y') {
            $f_product_ids = fn_cp_get_phrase_featured_products($q);
            if (!empty($f_product_ids)) {
                $params['exclude_pid'] = $f_product_ids;

                if (empty($_REQUEST['load_more'])) {
                    $f_products_limit = !empty($settings['f_products_amount']) ? $settings['f_products_amount'] : 5;

                    $f_params = array('item_ids' => implode(',', $f_product_ids));
                    list($f_products) = fn_get_products($f_params, $f_products_limit, $lang_code);
                }
            }
        }
        
        //+++++++++++
        $join = $condition = $limit = '';
        $default_params = array();
        
        if (Registry::get('addons.cp_product_page.status') == 'A') {
            if (!empty(Tygh::$app['session']['cp_user_has_defined_city'])) {
                $params['cp_np_type'] = 'B';
                $params['sort_by'] = 'cp_np_weight';
            } else {
                $params['cp_np_type'] = 'C';
                $params['sort_by'] = 'cp_np_price';
            }
            $params['sort_order'] = 'asc';
            $params['cp_np_settigns'] = Registry::get('addons.cp_product_page');
            $params['load_products_extra_data'] = false;
            $params['cp_live_search'] = false;
            $params['art_q'] = $params['q'];
            $params['cp_np_use_like'] = true;
            list($products, $search) = fn_get_products($params, $settings['items_limit'], $lang_code);
            $search['q'] = $params['q'];
            $search['cp_live_search'] = true;

            if (!empty($params['is_pname_search']) && $params['is_pname_search'] == 'Y') {
                Tygh::$app['view']->assign('is_pname_search', true);
            }

            if (!empty($products)) {
                if (!isset(Tygh::$app['session']['cp_cc_founded_products'])) {
                    Tygh::$app['session']['cp_cc_founded_products'] = array();
                }
                Tygh::$app['session']['cp_cc_founded_products'] = $products;
                if (empty($params['page']) || $params['page'] == 1) {
                    $best_offer = reset($products);
                    if ($settings['show_thumbnails'] == 'Y') {
                        $best_offer['main_pair'] = fn_get_image_pairs($best_offer['product_id'], 'product', 'M', true, true, $lang_code);
                    }
                    /* gMelnikov is_analog */
                    $manufacturer_code_id = Registry::get('addons.cp_change_inv.manufacturer_code_id');
                    
                    $this_manufacturer_code = db_get_field("SELECT vd.variant FROM ?:product_features_values as v LEFT JOIN ?:product_feature_variant_descriptions as vd ON v.variant_id = vd.variant_id WHERE v.product_id = ?i AND v.feature_id = ?i AND vd.lang_code = ?s", $best_offer['product_id'], $manufacturer_code_id, CART_LANGUAGE);
                    if ($best_offer['cp_np_manuf_code'] !== $this_manufacturer_code){
                       $best_offer['is_analog'] = true; 
                    }else {
                        $best_offer['is_analog'] = false;
                    }
                    if (!empty($params['is_pname_search']) && $params['is_pname_search'] == 'Y') {
                        $best_offer['is_pname_search'] = true;
                    }
                    /* gMelnikov is_analog */
                    Tygh::$app['view']->assign('cp_is_best_offer', $best_offer);
                    $products = array_slice($products, 1);
                    $search['total_items'] -= 1;
                }
            } elseif (!empty(Tygh::$app['session']['cp_cc_founded_products'])) {
                $products = Tygh::$app['session']['cp_cc_founded_products'];
                Tygh::$app['view']->assign('cp_is_no_results', true);
            }
        } else {
            $params['get_conditions'] = true;
            list(, $p_join, $p_condition) = fn_get_products($params, 0, $lang_code);
            
            
            $original_brand_id = Registry::get('addons.cp_catalog_changes.original_brand');
            $original_article_id = Registry::get('addons.cp_catalog_changes.original_article');
            $manufacturer_article_id = Registry::get('addons.cp_catalog_changes.manufacturer_article');
            
            
            //$join .= db_quote(" LEFT JOIN ?:product_features_values as o_pfv ON o_pfv.product_id = products.product_id AND o_pfv.lang_code = ?s AND o_pfv.feature_id = ?i", $lang_code, $original_article_id);
            $join .= db_quote(" INNER JOIN ?:product_feature_variant_descriptions as o_pfvd ON o_pfvd.variant_id = o_pfv.variant_id AND o_pfvd.lang_code = ?s", $lang_code);
            $join .= db_quote(" LEFT JOIN ?:products as products ON o_pfv.product_id = products.product_id");

            $join .= $p_join;
            
            $search_q = fn_cp_catalog_changes_convert_search_q($params['q']);
            
            $condition .= db_quote(" AND o_pfv.lang_code = ?s AND o_pfv.feature_id = ?i", $lang_code, $original_article_id);
            $condition .= db_quote(" AND ( o_pfvd.variant LIKE ?l OR lower(o_pfvd.cp_search_variant) LIKE ?l)", '%' . $params['q'] . '%', '%' . $search_q . '%');
            $condition .= $p_condition;
            
            $params['items_per_page'] = $settings['items_limit'];
            $params['page'] = !empty($params['page']) ? $params['page'] : 1;
            $limit = db_paginate($params['page'], $params['items_per_page']);
            $params['total_items'] = db_get_field("SELECT COUNT(DISTINCT o_pfv.variant_id) FROM ?:product_features_values as o_pfv $join $condition");  
            
            $products = db_get_hash_array("SELECT DISTINCT o_pfv.variant_id as product_id, o_pfvd.variant as product FROM ?:product_features_values as o_pfv $join $condition GROUP BY product $limit ",  'product_id');

            $search = $params;
            //+++++++++++
        }
        if ($settings['show_product_category'] == 'each' || $settings['show_product_category'] == 'group') {
            $main_categories = array();
            $_products = !empty($f_products) ? array_merge($products, $f_products) : $products;

            foreach ($_products as $product) {
                if (!empty($product['main_category']) && !in_array($product['main_category'], $main_categories)) {
                    $main_categories[] = $product['main_category'];
                }
            }
            if (!empty($main_categories)) {
                if ($settings['show_product_category'] == 'group') {

                    $category_names = fn_cp_live_search_get_category_names($main_categories);
                    Registry::get('view')->assign('category_names', $category_names);

                } elseif ($settings['show_product_category'] == 'each') {

                    $category_labels = Tygh::$app['session']['category_labels'];
                    if (!empty($category_labels)) {
                        $colors = array();
                        if ($settings['show_category_labels'] == 'assigned' && !empty($settings['category_labels_colors'])) {
                            $colors = explode(',', $settings['category_labels_colors']);
                            $correct_category_ids = array();
                            foreach ($category_labels as $l_category_id => $label) {
                                if (in_array($label['color'], $colors)) {
                                    $correct_category_ids[] = $l_category_id;
                                }
                            }
                            $main_categories = array_diff($main_categories, $correct_category_ids);
                        } else {
                            $main_categories = array_diff($main_categories, array_keys($category_labels));
                        }
                    }

                    if (!empty($main_categories)) {
                        $new_category_labels = fn_cp_live_search_get_category_labels($main_categories, $colors);
                        $category_labels = fn_array_merge($category_labels, $new_category_labels);
                    }

                    Tygh::$app['session']['category_labels'] = $category_labels;

                    Registry::get('view')->assign('category_labels', $category_labels);
                }
            }
        }

        /*
        $gather_params = array(
            'get_icon' => true,
            'get_detailed' => true,
            'get_additional' => false,
            'get_options'=> true,
            'get_taxed_prices' => false,
            'get_extra' => false,
            'detailed_params' => false,
            'get_taxed_prices' => true
        );
        
        fn_gather_additional_products_data($products, $gather_params);

        // Featured products in result output
        if (!empty($f_products)) {
            fn_gather_additional_products_data($f_products, $gather_params);

            Registry::get('view')->assign('f_products', $f_products);
        }
         * 
         */
        
        if (empty($_REQUEST['load_more'])) {
            $cpv1 = ___cp('YnJhbmRz');
            $cpv2 = ___cp('c3BlbGxlcg');
            $cpv3 = ___cp('Y2F0ZWdvcmllcw');
            $cpv4 = ___cp('dmVuZG9ycw');
            $cpv5 = ___cp('bWVhbl93b3Jkcw');
            $cpv6 = ___cp('c2hvd19icmFuZHXfcmVzdWx0');
            $cpv7 = ___cp('c2hvd19jYPRlZ29yaWVzP3Jlc3VsdA');
            $cpv8 = ___cp('c2hvd192ZW5kb3JzP3Jlc3VsdA');
            if ($settings['use_suggestions'] != 'none') {
                $items = array();
                if ($settings['use_suggestions'] == 'search_phrases' || $settings['use_suggestions'] == 'auto') {
                    $items = fn_cp_get_phrase_suggestions($q);
                }
                if ($settings['use_suggestions'] == 'search_results' || $settings['use_suggestions'] == 'auto' && empty($items)) {
                    $items = fn_cp_get_history_suggestions($q, $company_id, $lang_code, $settings);
                }
                if (!empty($items)) {
                    $items = array_slice($items, 0, $settings['suggestions_amount']);
                    Registry::get('view')->assign('suggestions', $items);
                }
            }
            if ($settings[$cpv6] == 'Y') {
                $items = call_user_func(___cp('Zm5fY3BfbGl2ZV9zZWFyY2hfZ2V0P3XlYPJjaF9icmFuZHM'), $q, $lang_code, $settings);
                if (!empty($items)) {
                    Registry::get('view')->assign($cpv1, $items);
                }
            }
            if ($settings[$cpv7] == 'Y') {
                $items = call_user_func(___cp('Zm5fY3BfbGl2ZV9zZWFyY2hfZ2V0P3XlYPJjaF9jYPRlZ29yaWVz'), $q, $lang_code, $settings);
                if (!empty($items)) {
                    Registry::get('view')->assign($cpv3, $items);
                }
            }
            if ($settings[$cpv8] == 'Y') {
                $items = call_user_func(___cp('Zm5fY3BfbGl2ZV9zZWFyY2hfZ2V0P3XlYPJjaF92ZW5kb3Jz'), $q, $lang_code, $settings);
                if (!empty($items)) {
                    Registry::get('view')->assign($cpv4, $items);
                }
            }
            if (!empty($settings[$cpv2])) {
                $items = call_user_func(___cp('Zm5fY3BfbHXfZ2V0P3XwZWxsZPJfd29yZHM'), $q, $lang_code, $settings);
                if (!empty($items)) {
                    Registry::get('view')->assign($cpv5, $items);
                }
            }
        }

        $search_id = '';
        if (!empty($_REQUEST['search_id'])) {
            $search_id = $_REQUEST['search_id'];
        } else {
            $search_id = fn_cp_add_search_history($q, 'L', !empty($search['total_items']) ? $search['total_items'] : 0);
        }
        Registry::get('view')->assign('search_id', $search_id);

        $search_input_id = !empty($_REQUEST['search_input_id']) ? $_REQUEST['search_input_id'] : '';
        Registry::get('view')->assign('search_input_id', $search_input_id);
        Registry::get('view')->assign('live_result', $products);
        Registry::get('view')->assign('search', $search);

        if (!empty($_REQUEST['load_more'])) {
            Registry::get('view')->display('addons/cp_live_search/components/products_list.tpl');
        } else {
            Registry::get('view')->display('addons/cp_live_search/components/result.tpl');
        }
    }
   exit;
}