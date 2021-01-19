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

namespace Tygh\Addons\CpInvoicesForAccounting\InvoiceManager;

class InvoiceManager
{
    
    public static function getUnloadedInvoices($params, $items_per_page, $lang_code = CART_LANGUAGE)
    {   
        $default_params = array(
            'page' => 1,
            'items_per_page' => $items_per_page
        );

        $params = array_merge($default_params, $params);

        $fields = [
            'order_id',
            'status',
            'cp_payment_order_number'
        ];
        $limit = '';
        
        $condition = db_quote(" cp_payment_order_number != ?s AND cp_was_download_for_accounting = ?s", '', 'N');

        if (!empty($params['cp_statuses_after_paid'])) {
            $condition .= db_quote(" AND status in (?a)", $params['cp_statuses_after_paid']);
        }

        if (!empty($params['items_per_page']) && empty($params['get_all'])) {

            $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:orders WHERE $condition");
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        }

        $invoices = db_get_array("SELECT " . implode(',', $fields) . " FROM ?:orders WHERE $condition $limit");
        
        $statuses_descr = db_get_hash_array("SELECT status, sd.description FROM ?:statuses as s
            LEFT JOIN ?:status_descriptions as sd ON sd.status_id = s.status_id AND sd.lang_code = ?s WHERE s.type = ?s", 'status', $lang_code, STATUSES_ORDER);
        
        if (!empty($invoices) && !empty($statuses_descr)) {

            foreach ($invoices as &$invoice_data) {

                if (!empty($invoice_data['status'])) {

                    $invoice_data['status_descr'] = $statuses_descr[$invoice_data['status']]['description'];
                }
            }
        }

        return array($invoices, $params);
    }

    public static function markAsUploaded($order_id)
    {
        db_query("UPDATE ?:orders SET cp_was_download_for_accounting = ?s WHERE order_id = ?i", 'Y', $order_id);
    }
}