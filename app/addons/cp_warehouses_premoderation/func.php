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
use Tygh\Languages\Languages;
use Tygh\Tools\SecurityHelper;
use Tygh\Addons\Warehouses\Manager;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/*HOOKS*/
function fn_cp_warehouses_premoderation_store_locator_update_store_location_before_update(&$store_location_data, $store_location_id, $lang_code)
{   
    $auth = Tygh::$app['session']['auth'];
    if (!empty($auth['user_type']) && $auth['user_type'] == 'V') {
        
        $store_location_data['status'] = 'P';
        $store_location_data['cp_disapprove_reason'] = "";
        if (empty($store_location_id)) {
            $store_location_data['cp_is_new'] = 'Y';
        }   
    }
}
function fn_cp_warehouses_premoderation_store_locator_update_store_location_post($store_location_data, $store_location_id, $lang_code, $action)
{
    $auth = Tygh::$app['session']['auth'];
    
    if ($action == 'add' && $auth['user_type'] == 'V') {
        fn_cp_update_store_location($store_location_data, $store_location_id, DESCR_SL, $action);
    }
     
}
function fn_cp_warehouses_premoderation_store_locator_delete_store_location_post($store_location_id, $affected_rows, $deleted)
{
    $deleting_tables = array(
        '?:cp_premoderation_store_locations',
        '?:cp_premoderation_store_location_descriptions',
        '?:cp_premoderation_store_location_shipping_delays',
        '?:cp_premoderation_store_location_destination_links'
    );
    foreach ($deleting_tables as $table) {
        db_query("DELETE FROM $table WHERE store_location_id = ?i", $store_location_id);
    }
    db_query("DELETE FROM ?:cp_premoderation_rus_exim_1c_warehouses WHERE warehouse_id = ?i",$store_location_id);
}
function fn_cp_warehouses_premoderation_get_store_locations_before_select($params, $fields, $joins, &$conditions, $sortings, $items_per_page, $lang_code)
{   
    if (Registry::get('runtime.controller') == 'store_locator' && Registry::get('runtime.mode') == 'manage'){
        $conditions[] .= db_quote(" ?:store_locations.status NOT IN (?a) ", array('P', 'F'));
    } 
    if (AREA == 'C') {
        
        if (!empty($conditions['store_status'])) {
            $conditions['store_status'] = "(?:store_locations.status = 'A' OR ((?:store_locations.status = 'P' OR ?:store_locations.status = 'F') AND ?:store_locations.cp_is_new != 'Y'))";
        }
    }   
}
function fn_cp_warehouses_premoderation_get_product_data_post(&$product_data, $auth, $preview, $lang_code)
{
    if (empty($product_data['amount']) || empty($product_data['product_id'])) {
        return;
    }
    /* Remove amount if isset not premoderation or disapproved warehouses*/
    fn_cp_cheсk_main_amount($product_data);
}
/*HOOKS*/
function fn_cp_warehouses_premoderation_install() 
{   

    $service = Tygh::$app['template.mail.service'];
    $email_data = array(
        array( 
            'code' => 'cp_warehouses_premoderation_request', 
            'area' => 'A', 
            'status' => 'A', 
            'subject' => '{{__("cp_warehouses_premoderation_admin_subj") }}', 
            'addon' => 'cp_warehouses_premoderation', 
            'template' => '
                {{ snippet("header") }}
                {{__("cp_warehouses_premoderation.premoderation_request_text", {"[vendor]": premoderation_request_data.vendor, "[warehouse]": premoderation_request_data.warehouse}) }} <br /> 
                {{ snippet("footer") }}',
        ),
        array( 
            'code' => 'cp_warehouses_premoderation_request_result', 
            'area' => 'A', 
            'status' => 'A', 
            'subject' => '{{__("cp_warehouses_premoderation_vendor_subj")}}', 
            'addon' => 'cp_warehouses_premoderation', 
            'template' => '
                {{ snippet("header") }}
                {% if premoderation_request_data.premoderation_status == "A" %}
                    {{__("cp_warehouses_premoderation.approve_message", {"[warehouse]": premoderation_request_data.warehouse}) }} <br />
                {% elseif premoderation_request_data.premoderation_status == "F" %}
                    {{__("cp_warehouses_premoderation.disapprove_message", {"[warehouse]": premoderation_request_data.warehouse}) }} <br />
                    {% if premoderation_request_data.reason %}
                        {{premoderation_request_data.reason}}
                    {% endif %}
                {% endif %}
                <br /> 
                {{ snippet("footer") }}',
        )
    );
    foreach ($email_data as $data) {
        $result = $service->createTemplate($data);
    }
 
}
function fn_cp_warehouses_premoderation_uninstall() 
{ 
    $service = Tygh::$app['template.mail.service'];
    $email_data = array(
        'cp_warehouses_premoderation_request',
        'cp_warehouses_premoderation_request_result'
    );
    foreach ($email_data as $template_code) {
        $service->removeTemplateByCodeAndArea($template_code, 'A');      
    } 
    db_query('DELETE FROM ?:template_emails WHERE area = ?s AND code IN (?a)', 'A', $email_data);
    
    fn_cp_disable_premoderation_warehouses(); 
}
function fn_cp_approve_warehouse($location_id)
{   
    fn_cp_replace_warehouse_data($location_id);

    $result = db_query("UPDATE ?:store_locations SET status = ?s ,cp_is_new = ?s WHERE store_location_id = ?i", 'A', 'N', $location_id);

    if (!empty($result)) {

        $company_email = fn_cp_get_company_email_by_location_id($location_id);

        if (!empty($company_email)) {
            $mail_params = array(
                'warehouse' => fn_get_store_location_name($location_id),
                'premoderation_status' => 'A',
                'company_id' => 0,
                'to' => $company_email
            );
        }
        
        if (!empty($mail_params)) {

            $event_dispatcher = Tygh::$app['event.dispatcher'];

            $event_dispatcher->dispatch('cp_warehouses_premoderation.request_approved', [
                'premoderation_request_data' => $mail_params
            ]);
        }
    }
    
    return $result;
}
function fn_cp_disapprove_warehouse($params)
{
    if (!empty($params['location_id'])) {

        $data['status'] = 'F';
        $data['cp_disapprove_reason'] = !empty($params['reason']) ? $params['reason'] : "";

        $result = db_query("UPDATE ?:store_locations SET status = ?s WHERE store_location_id = ?i", 'F', $params['location_id']);
        $result = db_query("UPDATE ?:cp_premoderation_store_locations SET ?u WHERE store_location_id = ?i", $data, $params['location_id']);

        if (!empty($result)) {

            $company_email = fn_cp_get_company_email_by_location_id($params['location_id']);

            if (!empty($company_email)) {
                $mail_params = array(
                    'warehouse' => fn_get_store_location_name($params['location_id']),
                    'premoderation_status' => $data['status'],
                    'company_id' => 0,
                    'to' => $company_email,
                    'reason' => $data['cp_disapprove_reason']
                );
            }

            if (!empty($mail_params)) {

                $event_dispatcher = Tygh::$app['event.dispatcher'];

                $event_dispatcher->dispatch('cp_warehouses_premoderation.request_disapproved', [
                    'premoderation_request_data' => $mail_params
                ]);
            }
        }
    }

    return !empty($result) ? $result : null;
}
function fn_cp_filter_warehouses_by_status(&$products)
{
    foreach ($products as $_pid => &$_pdata) {
        if (!empty($_pdata['extra_warehouse_data'])) {
            fn_cp_filter_warehouses($_pdata['extra_warehouse_data']);
        }
    }
}
function fn_cp_filter_warehouses(&$warehouse_data)
{
    foreach ($warehouse_data as $_wid => $_wdata) {
        if (!fn_cp_check_warehouse_status($_wid)) {
            unset($warehouse_data[$_wid]);
        }
    }
}
function fn_cp_check_warehouse_status($warehouse_id)
{
    $warehouse_status = db_get_field("SELECT status FROM ?:store_locations WHERE store_location_id = ?i", $warehouse_id);

    /* Second check if warehouse on pre-moderation */
    if (!empty($warehouse_status) && ($warehouse_status == 'P' || $warehouse_status == 'F')) {
        /* Check if not first update status in this case, we do not delete the warehouse from the selection*/
        $update = db_get_field('SELECT sl.store_location_id FROM ?:store_locations as sl WHERE sl.store_location_id = ?i AND sl.cp_is_new <> ?s',$warehouse_id, 'Y');
        if (empty($update)) {
            return false;
        }
    }

    return true;
}
function fn_cp_cheсk_main_amount(&$product_data)
{
    $product_warehouses = db_get_array("SELECT warehouse_id, amount FROM ?:warehouses_products_amount WHERE product_id = ?i", $product_data['product_id']);

    if (!empty($product_warehouses)) {
        foreach ($product_warehouses as $_wdata) {
            if (!fn_cp_check_warehouse_status($_wdata['warehouse_id'])) {
                $product_data['amount'] -= $_wdata['amount'];
            }
        }
    }
}
function fn_cp_update_store_location($store_location_data, $store_location_id, $lang_code = DESCR_SL, $action = 'update')
{   
    SecurityHelper::sanitizeObjectData('store_location', $store_location_data);

    $store_location_data['localization'] = !empty($store_location_data['localization']) ? fn_implode_localizations($store_location_data['localization']) : '';
    $store_location_data['main_destination_id'] = !empty($store_location_data['main_destination_id']) && is_numeric($store_location_data['main_destination_id'])
        ? $store_location_data['main_destination_id']
        : null;

    if (!empty($store_location_data['pickup_destinations_ids']) && $action !== 'add') {
        if ($store_location_data['main_destination_id']
            && !in_array($store_location_data['main_destination_id'], $store_location_data['pickup_destinations_ids'])
        ) {
            $store_location_data['pickup_destinations_ids'][] = $store_location_data['main_destination_id'];
        }

        $store_location_data['pickup_destinations_ids'] = implode(',', $store_location_data['pickup_destinations_ids']);
    } else {
        $store_location_data['pickup_destinations_ids'] = $store_location_data['main_destination_id'] ?: '0';
    }

    /**
     * Executes when creating or updating a store location right before the location data is stored in the database.
     * Allows you to modify the saved location data
     *
     * @param array  $store_location_data Store location data
     * @param int    $store_location_id   Store location identifier
     * @param string $lang_code           Two-letter language code
     */

    fn_set_hook('cp_store_locator_update_store_location_before_update', $store_location_data, $store_location_id, $lang_code);

    $store_location_data['status'] = 'P';
    $store_location_data['cp_disapprove_reason'] = "";
    /* change warehouse status */

    if (!empty($store_location_id)) {
        $data['status'] = 'P';
        $data['cp_disapprove_reason'] = "";
        db_query('UPDATE ?:store_locations SET ?u WHERE store_location_id = ?i', $data, $store_location_id); 
    }

    /* change warehouse status */

    if ($store_location_data['store_type'] === Manager::STORE_LOCATOR_TYPE_WAREHOUSE) {
        $store_location_data['pickup_destinations_ids'] = '0';
    } elseif ($store_location_data['store_type'] === Manager::STORE_LOCATOR_TYPE_PICKUP) {
        $store_location_data['shipping_destinations_ids'] = '0';
    }

    if (isset($store_location_data['shipping_destinations'])) {
        $store_location_data['shipping_destinations_ids'] = [];

        $destinations = $store_location_data['shipping_destinations'] ?: [];

        foreach ($destinations as $destination) {
            $destination_id = $destination['destination_id'];
            $store_location_data['shipping_destinations_ids'][] = $destination_id;

            $destination['store_location_id'] = $store_location_id;
            if (empty($destination['position'])) {
                $destination['position'] = 1 + (int) db_get_field(
                    'SELECT MAX(position)'
                    . ' FROM ?:cp_premoderation_store_location_destination_links'
                    . ' WHERE destination_id = ?i',
                    $destination['destination_id']
                );
            }
            db_replace_into('cp_premoderation_store_location_destination_links', $destination);

            $shipping_delay_exists = (bool) db_get_field(
                'SELECT COUNT(*) FROM ?:cp_premoderation_store_location_shipping_delays'
                . ' WHERE destination_id = ?i'
                . ' AND store_location_id = ?i',
                $destination_id,
                $store_location_id
            );

            $language_codes_list = [$lang_code];
            if (!$shipping_delay_exists) {
                $language_codes_list = array_keys(Languages::getAll());
            }

            foreach ($language_codes_list as $language_code) {
                $destination['lang_code'] = $language_code;
                db_replace_into('cp_premoderation_store_location_shipping_delays', $destination);
            }
        }

        $store_location_data['shipping_destinations_ids'] = $store_location_data['shipping_destinations_ids'] ?: [0];

        db_query(
            'DELETE FROM ?:cp_premoderation_store_location_destination_links WHERE store_location_id = ?i AND destination_id NOT IN (?n)',
            $store_location_id,
            $store_location_data['shipping_destinations_ids']
        );

        db_query(
            'DELETE FROM ?:cp_premoderation_store_location_shipping_delays WHERE store_location_id = ?i AND destination_id NOT IN (?n)',
            $store_location_id,
            $store_location_data['shipping_destinations_ids']
        );

        $store_location_data['shipping_destinations_ids'] = implode(',', $store_location_data['shipping_destinations_ids']);
    }

    
    if (empty($store_location_data['store_location_id'])) {
        $store_location_data['store_location_id'] = $store_location_id;

    }
    if (empty($store_location_data['lang_code'])) {
        $store_location_data['lang_code'] = $lang_code;
    }

    db_query('REPLACE INTO ?:cp_premoderation_store_locations ?e ', $store_location_data);

    if ($action == 'add') {
        foreach (Languages::getAll() as $store_location_data['lang_code'] => $v) {
            db_query("INSERT INTO ?:cp_premoderation_store_location_descriptions ?e", $store_location_data);
        }
    }else {
        db_query('REPLACE INTO ?:cp_premoderation_store_location_descriptions ?e ', $store_location_data);
    }
    $action = 'update';
    /**
     * Executes after store location was updated, allows you to update the corresponding data
     *
     * @param array  $store_location_data Set of store locator fields and their values
     * @param int    $store_location_id   Store location identifier
     * @param string $lang_code           Two-letters language code
     * @param string $action              Describe action with store location update or add
     */
    fn_set_hook('cp_store_locator_update_store_location_post', $store_location_data, $store_location_id, $lang_code, $action);

    $auth = Tygh::$app['session']['auth'];

    if (!empty($auth['company_id'])) {
        $mail_params = array(
            'vendor' => fn_get_company_name($auth['company_id']),
            'warehouse' => fn_get_store_location_name($store_location_id),
            'premoderation_status' => 'P',
            'company_id' => $auth['company_id'],
            'to' => Registry::get('settings.Company.company_site_administrator') 
        );
    }
    
    if (!empty($mail_params)) {

        $event_dispatcher = Tygh::$app['event.dispatcher'];

        $event_dispatcher->dispatch('cp_warehouses_premoderation.premoderation_request_created', [
            'premoderation_request_data' => $mail_params
        ]);
    }
    
    if (!isset($store_location_data['external_id'])) {
        return;
    }

    db_replace_into('cp_premoderation_rus_exim_1c_warehouses', [
        'warehouse_id' => $store_location_id,
        'external_id'  => $store_location_data['external_id'],
        'company_id' => !empty(Registry::get('runtime.company_id')) ? Registry::get('runtime.company_id') : 0
    ]);

    return $store_location_id;
}
function fn_cp_replace_warehouse_data($location_id)
{
    $check = $premoderation_data = db_get_field("SELECT store_location_id FROM ?:cp_premoderation_store_locations WHERE store_location_id = ?i",$location_id);
    if (empty($check)) {
        return;
    }

    $replased_tables = array(
        '?:cp_premoderation_store_locations' => '?:store_locations',
        '?:cp_premoderation_store_location_descriptions' => '?:store_location_descriptions',
        '?:cp_premoderation_store_location_shipping_delays' => '?:store_location_shipping_delays',
        '?:cp_premoderation_store_location_destination_links' => '?:store_location_destination_links'
    );
    foreach ($replased_tables as $cp_premoderation_table => $table) {

        $premoderation_data = db_get_row("SELECT * FROM $cp_premoderation_table WHERE store_location_id = ?i",$location_id);
        db_query("REPLACE INTO $table ?e", $premoderation_data);
    
        if ($table == '?:store_location_shipping_delays' || $table == '?:store_location_descriptions') {

            $premoderation_data = db_get_row("SELECT * FROM $cp_premoderation_table WHERE store_location_id = ?i",$location_id);
            foreach (Languages::getAll() as $premoderation_data['lang_code'] => $v) {
                db_query("REPLACE INTO $table ?e", $premoderation_data);
            }            
        }
        db_query("DELETE FROM $cp_premoderation_table WHERE store_location_id = ?i",$location_id);
        unset($premoderation_data);
    }

    /* because rus_exim use warehouse_id */
    $premoderation_data = db_get_row("SELECT * FROM ?:cp_premoderation_rus_exim_1c_warehouses WHERE warehouse_id = ?i",$location_id);
    db_query("UPDATE ?:rus_exim_1c_warehouses SET ?u WHERE warehouse_id = ?i", $premoderation_data, $location_id);
    unset($premoderation_data);

    db_query("DELETE FROM ?:cp_premoderation_rus_exim_1c_warehouses WHERE warehouse_id = ?i",$location_id);
}
function fn_cp_get_store_location($store_location_id, $lang_code = CART_LANGUAGE)
{
    $fields = array(
        '?:cp_premoderation_store_locations.*',
        '?:cp_premoderation_store_location_descriptions.*',
        '?:country_descriptions.country as country_title',
    );

    $join = db_quote(" LEFT JOIN ?:cp_premoderation_store_location_descriptions ON ?:cp_premoderation_store_locations.store_location_id = ?:cp_premoderation_store_location_descriptions.store_location_id AND ?:cp_premoderation_store_location_descriptions.lang_code = ?s", $lang_code);
    $join .= db_quote(" LEFT JOIN ?:country_descriptions ON ?:cp_premoderation_store_locations.country = ?:country_descriptions.code AND ?:country_descriptions.lang_code = ?s", $lang_code);

    $condition = db_quote(" ?:cp_premoderation_store_locations.store_location_id = ?i ", $store_location_id);
    $condition .= (AREA == 'C' && defined('CART_LOCALIZATION')) ? fn_get_localizations_condition('?:cp_premoderation_store_locations.localization') : '';

    /**
     * Executes before store location getting, allows you to modify SQL query parts
     *
     * @param int    $store_location_id Store location identifier
     * @param string $lang_code         Two-letters language code
     * @param array  $fields            List of fields for retrieving
     * @param string $join              String with the complete JOIN information (JOIN type, tables and fields) for an SQL-query
     * @param string $condition         String containing SQL-query condition possibly prepended with a logical operator (AND or OR)
     */
    fn_set_hook('cp_store_locator_get_store_location_before_select', $store_location_id, $lang_code, $fields, $join, $condition);

    /* rus_exim_1c*/
    $fields[] = '?:cp_premoderation_rus_exim_1c_warehouses.external_id';
    $join .= 'LEFT JOIN ?:cp_premoderation_rus_exim_1c_warehouses ON ?:cp_premoderation_store_locations.store_location_id = ?:cp_premoderation_rus_exim_1c_warehouses.warehouse_id ';
    /* rus_exim_1c*/

    $store_location = db_get_row('SELECT ?p FROM ?:cp_premoderation_store_locations ?p WHERE ?p', implode(', ', $fields), $join, $condition);

    if (!empty($store_location['pickup_destinations_ids'])) {
        $store_location['pickup_destinations_ids'] = explode(',', $store_location['pickup_destinations_ids']);
    }

    /**
     * Executes after the store location is obtained, allows you to modify the location data
     *
     * @param int    $store_location_id Store location identifier
     * @param string $lang_code         Two-letters language code
     * @param array  $store_location    Store location data
     */
    fn_set_hook('cp_store_locator_get_store_location_post', $store_location_id, $lang_code, $store_location);

    /*warehouses*/
    if (!empty($store_location['shipping_destinations_ids'])) {
        $store_location['shipping_destinations_ids'] = explode(',', $store_location['shipping_destinations_ids']);
    } elseif (isset($store_location['shipping_destinations_ids'])) {
        $store_location['shipping_destinations_ids'] = [];
    }
    /*warehouses*/

    return $store_location;
}
function fn_cp_get_store_locations($params, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    $params = array_merge([
        'page'              => 1,
        'q'                 => '',
        'match'             => 'any',
        'sort_by'           => 'position_name',
        'sort_order'        => 'asc',
        'items_per_page'    => $items_per_page,
        'store_location_id' => [],
    ], $params);

    $sortings = [
        'position_name' => '?:cp_premoderation_store_locations.position asc, ?:cp_premoderation_store_location_descriptions.name',
    ];

    $fields = [
        'locations'                   => '?:cp_premoderation_store_locations.*',
        'store_location_descriptions' => '?:cp_premoderation_store_location_descriptions.*',
        'country_descriptions'        => '?:country_descriptions.country as country_title',
    ];

    $joins['country_descriptions'] = db_quote(
        'LEFT JOIN ?:country_descriptions ON ?:cp_premoderation_store_locations.country = ?:country_descriptions.code AND ?:country_descriptions.lang_code = ?s',
        $lang_code
    );
    $joins['store_location_descriptions'] = db_quote(
        'LEFT JOIN ?:cp_premoderation_store_location_descriptions'
        . ' ON ?:cp_premoderation_store_locations.store_location_id = ?:cp_premoderation_store_location_descriptions.store_location_id AND ?:cp_premoderation_store_location_descriptions.lang_code = ?s', $lang_code
    );

    $conditions = ['1=1'];

    if ($params['store_location_id']) {
        $conditions['store_location_id'] = db_quote(
            '?:cp_premoderation_store_locations.store_location_id IN (?n)',
            (array) $params['store_location_id']
        );
    }

    // Search string condition for SQL query
    if (!empty($params['q'])) {
        $search_words = [$params['q']];
        $search_type = '';

        if ($params['match'] === 'any' || $params['match'] === 'all') {
            $search_words = explode(' ', $params['q']);
            $search_type = $params['match'] === 'any' ? ' OR ' : ' AND ';
        }

        $search_condition = [];
        foreach ($search_words as $word) {
            $word_conditions = [
                'name'        => db_quote('?:cp_premoderation_store_location_descriptions.name LIKE ?l', "%{$word}%"),
                'city'        => db_quote('?:cp_premoderation_store_location_descriptions.city LIKE ?l', "%{$word}%"),
                'country'     => db_quote('?:cp_premoderation_country_descriptions.country LIKE ?l', "%{$word}%"),
                'description' => db_quote('?:cp_premoderation_store_location_descriptions.description LIKE ?l', "%{$word}%"),
            ];
            $search_condition[] = db_quote('(?p)', implode(' OR ', $word_conditions));
        }

        if (!empty($search_condition)) {
            $conditions['search'] = db_quote('(?p)', implode($search_type, $search_condition));
        }
        unset($word, $word_conditions, $search_condition);
    }

    if (!empty($params['city'])) {
        $conditions['city'] = db_quote('?:cp_premoderation_store_location_descriptions.city = ?s', $params['city']);
    }

    if (!empty($params['pickup_only'])) {
        $conditions['pickup_only'] = db_quote('main_destination_id IS NOT NULL');
    }

    if (!empty($params['company_id'])) {
        if (is_array($params['company_id'])) {
            $conditions['company_id'] = db_quote('?:cp_premoderation_store_locations.company_id IN (?n)', $params['company_id']);
        } elseif (fn_get_company_condition('?:cp_premoderation_store_locations.company_id', true, $params['company_id'])) {
            $conditions['company_id'] = fn_get_company_condition('?:cp_premoderation_store_locations.company_id', false, $params['company_id']);
        }
    }

    if (!empty($params['pickup_destination_id'])) {
        $conditions['pickup_destination_id'] = db_quote('FIND_IN_SET(?n, pickup_destinations_ids)', $params['pickup_destination_id']);
    }

    if (!empty($params['main_destination_id'])) {
        $conditions['main_destination_id'] = db_quote('main_destination_id = ?i', $params['main_destination_id']);
    }

    if (!empty($params['company_status'])) {
        $joins['company'] = db_quote('LEFT JOIN ?:companies ON ?:cp_premoderation_store_locations.company_id = ?:companies.company_id');
        $conditions['company_status'] = db_quote('?:companies.status = ?s', $params['company_status']);
    }

    /**
     * Change SQL parameters for store locations selection
     *
     * @param array    $params
     * @param array    $fields         List of fields for retrieving
     * @param string   $joins          String with the complete JOIN information (JOIN type, tables and fields) for an SQL-query
     * @param string   $conditions     String containing SQL-query condition possibly prepended with a logical operator (AND or OR)
     * @param string[] $sortings       Possible sortings for a query
     * @param int      $items_per_page Amout of items per page
     * @param string   $lang_code      Two-letter language code
     */
    
    fn_set_hook('cp_get_store_locations_before_select', $params, $fields, $joins, $conditions, $sortings, $items_per_page, $lang_code);

    if (!empty($params['store_status'])) {
        $conditions[] .= db_quote(" ?:cp_premoderation_store_locations.status = ?s ", $params['store_status']);
    }

    if (!empty($params['store_types'])) {
        $store_types = (array) $params['store_types'];
        $conditions['store_types'] = db_quote('store_type IN (?a)', $store_types);
    } elseif (AREA == 'C') {
        $conditions['not_warehouse'] = db_quote('store_type <> ?s', Manager::STORE_LOCATOR_TYPE_WAREHOUSE);
    }

    $sortings['destination_position_name'] = 'position asc, ?:cp_premoderation_store_location_descriptions.name';

    $get_destinations_data = !empty($params['shipping_destination_id'])
        || !empty($params['pickup_destination_id']);
    $destination_id = null;

    if (!empty($params['shipping_destination_id'])) {
        $destination_id = $params['shipping_destination_id'];
        $conditions['destination_id'] = db_quote(
            'FIND_IN_SET(?i, shipping_destinations_ids)',
            $params['shipping_destination_id']
        );
    }

    if ($get_destinations_data) {
        $fields['link_id'] = 'destination_links.link_id';
        $fields['position'] = '(CASE'
            . ' WHEN destination_links.position IS NOT NULL'
            . ' THEN destination_links.position'
            . ' ELSE ?:cp_premoderation_store_locations.position'
            . ' END) AS position';
        $fields['warn_about_delay'] = 'destination_links.warn_about_delay';
        $joins['destination_links'] = db_quote(
            'LEFT JOIN ?:cp_premoderation_store_location_destination_links AS destination_links'
            . ' ON destination_links.store_location_id = ?:cp_premoderation_store_locations.store_location_id'
            . ' AND destination_links.destination_id = ?i',
            $destination_id
        );

        $fields['shipping_delay'] = 'shipping_delays.shipping_delay';
        $joins['store_location_shipping_delays'] = db_quote(
            'LEFT JOIN ?:cp_premoderation_store_location_shipping_delays AS shipping_delays'
            . ' ON shipping_delays.store_location_id = ?:cp_premoderation_store_locations.store_location_id'
            . ' AND shipping_delays.destination_id = ?i'
            . ' AND shipping_delays.lang_code = ?s',
            $destination_id,
            $lang_code
        );

        $fields['main_destination'] = 'destination_descriptions.destination AS main_destination';
        $joins['destination_descriptions'] = db_quote(
            'LEFT JOIN ?:destination_descriptions AS destination_descriptions'
            . ' ON destination_descriptions.destination_id = ?:cp_premoderation_store_locations.main_destination_id'
            . ' AND destination_descriptions.lang_code = ?s',
            $lang_code
        );
    }

    $join = implode(' ', $joins);
    $condition = implode(' AND ', $conditions);
    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field('SELECT COUNT(?:cp_premoderation_store_locations.store_location_id) FROM ?:cp_premoderation_store_locations ?p WHERE 1=1 AND ?p', $join, $condition);
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }
    $sorting = db_sort($params, $sortings);

    $data = db_get_hash_array(
        'SELECT ?p FROM ?:cp_premoderation_store_locations ?p'
        . ' WHERE 1=1'
        . ' AND ?p'
        . ' GROUP BY ?:cp_premoderation_store_locations.store_location_id'
        . ' ?p'
        . ' ?p',
        'store_location_id',
        implode(', ', $fields),
        $join,
        $condition,
        $sorting,
        $limit
    );

    /**
     * Executes after the store locations are obtained, allows you to modify locations data
     *
     * @param array  $params         Request parameters
     * @param int    $items_per_page Amount of items per page
     * @param string $lang_code      Two-letter language code
     * @param array  $data           List of store locations
     */
    fn_set_hook('cp_store_locator_get_store_locations_post', $params, $items_per_page, $lang_code, $data);

    return [$data, $params];
}
function fn_cp_get_company_email_by_location_id($location_id)
{
    $company_email = db_get_field("SELECT email FROM ?:companies as c 
                                    INNER JOIN ?:store_locations as sl ON sl.company_id = c.company_id 
                                    WHERE store_location_id = ?i",$location_id);

    return !empty($company_email) ? $company_email : null;
}
function fn_cp_disable_premoderation_warehouses()
{

}