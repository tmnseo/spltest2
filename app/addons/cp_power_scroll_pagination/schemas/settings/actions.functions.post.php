<?php

use Tygh\Http;
use Tygh\Registry;

function fn_settings_actions_addons_cp_power_scroll_pagination(&$new_value, $old_value) {

	//fn_cp_check_license_20($new_value, $old_value, ($_REQUEST['id'])?$_REQUEST['id']:$_REQUEST['addon']);

	return true;
}

if (function_exists('fn_cp_check_license_20') != true) {
	function fn_cp_check_license_20($new_value, $old_value, $name) {
	
        if (fn_allowed_for('MULTIVENDOR') != true) {
           $companies = db_get_array('SELECT storefront, secure_storefront FROM ?:companies');
        } else {
           $companies = array(array('storefront' => fn_url('', 'C', 'http')));
        }
        
        $store_ip = fn_get_ip();
        $store_ip = $store_ip['host'];

        $_cp_req = array(
            'companies' => $companies,
            'addon' => $name,
            'license' => Registry::get('addons.'. $name . '.licensekey'),
            'addon_status' => $new_value,
            'ver' => PRODUCT_VERSION,
            'product_status' => PRODUCT_STATUS,
            'product_build' => strtoupper(PRODUCT_BUILD),
            'edition' => PRODUCT_EDITION,
            'lang' => strtoupper(CART_LANGUAGE),
            'store_uri' => fn_url('', 'C', 'http'),
            'secure_store_uri' => fn_url('', 'C', 'https'),
            'https_enabled' => (Registry::get('settings.General.secure_checkout') == 'Y' || Registry::get('settings.General.secure_admin') == 'Y' || Registry::get('settings.General.secure_auth') == 'Y') ? 'Y' : 'N',
            'admin_uri' => fn_url('', 'A', 'http'),
            'store_host' => Registry::get('config.http_host'),
            'store_ip' => $store_ip
        );
			
		$request = json_encode($_cp_req);

		$check_host = "http://cart-power.com/index.php?dispatch=check_license_20.check";
		
		$data = Http::post($check_host, array('request' => urlencode($request)), array(
			'timeout' => 60
		));
				
		preg_match('/\<status\>(.*)\<\/status\>/u', $data, $result);
		  
		$_status = 'FALSE';
		if (isset($result[1])) {
		  $_status = $result[1];
		}
					
		if ($_REQUEST['dispatch'] == 'addons.update_status' && $_status != 'TRUE') {
		  db_query("UPDATE ?:addons SET status = ?s WHERE addon = ?s", 'D', $name);
		  fn_set_notification('W', __('warning'), __('cp_your_license_is_not_valid'));		  
		  exit;
		}
		
		return true;
	}
}