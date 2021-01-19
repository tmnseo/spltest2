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
use Tygh\Settings;
use Tygh\Enum\ObjectStatuses;
use Tygh\Addons\CpStatusesRules\Order\Order;
use Tygh\Enum\UserTypes;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/*HOOKS*/
function fn_cp_demo_vendors_get_companies($params, &$fields, &$sortings, $condition, $join, $auth, $lang_code, $group)
{
    $fields[] = "?:companies.cp_is_demo";
    $sortings['cp_demo'] = "?:companies.cp_is_demo";
}
function fn_cp_demo_vendors_update_company_pre(&$company_data, $company_id, $lang_code, $can_update)
{
    if (empty($company_id) && !empty($company_data) && $can_update == true) {
        $company_data['cp_is_demo'] = 'Y';
    }
}
function fn_cp_demo_vendors_create_company_admin_post($company_data, $fields, $notify, $user)
{   
    $cp_is_demo = db_get_field("SELECT cp_is_demo FROM ?:companies WHERE company_id = ?i",$company_data['company_id']);
    if (!empty($cp_is_demo) && $cp_is_demo == 'Y') {
        $usergroup_id = Registry::get('addons.cp_demo_vendors.demo_usergroup_id');
        fn_change_usergroup_status('A', $user['user_id'], $usergroup_id);
    }
}
function fn_cp_demo_vendors_update_product_pre(&$product_data, $product_id, $lang_code, $can_update)
{
    if (!empty($product_data['company_id']) && fn_cp_check_demo_mode($product_data['company_id'])) {
        $product_data['status'] = ObjectStatuses::HIDDEN;
    }
}
function fn_cp_demo_vendors_vendor_data_premoderation_request_approval_for_products_pre($product_ids, &$update_product)
{
    $company_products = db_get_hash_array(
        "SELECT c.company_id, product_id FROM ?:products as p
        INNER JOIN ?:companies as c ON c.company_id = p.company_id 
        WHERE product_id IN (?n) AND cp_is_demo = ?s",
        'company_id',
        $product_ids, 'Y'
    );
    if (!empty($company_products)) {
        foreach ($company_products as $company_id => $data) {
            db_query("UPDATE ?:products SET status = ?s WHERE product_id = ?i", ObjectStatuses::HIDDEN, $data['company_id']);
        }
        $update_product = false;
    }   
}
function fn_cp_demo_vendors_import_post($pattern, $import_data, $options, $result, $processed_data)
{
    if ($result && !empty($import_data) && strtolower($pattern['pattern_id']) === 'products') {

        $company_id = !empty($options['preset']['company_id']) ? $options['preset']['company_id'] : null;

        if (!empty($company_id) && fn_cp_check_demo_mode($company_id)) {
            foreach ($import_data as $data) {
                $_pdata = current($data);
                $product_code = !empty($_pdata['product_code']) ? CP_CATALOG_CHANGES_VENDOR_PREFIX . $company_id . '-' . $_pdata['product_code'] : null;
                if (!empty($product_code)) {
                    db_query("UPDATE ?:products SET status = ?s WHERE product_code = ?s", ObjectStatuses::HIDDEN, $product_code);
                }
            }
        }        
    }
}
function fn_cp_demo_vendors_update_profile($action, $user_data, $current_user_data)
{   
    
    if ($action === 'add' && $user_data['user_type'] === UserTypes::VENDOR
    ) {
        $company_id = !empty($user_data['company_id']) ? $user_data['company_id'] : null;
        if (!empty($company_id)) {
            $root_admin_usergroups = db_get_fields("SELECT usergroup_id FROM ?:usergroup_links as ul 
                                                    INNER JOIN ?:users as u ON u.user_id = ul.user_id
                                                    WHERE u.company_id = ?i AND u.is_root = ?s AND ul.status = ?s", $company_id, 'Y', 'A');
            if (!empty($root_admin_usergroups)) {
                foreach ($root_admin_usergroups as $usergroup_id) {
                   fn_change_usergroup_status('A', $user_data['user_id'], $usergroup_id); 
                }
            }
        }
    }
}
/*HOOKS*/
function fn_cp_demo_vendors_install()
{   
    $usergroup_data = array(
        'usergroup' => __('cp_demo_vendors.usergroup_name'),
        'type' => 'V',
        'status' => 'A',
        'privileges' => array(
            'view_currencies' => 'Y',
            'exim_access' => 'Y',
            'edit_files' => 'Y',
            'view_locations' => 'Y',
            'manage_shipping' => 'Y',
            'view_shipping' => 'Y',
            'manage_store_locator' => 'Y',
            'view_store_locator' => 'Y',
            'edit_order' => 'Y',
            'change_order_status' => 'Y',
            'view_orders' => 'Y',
            'view_reports' => 'Y',
            'cp_products_view' => 'Y',
            'view_catalog' => 'Y',
            'view_users' => 'Y',
            'view_vendor_communication' => 'Y',
            'manage_payouts' => 'Y',
            'view_payouts' => 'Y',
            'manage_vendors' => 'Y',
            'view_vendors' => 'Y'
         )
    );

    $usergroup_id = fn_update_usergroup($usergroup_data, 0, DESCR_SL);

    if (!empty($usergroup_id)) {    
        Registry::set('addons.cp_demo_vendors.demo_usergroup_id', $usergroup_id);
        Settings::instance()->updateValue('demo_usergroup_id', $usergroup_id, 'cp_demo_vendors');
    }
}
function fn_cp_check_demo_mode($company_id = null)
{   
    $auth = Tygh::$app['session']['auth'];
    
    if ($auth['user_type'] != 'V' && empty($company_id)){
        return false;
    }
    if (!empty($auth['company_id']) || !empty($company_id)) {

        $vendor_id = !empty($company_id) ? $company_id : $auth['company_id'];

        if (!empty($vendor_id)) { 
            $cp_is_demo = db_get_field("SELECT cp_is_demo FROM ?:companies WHERE company_id = ?i",$vendor_id);

            if (empty($cp_is_demo) || $cp_is_demo == 'N'){
                return false;
            }elseif ($cp_is_demo == 'Y') {
                return true;
            }
        }
    }

    return false;
}

function fn_cp_get_statuses( $order_id, $type = STATUSES_ORDER,
    $status_to_select = array(),
    $additional_statuses = false,
    $exclude_parent = false,
    $lang_code = DESCR_SL,
    $company_id = 0)
{
    $cp_company_id = $company_id = Registry::get('runtime.company_id');
    if (empty($cp_company_id) || empty($order_id) || $type != STATUSES_ORDER) {
        return [];
    }

    $join = db_quote(
        ' LEFT JOIN ?:status_descriptions ON ?:status_descriptions.status_id = ?:statuses.status_id'
        . ' AND ?:status_descriptions.lang_code = ?s',
        $lang_code
    );

    $condition = db_quote(' AND ?:statuses.type = ?s', $type);

    /* SET RULES FOR CHANGE STATUSES */
    $need_addon_status = Registry::get('addons.cp_statuses_rules.status');

    if ($need_addon_status == 'A') {
        $cp_order = new Order($order_id);
            
        $condition .= $cp_order->getExcludeLockedStatusesCondition();
    }

    /*SET RULES FOR CHANGE STATUSES*/
    if ($status_to_select) {
        $condition .= db_quote(' AND ?:statuses.status IN (?a)', $status_to_select);
    }

    $params = array('sort_by' => '?:statuses.position', 'sort_order' => 'asc');
    $sort = array('?:statuses.position' => '?:statuses.position');
    $order = db_sort($params, $sort);


    $statuses = db_get_hash_array(
        'SELECT ?:statuses.*, ?:status_descriptions.* FROM ?:statuses ?p WHERE 1 = 1 ?p ?p',
        'status',
        $join,
        $condition,
        $order
    );
    
    $statuses_params = db_get_hash_multi_array(
        'SELECT status_id, param, value'
        . ' FROM ?:status_data'
        . ' WHERE status_id IN (?n)',
        array('status_id', 'param'),
        array_keys(fn_get_statuses_by_type($type))
    );

    foreach ($statuses as $status => $status_data) {
        $statuses[$status]['params'] = array();
        if (isset($statuses_params[$status_data['status_id']])) {
            foreach ($statuses_params[$status_data['status_id']] as $param_name => $param_data) {
                $statuses[$status]['params'][$param_name] = $param_data['value'];
            }
        }
    }

    if ($type == STATUSES_ORDER && $additional_statuses && empty($status_to_select)) {
        $statuses[STATUS_INCOMPLETED_ORDER] = array (
            'status' => STATUS_INCOMPLETED_ORDER,
            'status_id' => null,
            'description' => __('incompleted', '', $lang_code),
            'type' => STATUSES_ORDER,
            'params' => array(
                'inventory' => 'I',
            ),
        );

        if (empty($exclude_parent)) {
            $statuses[STATUS_PARENT_ORDER] = array (
                'status' => STATUS_PARENT_ORDER,
                'status_id' => null,
                'description' => __('parent_order', '', $lang_code),
                'type' => STATUSES_ORDER,
                'params' => array(
                    'inventory' => 'I',
                ),
            );
        }
    }

    return $statuses;
}
function fn_cp_get_simple_statuses($order_id, $type = STATUSES_ORDER, $additional_statuses = false, $exclude_parent = false, $lang_code = CART_LANGUAGE)
{
    $result = array();
    $statuses = fn_cp_get_statuses($order_id, $type, array(), $additional_statuses, $exclude_parent, $lang_code);
    
    foreach ($statuses as $key => $status) {
        $result[$key] = $status['description'];
    }

    return $result;
}