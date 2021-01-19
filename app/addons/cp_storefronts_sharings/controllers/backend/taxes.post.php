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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if ($mode == 'm_update') {
      if ($auth['user_type'] == 'V') {
         
         $storefront_id = empty($_REQUEST['storefront_id']) ? NULL : $_REQUEST['storefront_id'];
         if (!empty($_REQUEST['tax_data']) && !empty($storefront_id)) {
            foreach ($_REQUEST['tax_data'] as $tax_id => $tax_data) {
               fn_cp_update_vendor_primary_tax($storefront_id, $auth['company_id'], $tax_id);
            }
         }elseif (empty($_REQUEST['tax_data'])) {
            db_query('UPDATE ?:cp_vendor_taxes SET is_primary = ?s WHERE storefront_id = ?i AND vendor_id = ?i','N' ,$storefront_id, $auth['company_id']); 
         }
         return array(CONTROLLER_STATUS_OK, "taxes.manage&s_storefront=$storefront_id");
      }
   }
}
if ($mode == 'update' || $mode == 'add') {
   if ($auth['user_type'] == 'A') {
      Registry::set('navigation.tabs.storefronts', [
         'title' => __('storefronts'),
         'js' => true,
      ]);
      
      if (isset($_REQUEST['tax_id']) && !empty($_REQUEST['tax_id'])) {
         $selected_storefront_ids = fn_cp_get_selected_storefronts($_REQUEST['tax_id'], 'tax_id', '?:cp_storefronts_taxes');
      }
      if (!empty($selected_storefront_ids)) {
         Tygh::$app['view']->assign('cp_storefront_ids',$selected_storefront_ids);
      }
   }
} elseif ($mode == 'manage') {
   if ($auth['user_type'] == 'V') {
      Tygh::$app['view']->assign('cp_is_vendor', true);

      $availibility_storefronts = fn_cp_get_selected_storefronts($auth['company_id'], 'company_id', '?:storefronts_companies');
      /*check storefront*/
      $all_storefronts = db_get_fields("SELECT storefront_id FROM ?:storefronts WHERE storefront_id NOT IN (?n)",$availibility_storefronts);
      
      if (!empty($all_storefronts)) {
         foreach ($all_storefronts as $_sid) {
            $check = db_get_fields("SELECT company_id FROM ?:storefronts_companies WHERE storefront_id = ?i",$_sid);
            if (empty($check)) {
               $availibility_storefronts[] = $_sid;
            }
         }
      }
      /*check storefront*/
      Tygh::$app["storefront.switcher.is_enabled"] = true;
      
      $taxes = Tygh::$app['view']->getTemplateVars('taxes');
      $storefront_id = empty($_REQUEST['s_storefront']) ? $availibility_storefronts[0] : $_REQUEST['s_storefront'];

      if (isset($taxes) && !empty($taxes) && !empty($storefront_id)) {
         foreach ($taxes as $tax_id => $tax) {
            fn_cp_filter_taxes($storefront_id, $auth['company_id'], $taxes, $tax_id);
         }
      }
      Tygh::$app['view']->assign('selected_storefront_id', $storefront_id);
      Tygh::$app['view']->assign('storefront_id', $storefront_id);
      Tygh::$app['view']->assign('taxes', $taxes);
   }
}