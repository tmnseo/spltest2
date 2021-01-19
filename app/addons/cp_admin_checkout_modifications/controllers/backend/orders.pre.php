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
   
   if ($mode == 'update_details') {
      if (!empty($_REQUEST['cp_data']['cp_payment_order_date'])) {
         $_REQUEST['cp_data']['cp_payment_order_date'] = strtotime($_REQUEST['cp_data']['cp_payment_order_date']);
      }
      if (!empty($_REQUEST['cp_data']['cp_planned_time_issuing_order'])) {
         $_REQUEST['cp_data']['cp_planned_time_issuing_order'] = strtotime($_REQUEST['cp_data']['cp_planned_time_issuing_order']);

         /* NOTIFICATION FOR CUSTOMER AND ADMIN */
         if (!empty(Registry::get('runtime.company_id'))) {
            
            if (!empty($_REQUEST['order_id'])) {

               $event_dispatcher = Tygh::$app['event.dispatcher'];
               $formatter = Tygh::$app['formatter'];
               $request_data['issuing_time'] = $formatter->asDatetime($_REQUEST['cp_data']['cp_planned_time_issuing_order'], "%d/%m/%y");
               $request_data['order_id'] = $_REQUEST['order_id'];
               
               $notification_data = db_get_row("SELECT company_id, email FROM ?:orders WHERE order_id = ?i", $_REQUEST['order_id']);

               if (!empty($notification_data['email'])) {

                  $request_data['email'] = $notification_data['email'];

                  $order_info = fn_get_order_info($_REQUEST['order_id']);
                  $request_data['cancel_href'] = fn_cp_generate_auth_link($order_info, "orders.cp_cancel_order?order_id=" . $_REQUEST['order_id']);
                  
                  $event_dispatcher->dispatch('cp_additional_email_templates.planned_time_issuing_order_change_for_customer', [
                     'request_data' => $request_data
                  ]);
               }

               $request_data['email'] = Registry::get('settings.Company.company_site_administrator');
               $event_dispatcher->dispatch('cp_additional_email_templates.planned_time_issuing_order_change_for_admin', [
                  'request_data' => $request_data
               ]);
            }
            
         }
         /* NOTIFICATION FOR CUSTOMER AND ADMIN */

      }

      db_query('UPDATE ?:orders SET ?u WHERE order_id = ?i', $_REQUEST['cp_data'], $_REQUEST['order_id']);
    
      if (!empty($_REQUEST['shipment_id']) && isset($_REQUEST['cp_data']['cp_tracking_number_ajax'])) {
          db_query('UPDATE ?:shipments SET tracking_number = ?s WHERE shipment_id = ?i', $_REQUEST['cp_data']['cp_tracking_number_ajax'], $_REQUEST['shipment_id']);
      }elseif (empty($_REQUEST['shipment_id']) && !empty($_REQUEST['cp_data']['cp_tracking_number_ajax']) && !empty($_REQUEST['shipping_id']) && !empty($_REQUEST['order_id'])) {
        $shipment_data = array(
          'tracking_number' => $_REQUEST['cp_data']['cp_tracking_number_ajax'],
          'shipping_id' => $_REQUEST['shipping_id'],
          'order_id' => $_REQUEST['order_id'],
        );

        fn_update_shipment($shipment_data,0 ,0 , true);
      }

    }elseif ($mode == 'cp_update_totals') {
      
      fn_clear_cart($cart, true);
      $customer_auth = fn_fill_auth(array(), array(), false, 'C');

      $cart_status = md5(serialize($cart));
      fn_form_cart($_REQUEST['order_id'], $cart, $customer_auth, !empty($_REQUEST['copy']));

      fn_store_shipping_rates($_REQUEST['order_id'], $cart, $customer_auth);

      $params = $_REQUEST;
      
      $params['cart_products'] = !empty($cart['products']) ? $cart['products'] : [];
      if (!empty($params['cart_products'])) {
        foreach ($params['cart_products'] as $key => &$_pdata) {
          $_pdata['stored_price'] = 'Y';
        }
      }
      
      if (!empty($cart['product_groups']) && isset($params['cp_stored_shipping_cost'])) {
          foreach ($cart['product_groups'] as $group_key => &$group) {
              if (!empty($group['chosen_shippings'])) {
                  foreach ($group['chosen_shippings'] as $shipping_key => $shipping) {
                      $params['stored_shipping'][$group_key][$shipping_key] = 'Y';
                      $old_shipping_cost = !empty($shipping['rate']) ? $shipping['rate'] : 0;
                      $params['stored_shipping_cost'][$group_key][$shipping_key] = $params['cp_stored_shipping_cost'];
                      $group['shippings'][$shipping['shipping_id']]['rate'] = $params['cp_stored_shipping_cost'];
                  }
              }
          }
      }
      
      fn_update_cart_by_data($cart, $params, $customer_auth);
      $cart['order_id'] = $params['order_id'];
      

      $force_notification = fn_get_notification_rules($params);
      
      
      list($order_id, $action) = fn_cp_place_order_manually($cart, $_REQUEST, $customer_auth, 'save', $auth['user_id'], $force_notification);
      
          if ($order_id) {
              $req_data = array(
            'order_id' => $order_id,
            'label' => 'cp_admin_checkout_modifications.change_shipping_cost',
            'description' => __('cp_edost_improvement.shipping_cost_change', ["[from]" => $old_shipping_cost, "[to]" => $params['cp_stored_shipping_cost']]),
            'notice' => ''
          );
          $put_data = array(
              'controller' => 'orders',
              'mode' => '',
              'method' => 'post',
              'timestamp' => time(),
              'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
              'object_id' => $order_id,
              'request' => json_encode($req_data)
          );
          fn_cp_megalog_ml_add_log($put_data);
          return array(CONTROLLER_STATUS_REDIRECT, 'orders.details?order_id='.$order_id);
      }
    }
}