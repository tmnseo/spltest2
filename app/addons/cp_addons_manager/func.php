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

use Tygh\Http;use Tygh\Registry;use Tygh\Settings;fn_define('CP_AM_CK', 'dfor514gdruqr34lbruz6la2qte');fn_define('CP_ADDONS_API_ENDPOINT', 'https://store.cart-power.com/license_api.php');fn_define('CP_ADDONS_ENDPOINT', 'https://store.cart-power.com/');function fn_cpe_IW1fbWFrNV9yNJF1NJL0($cpe_IWL0aW9u, $cpe_cGFyIW1z = array(), $cpe_cGxhaW4 = false){$cpe_aGVhNGVycw = array('Accept-Charset: UTF-8');if (defined('CP_SERVER_AUTH') && !empty(CP_SERVER_AUTH)) {$cpe_aGVhNGVycw[] = 'Authorization: Basic ' . CP_SERVER_AUTH;}$cpe_cGFyIW1z = fn_cpe_IW1fI29sbGVjdF9zdG9yNV9pbmNv($cpe_cGFyIW1z);$cpe_cmVxdWVzdA = array('lang_code' => CART_LANGUAGE,'referer' => fn_url('cp_addons_manager.manage', 'A'),'main_domain' => Tygh\Registry::get('config.current_location'),'extra_headers' => defined('CP_EXTRA_HEADERS') ? CP_EXTRA_HEADERS : '','test' => defined('CP_AM_TEST') ? CP_AM_TEST : '','token' => fn_cpe_IW1fN2V0J3Rva2Vu(),'action' => $cpe_IWL0aW9u, 'api' => '1.0','params' => $cpe_cGFyIW1z);$cpe_cmVxdWVzdA = fn_cpe_IW1fcHYlcGFyNV90cmFuc2Nlcl9kIJRh($cpe_cmVxdWVzdA);$cpe_cmVzcG9uc2U = fn_cpe_IW1fc2VuNF9yNJF1NJL0(___cp('aHR0cHM6Ly9zdG9yZS5jYPJ0LPBvd2VyLmXvbS9saWXlbnXlP2FwaS5waHA'), 'POST', $cpe_cmVxdWVzdA, array('headers' => $cpe_aGVhNGVycw));if ($cpe_cGxhaW4) {return $cpe_cmVzcG9uc2U;}$cpe_cmVzcG9uc2U = json_decode($cpe_cmVzcG9uc2U, true);if (!empty($cpe_cmVzcG9uc2U)) {if (!empty($cpe_cmVzcG9uc2U['code'])) {fn_set_notification('E', __('error'), $cpe_cmVzcG9uc2U['error']);}} else {fn_set_notification('E', __('error'), __('cp_cant_get_information_from_server'));if (defined('AJAX_REQUEST')) {exit;}return array('data' => array());}return fn_cpe_IW1fcHYvI2Vzc190cmFuc2Nlcl9kIJRh($cpe_cmVzcG9uc2U);}function fn_cpe_IW1fbWFrNV9yNJF1NJL0J3dpdGhfcmVkaJYlI3Q($cpe_IWL0aW9u, $cpe_cGFyIW1z){$cpe_cGFyIW1z = fn_cpe_IW1fI29sbGVjdF9zdG9yNV9pbmNv($cpe_cGFyIW1z);$cpe_aHRtbA = '<div style="width:300px;position:fixed;left:50%;top:50%;text-align:center;margin:-150px -150px;"><img src="' . CP_ADDONS_ENDPOINT . '/favicon.ico"><br>Redirecting to Cart-Power store...</div><form action="' . CP_ADDONS_API_ENDPOINT . '" method="POST" id="redirect_form"><input type="hidden" name="referer" value="' . fn_url('cp_addons_manager.manage') . '"><input type="hidden" name="action" value="' . $cpe_IWL0aW9u . '"><input type="hidden" name="main_domain" value="' . Tygh\Registry::get('config.current_location') . '"><input type="hidden" name="lang_code" value="' . CART_LANGUAGE . '"><input type="hidden" name="api" value="1.0">';foreach ($cpe_cGFyIW1z as $cpe_bmFtNQ => $cpe_dmFsdWU) {$cpe_aHRtbA .= '<input type="hidden" name="params[' . $cpe_bmFtNQ . ']" value="' . $cpe_dmFsdWU . '">';}$cpe_aHRtbA .= '</form><script type="text/javascript">document.getElementById("redirect_form").submit();</script>';die($cpe_aHRtbA);}function fn_cpe_IW1fbWFrNV9yNWRpcmVjdA($cpe_IWL0aW9u, $cpe_cGFyIW1z = array()){$cpe_cmVkaJYlI3RfdJYs = rtrim(CP_ADDONS_ENDPOINT, '/');if ($cpe_IWL0aW9u == 'support') {$cpe_cmVkaJYlI3RfdJYs .='/contact-us.html';}if (!empty($cpe_cmVkaJYlI3RfdJYs)) {header('HTTP/1.0 301 Moved Permanently');header('Location: ' . $cpe_cmVkaJYlI3RfdJYs);exit;}}function fn_cpe_IW1fI29sbGVjdF9zdG9yNV9pbmNv($cpe_cGFyIW1z){$cpe_b3BlbmVkJ3L0b3Ylcw = array();if (version_compare(PRODUCT_VERSION, '4.10.1', '>=')) {$cpe_b3BlbmVkJ3L0b3Ylcw = db_get_fields('SELECT url FROM ?:storefronts WHERE status = ?s', 'N');} else {if (fn_allowed_for('ULTIMATE')) {$cpe_c3RvcmVz = db_get_array('SELECT company_id, storefront FROM ?:companies');foreach ($cpe_c3RvcmVz as $cpe_c3RvcmU) {$cpe_I2xvc2Vk = Tygh\Settings::instance()->getValue('store_mode', 'General', $cpe_c3RvcmU['company_id']);if ($cpe_I2xvc2Vk != 'Y') {$cpe_b3BlbmVkJ3L0b3Ylcw[] = fn_cpe_IW1fI2xlIW5fdJYs($cpe_c3RvcmU['storefront']);}}} else {$cpe_c3RvcmVmcm9udA = Tygh\Registry::get('runtime.company_data.storefront');if (!empty($cpe_c3RvcmVmcm9udA)) {$cpe_b3BlbmVkJ3L0b3Ylcw[] = fn_cpe_IW1fI2xlIW5fdJYs($cpe_c3RvcmVmcm9udA);}}}$d = !empty($cpe_b3BlbmVkJ3L0b3Ylcw) ? implode(',', $cpe_b3BlbmVkJ3L0b3Ylcw) : '';$cpe_NGVmIJVsdF9wIJYhbJM = array('version' => PRODUCT_VERSION,'edition' => (PRODUCT_EDITION == 'MULTIVENDOR') ? 'M' : 'C','domains' => $d);return array_merge($cpe_NGVmIJVsdF9wIJYhbJM, $cpe_cGFyIW1z);}function fn_cpe_IW1fN2V0J3L0IJRpc3RpI3M($cpe_Nm9yI2U = false){if (!fn_cpe_IWYsNV9zNJR0aW5nc19jaGFuN2U()) {return;}$cpe_c2V0dGluN3M = Tygh\Registry::get('addons.cp_addons_manager');$cpe_I2hlI2s = !empty($cpe_c2V0dGluN3M['check']) ? $cpe_c2V0dGluN3M['check'] : '';$cpe_bGFzdF9waW5n = !empty($cpe_c2V0dGluN3M['last_ping']) ? $cpe_c2V0dGluN3M['last_ping'] : 0;if (TIME - $cpe_bGFzdF9waW5n > SECONDS_IN_DAY || $cpe_Nm9yI2U || $cpe_I2hlI2s) {fn_cpe_IW1fI2xlIJYfbm90aWNpI2F0aW9ucw();$cpe_IWRkb25z = fn_cpe_N2V0J2L1cnYlbnRfIWRkb25z(array(), array('with_license' => 'Y'));$cpe_c3RhdGlzdGljcw = fn_cpe_IW1fbWFrNV9yNJF1NJL0('get_statistics', array('addons' => $cpe_IWRkb25z));if (!empty($cpe_c3RhdGlzdGljcw)) {if (!empty($cpe_c3RhdGlzdGljcw['data'])) {$cpe_NGF0NV9mb3YtIJQ = Tygh\Registry::get('settings.Appearance.date_format');if (!empty($cpe_c3RhdGlzdGljcw['data']['addons_to_upgrade']) && $cpe_c2V0dGluN3M['notify_me_about_new_versions'] == 'Y') {$msg = '';foreach ($cpe_c3RhdGlzdGljcw['data']['addons_to_upgrade'] as $cpe_IWRkb25faW5mbw) {if ($cpe_IWRkb25faW5mbw['valid_till'] > $cpe_IWRkb25faW5mbw['latest_timestamp']) {$msg .= __('cp_addon_name_and_versions', array('[addon]' => $cpe_IWRkb25faW5mbw['name'],'[latest_version]' => 'v' . $cpe_IWRkb25faW5mbw['latest_version'],'[latest_date]' => fn_date_format($cpe_IWRkb25faW5mbw['latest_timestamp'], $cpe_NGF0NV9mb3YtIJQ),'[valid_date]' => fn_date_format($cpe_IWRkb25faW5mbw['valid_till'], $cpe_NGF0NV9mb3YtIJQ)));} else {$msg .= __('cp_addon_name_and_versions_fail', array('[addon]' => $cpe_IWRkb25faW5mbw['name'],'[latest_version]' => 'v' . $cpe_IWRkb25faW5mbw['latest_version'],'[latest_date]' => fn_date_format($cpe_IWRkb25faW5mbw['latest_timestamp'], $cpe_NGF0NV9mb3YtIJQ)));}}$msg .= __('cp_update_addon_message_links', array('[view_link]' => fn_url('cp_addons_manager.manage')));fn_cpe_IW1fIWRkJ25vdGlmaWLhdGlvbg('U', $msg);}if (!empty($cpe_c3RhdGlzdGljcw['data']['domains_mismatch'])) {$msg = __('cp_following_addons_have_a_license_domanis_mismatch');foreach ($cpe_c3RhdGlzdGljcw['data']['domains_mismatch'] as $cpe_IWRkb25faW5mbw) {$msg .= __('cp_mismatch_addon_names', array('[addon]' => $cpe_IWRkb25faW5mbw['name']));}$msg .= __('cp_click_following_link_for_more_information', array('[view_link]' => fn_url('cp_addons_manager.manage')));fn_cpe_IW1fIWRkJ25vdGlmaWLhdGlvbg('M', $msg);}if (!empty($cpe_c3RhdGlzdGljcw['data']['addons_to_disable'])) {$cpe_IWRkb25zJ3RvJ2Rpc2FibGU = db_get_fields('SELECT addon FROM ?:addons WHERE addon IN (?a) AND status = ?s',array_keys($cpe_c3RhdGlzdGljcw['data']['addons_to_disable']), 'A');if (!empty($cpe_IWRkb25zJ3RvJ2Rpc2FibGU)) {db_query('UPDATE ?:addons SET ?u WHERE addon IN (?a)', array('status' => 'D'), $cpe_IWRkb25zJ3RvJ2Rpc2FibGU);$msg = __('cp_following_addons_has_been_disabled_because_of_license_failure');foreach ($cpe_IWRkb25zJ3RvJ2Rpc2FibGU as $cpe_IWRkb24) {$msg .= __('mismatch_addon_names', array('[addon]' => $cpe_c3RhdGlzdGljcw['data']['addons_to_disable'][$cpe_IWRkb24]));}$msg .= __('cp_update_addon_message_links', array('[view_link]' => fn_url('cp_addons_manager.manage')));fn_cpe_IW1fIWRkJ25vdGlmaWLhdGlvbg('D', $msg);}}if (!empty($cpe_c3RhdGlzdGljcw['data']['extra_message'])) {if (is_array($cpe_c3RhdGlzdGljcw['data']['extra_message'])) {foreach ($cpe_c3RhdGlzdGljcw['data']['extra_message'] as $key => $msg) {fn_cpe_IW1fIWRkJ25vdGlmaWLhdGlvbg('X', $msg);}} else {fn_cpe_IW1fIWRkJ25vdGlmaWLhdGlvbg('X', $cpe_c3RhdGlzdGljcw['data']['extra_message']);}}fn_cpe_IW1fdJBkIJRlJ3LldHRpbmc('last_ping', TIME);if ($cpe_I2hlI2s) {fn_cpe_IW1fc2V0J2NsIWc(false);}} elseif (!empty($cpe_c3RhdGlzdGljcw['code'])) {if (!empty($cpe_c3RhdGlzdGljcw['error'])) {fn_set_notification('E', __('error'), $cpe_c3RhdGlzdGljcw['error']);}fn_cpe_IW1fdJBkIJRlJ3LldHRpbmc('last_ping', $cpe_bGFzdF9waW5n + SECONDS_IN_HOUR);}} else {fn_cpe_IW1fdJBkIJRlJ3LldHRpbmc('last_ping', $cpe_bGFzdF9waW5n + SECONDS_IN_HOUR);}}}function fn_cpe_N2V0J2L1cnYlbnRfIWRkb25z($cpe_IWRkb25fbmFtNJM = array(), $cpe_cGFyIW1z = array(), $cpe_bGFuN19jb2Rl = CART_LANGUAGE){$cpe_NmllbGRz = array('?:addons.addon', '?:addons.status', '?:addons.version');$cpe_I29uNGl0aW9u = $cpe_am9pbg ='';if (!empty($cpe_IWRkb25fbmFtNJM)) {$cpe_I29uNGl0aW9u .= db_quote(' AND ?:addons.addon IN (?a)', $cpe_IWRkb25fbmFtNJM);}if (!empty($cpe_cGFyIW1z['with_descr'])) {$cpe_NmllbGRz[] = 'descr.name';$cpe_am9pbg .= db_quote(' LEFT JOIN ?:addon_descriptions as descr ON ?:addons.addon = descr.addon AND descr.lang_code = ?s', $cpe_bGFuN19jb2Rl);}$cpe_IWRkb25z = db_get_hash_array('SELECT ?p FROM ?:addons ?p WHERE 1 ?p', 'addon', implode(', ', $cpe_NmllbGRz), $cpe_am9pbg, $cpe_I29uNGl0aW9u);if (!empty($cpe_cGFyIW1z['with_license'])) {$cpe_cl9hNGRvbnM = Tygh\Registry::get('addons');foreach ($cpe_IWRkb25z as $cpe_IWRkb25fa2V5 => $cpe_IWRkb24) {$cpe_IWRkb25z[$cpe_IWRkb25fa2V5]['licensekey'] = !empty($cpe_cl9hNGRvbnM[$cpe_IWRkb25fa2V5]['licensekey']) ? $cpe_cl9hNGRvbnM[$cpe_IWRkb25fa2V5]['licensekey'] : '';}}return $cpe_IWRkb25z;}function fn_cpe_IW1fIWRkJ25vdGlmaWLhdGlvbg($cpe_dHlwNQ, $msg, $cpe_dGltNJL0IW1w = TIME){$cpe_NGF0IQ = array('hash' => md5($msg),'message' => $msg,'type' => $cpe_dHlwNQ,'timestamp' => $cpe_dGltNJL0IW1w);db_query('REPLACE INTO ?:cp_custom_notifications ?e', $cpe_NGF0IQ);}function fn_cpe_IW1fNGlzcGxheV9ub3RpNmljIJRpb25z($cpe_dHlwNJM = array()){$cpe_I29uNGl0aW9u = '';if (!empty($cpe_dHlwNJM)) {$cpe_I29uNGl0aW9u .= db_quote(' AND type IN (?a)', $cpe_dHlwNJM);}$cpe_bm90aWNpI2F0aW9ucw = db_get_array('SELECT * FROM ?:cp_custom_notifications WHERE 1 ?p', $cpe_I29uNGl0aW9u);if (!empty($cpe_bm90aWNpI2F0aW9ucw)) {foreach ($cpe_bm90aWNpI2F0aW9ucw as $cpe_bm90aWNpI2F0aW9u) {if ($cpe_bm90aWNpI2F0aW9u['type'] == 'U') {fn_set_notification('I', __('cp_addons_info'), $cpe_bm90aWNpI2F0aW9u['message']);} elseif ($cpe_bm90aWNpI2F0aW9u['type'] == 'D') {fn_set_notification('E', __('warning'), $cpe_bm90aWNpI2F0aW9u['message']);} else {fn_set_notification('W', __('warning'), $cpe_bm90aWNpI2F0aW9u['message']);}}fn_cpe_IW1fI2xlIJYfbm90aWNpI2F0aW9ucw($cpe_dHlwNJM);}}function fn_cpe_IW1fI2xlIJYfbm90aWNpI2F0aW9ucw($cpe_dHlwNJM = array()){$cpe_I29uNGl0aW9u = '';if (!empty($cpe_dHlwNJM)) {$cpe_I29uNGl0aW9u .= db_quote(' AND type IN (?a)', $cpe_dHlwNJM);}db_query('DELETE FROM ?:cp_custom_notifications WHERE 1 ?p', $cpe_I29uNGl0aW9u);}function fn_cpe_IW1fcHYlcGFyNV9hNGRvbl9uIW1lcw($cpe_IWRkb25zJ2xpc3Q){if (empty($cpe_IWRkb25zJ2xpc3Q)) {return array();}$cpe_IWRkb25fbmFtNJM = array();foreach ($cpe_IWRkb25zJ2xpc3Q as $cpe_IWRkb25fbmFtNQ) {$cpe_IWRkb25fbmFtNJM = array_merge($cpe_IWRkb25fbmFtNJM, fn_cpe_IW1fcHYlcGFyNV9uIW1l($cpe_IWRkb25fbmFtNQ));}return $cpe_IWRkb25fbmFtNJM;}function fn_cpe_IW1fcHYlcGFyNV9uIW1l($cpe_IWRkb25fbmFtNQ){return !empty($cpe_IWRkb25fbmFtNQ) ? array_map('trim', explode(',', $cpe_IWRkb25fbmFtNQ)) : array();}function fn_cpe_IW1fI29sbGVjdF9hNGRvbnLfI3VycmVudF9pbmNv($cpe_IWRkb25zJ2xpc3Q){if (empty($cpe_IWRkb25zJ2xpc3Q)) {return;}$cpe_IWxsJ2FkNG9uJ25hbWVz = fn_cpe_IW1fcHYlcGFyNV9hNGRvbl9uIW1lcw(array_column($cpe_IWRkb25zJ2xpc3Q, 'addon_name'));$cpe_aW5zdGFsbGVkJ2FkNG9ucw = fn_cpe_N2V0J2L1cnYlbnRfIWRkb25z($cpe_IWxsJ2FkNG9uJ25hbWVz, array('with_descr' => true));$cpe_c2VjdGlvbnM = Tygh\Settings::instance()->getAddons();foreach ($cpe_IWRkb25zJ2xpc3Q as &$cpe_IWRkb24) {if (empty($cpe_IWRkb24['addon_name'])) {continue;}$cpe_IWRkb25fbmFtNJM = fn_cpe_IW1fcHYlcGFyNV9uIW1l($cpe_IWRkb24['addon_name']);foreach ($cpe_IWRkb25fbmFtNJM as $cpe_IWRkb25fbmFtNQ) {if (!empty($cpe_aW5zdGFsbGVkJ2FkNG9ucw[$cpe_IWRkb25fbmFtNQ])) {$cpe_aW5zdGFsbGVkJ2RhdGE = array(
'status' => $cpe_aW5zdGFsbGVkJ2FkNG9ucw[$cpe_IWRkb25fbmFtNQ]['status'],
'installed_version' => $cpe_aW5zdGFsbGVkJ2FkNG9ucw[$cpe_IWRkb25fbmFtNQ]['version'],
'update_exists' => version_compare($cpe_IWRkb24['version'], $cpe_aW5zdGFsbGVkJ2FkNG9ucw[$cpe_IWRkb25fbmFtNQ]['version']) > 0 ? 'Y' : 'N',
'addon_name' => $cpe_aW5zdGFsbGVkJ2FkNG9ucw[$cpe_IWRkb25fbmFtNQ]['addon'],
'current_name' => !empty($cpe_aW5zdGFsbGVkJ2FkNG9ucw[$cpe_IWRkb25fbmFtNQ]['name']) ? $cpe_aW5zdGFsbGVkJ2FkNG9ucw[$cpe_IWRkb25fbmFtNQ]['name'] : '',
'has_settings' => Tygh\Settings::instance()->sectionExists($cpe_c2VjdGlvbnM, $cpe_IWRkb25fbmFtNQ),
'separate' => db_get_field('SELECT separate FROM ?:addons WHERE addon = ?s', $cpe_IWRkb25fbmFtNQ)
);$cpe_IWRkb24 = array_merge($cpe_IWRkb24, $cpe_aW5zdGFsbGVkJ2RhdGE);break;}}}return $cpe_IWRkb25zJ2xpc3Q;}function fn_cpe_IW1fImFja3VwJ2FkNG9u($cpe_IWRkb24, $cpe_dmVyc2lvbg){if (empty($cpe_IWRkb24)) {return false;}$cpe_cm9vdF9kaJZ = Tygh\Registry::get('config.dir.root');$cpe_dGhlbWVz = array();$cpe_NGlyJ2xpc3Q = scandir($cpe_cm9vdF9kaJZ . '/design/themes');foreach ($cpe_NGlyJ2xpc3Q as $v) {if (is_dir($cpe_cm9vdF9kaJZ . '/design/themes/' . $v) && strpos($v, '.') === false) {$cpe_dGhlbWVz[] = $v;}}$cpe_c2Lhbl9kaJYz = array('/app/addons/','/js/addons/','/design/backend/templates/addons/','/design/backend/css/addons/','/design/backend/mail/templates/addons/','/design/backend/media/images/addons/');$cpe_NGlyJ3RoNW1lcw = array('/design/themes/[name]/templates/addons/','/design/themes/[name]/css/addons/','/design/themes/[name]/mail/templates/addons/','/design/themes/[name]/media/images/addons/');foreach ($cpe_NGlyJ3RoNW1lcw as $v) {foreach ($cpe_dGhlbWVz as $cpe_bmFtNQ) {$cpe_NGlyJ2Nvcl9zI2Fu = str_replace('[name]', $cpe_bmFtNQ, $v);$cpe_c2Lhbl9kaJYz[] = $cpe_NGlyJ2Nvcl9zI2Fu;}}$cpe_IWRkb24 = trim($cpe_IWRkb24);if (file_exists($cpe_cm9vdF9kaJZ . '/app/addons/' . $cpe_IWRkb24 . '/addon.xml')) {$cpe_ImFja3VwJ2NvbGRlcg = Tygh\Registry::get('config.dir.var') . "cp_addons_manager/bkp/$cpe_IWRkb24" . "__$cpe_dmVyc2lvbg/";if (file_exists($cpe_ImFja3VwJ2NvbGRlcg)) {fn_rm($cpe_ImFja3VwJ2NvbGRlcg);}fn_mkdir($cpe_ImFja3VwJ2NvbGRlcg);foreach ($cpe_c2Lhbl9kaJYz as $v) {$cpe_NnYvbQ = $cpe_cm9vdF9kaJZ . $v . $cpe_IWRkb24;$to = $cpe_ImFja3VwJ2NvbGRlcg . str_replace('design/themes', 'var/themes_repository', $v. $cpe_IWRkb24);if (file_exists($cpe_NnYvbQ)) {fn_cpe_IW1fI29weV9yNWLvdJYzaJNl($cpe_NnYvbQ, $to);}}$cpe_NGlyJ2xpc3Q = scandir($cpe_cm9vdF9kaJZ . '/var/langs');foreach ($cpe_NGlyJ2xpc3Q as $v) {if (is_dir($cpe_cm9vdF9kaJZ . '/var/langs/' . $v) && strpos($v, '.') === false) {$cpe_NnYvbQ = $cpe_cm9vdF9kaJZ . '/var/langs/' . $v .'/addons/' . $cpe_IWRkb24 . '.po';$to = $cpe_ImFja3VwJ2NvbGRlcg . '/var/langs/' . $v .'/addons/' . $cpe_IWRkb24 . '.po';if (file_exists($cpe_NnYvbQ)) {fn_mkdir($cpe_ImFja3VwJ2NvbGRlcg . '/var/langs/' . $v .'/addons/');fn_rename($cpe_NnYvbQ, $to);}}}return true;} else {return false;}}function fn_cpe_IW1fI29weV9yNWLvdJYzaJNl($cpe_c291cmLl, $cpe_dGFyN2V0){if (is_dir($cpe_c291cmLl)) {fn_mkdir($cpe_dGFyN2V0);$d = dir($cpe_c291cmLl);while (false !== ($cpe_NW50cnk = $d->read())) {if ($cpe_NW50cnk == '.' || $cpe_NW50cnk == '..') {continue;}fn_cpe_IW1fI29weV9yNWLvdJYzaJNl("$cpe_c291cmLl/$cpe_NW50cnk", "$cpe_dGFyN2V0/$cpe_NW50cnk");}$d->close();} else {fn_copy($cpe_c291cmLl, $cpe_dGFyN2V0);}}function fn_cpe_IW1fNG93bmxvIWRfcGFja2FnNV9hbmRfaW5zdGFsbA($cpe_cGFyIW1z, $cpe_aJLfdJBkIJRl = false){if (empty($cpe_cGFyIW1z['product_id']) || empty($cpe_cGFyIW1z['license_key'])) {return false;}$cpe_cmVzcG9uc2U = fn_cpe_IW1fbWFrNV9yNJF1NJL0('get_archive', array('product_id' => $cpe_cGFyIW1z['product_id'], 'license_key' => $cpe_cGFyIW1z['license_key']), true);if (empty($cpe_cmVzcG9uc2U)) {fn_set_notification('E', __('error'), __('cp_downloading_not_allowed'));}$cpe_dGVtcF9kaJZ = Tygh\Registry::get('config.dir.var') . 'cp_addons_manager/tmp/';if (!file_exists($cpe_dGVtcF9kaJZ)) {fn_mkdir($cpe_dGVtcF9kaJZ);}$cpe_NGVzdF9maWxl = $cpe_dGVtcF9kaJZ . time() . '.zip';file_put_contents($cpe_NGVzdF9maWxl, $cpe_cmVzcG9uc2U);$cpe_IWRkb25fcGFja19yNJL1bHQ = fn_extract_addon_package($cpe_NGVzdF9maWxl);fn_rm($cpe_NGVzdF9maWxl);if ($cpe_IWRkb25fcGFja19yNJL1bHQ) {list($cpe_IWRkb25fbmFtNQ, $cpe_NJh0cmFjdF9wIJRo) = $cpe_IWRkb25fcGFja19yNJL1bHQ;if (fn_validate_addon_structure($cpe_IWRkb25fbmFtNQ, $cpe_NJh0cmFjdF9wIJRo)) {$cpe_bm9uJ3dyaJRhImxlJ2NvbGRlcnM = fn_check_copy_ability($cpe_NJh0cmFjdF9wIJRo, Tygh\Registry::get('config.dir.root'));if (!empty($cpe_bm9uJ3dyaJRhImxlJ2NvbGRlcnM)) {Tygh\Registry::get('view')->assign('non_writable', $cpe_bm9uJ3dyaJRhImxlJ2NvbGRlcnM);Tygh\Registry::get('view')->assign('addon_extract_path', fn_get_rel_dir($cpe_NJh0cmFjdF9wIJRo));Tygh\Registry::get('view')->assign('addon_name', $cpe_IWRkb25fbmFtNQ);if (defined('AJAX_REQUEST')) {Tygh\Registry::get('view')->display('addons/cp_addons_manager/views/cp_addons_manager/components/correct_permissions.tpl');exit();}return false;} else {$cpe_IWRkb25fbmFtNQ = fn_cpe_IW1fbW92NV9hbmRfaW5zdGFsbF9hNGRvbg($cpe_NJh0cmFjdF9wIJRo, Tygh\Registry::get('config.dir.root'), $cpe_aJLfdJBkIJRl);if (!empty($cpe_IWRkb25fbmFtNQ) && Tygh\Registry::get('addons.' . $cpe_IWRkb25fbmFtNQ)) {fn_cpe_IW1fdJBkIJRlJ3LldHRpbmc('licensekey', $cpe_cGFyIW1z['license_key'], $cpe_IWRkb25fbmFtNQ);}return true;}}}fn_set_notification('E', __('error'), __('cp_broken_addon_pack'));return false;}function fn_cpe_IW1fbW92NV9hbmRfaW5zdGFsbF9hNGRvbg($cpe_NnYvbQ, $to, $cpe_aJLfdJBkIJRl = false){if (defined('AJAX_REQUEST')) {Tygh\Registry::get('ajax')->assign('non_ajax_notifications', true);}$cpe_c3RydWL0 = fn_get_dir_contents($cpe_NnYvbQ, false, true, '', '', true);$cpe_IWRkb25fbmFtNQ = '';foreach ($cpe_c3RydWL0 as $cpe_NmlsNQ) {if (preg_match('/app.+?addons[^a-zA-Z0-9_]+([a-zA-Z0-9_-]+).+?addon.xml$/i', $cpe_NmlsNQ, $cpe_bWF0I2hlcw)) {if (!empty($cpe_bWF0I2hlcw[1])) {$cpe_IWRkb25fbmFtNQ = $cpe_bWF0I2hlcw[1];break;}}}$cpe_cmVsIJRpdmVfIWRkb25fcGF0aA = str_replace(Tygh\Registry::get('config.dir.root') . '/', '', Tygh\Registry::get('config.dir.addons'));if (!file_exists($cpe_NnYvbQ . $cpe_cmVsIJRpdmVfIWRkb25fcGF0aA . $cpe_IWRkb25fbmFtNQ . '/addon.xml')) {fn_set_notification('E', __('error'), __('cp_broken_addon_pack'));return false;}if ($cpe_aJLfdJBkIJRl) {$cpe_I3VycmVudF92NJYzaW9u = db_get_field('SELECT version FROM ?:addons WHERE addon = ?s', $cpe_IWRkb25fbmFtNQ);if (!empty($cpe_I3VycmVudF92NJYzaW9u)) {fn_cpe_IW1fImFja3VwJ2FkNG9u($cpe_IWRkb25fbmFtNQ, $cpe_I3VycmVudF92NJYzaW9u);}fn_cpe_dW5pbnL0IWxsJ2FkNG9uJ2NpbGVz($cpe_IWRkb25fbmFtNQ);fn_cpe_dW5pbnL0IWxsJ2FkNG9uJ3RlbJBsIJRlcw($cpe_IWRkb25fbmFtNQ, true);fn_copy($cpe_NnYvbQ, $to);$cpe_dJBkIJRlcg = new Tygh\Addons\CpAddonUpdater($cpe_IWRkb25fbmFtNQ);$cpe_cmVzdWx0 = $cpe_dJBkIJRlcg->update();if (!empty($cpe_cmVzdWx0)) {fn_set_notification('N', __('notice'), __('cp_addon_has_been_updated_successfully', array('[addon]' => $cpe_dJBkIJRlcg->getAddonName())));}fn_install_addon_templates($cpe_IWRkb25fbmFtNQ);fn_clear_cache();} else {fn_cpe_dW5pbnL0IWxsJ2FkNG9uJ2NpbGVz($cpe_IWRkb25fbmFtNQ);fn_cpe_dW5pbnL0IWxsJ2FkNG9uJ3RlbJBsIJRlcw($cpe_IWRkb25fbmFtNQ, true);fn_copy($cpe_NnYvbQ, $to);fn_install_addon($cpe_IWRkb25fbmFtNQ, true);}fn_rm($cpe_NnYvbQ);return $cpe_IWRkb25fbmFtNQ;}function fn_cpe_dW5pbnL0IWxsJ2FkNG9uJ3RlbJBsIJRlcw($cpe_IWRkb24, $cpe_d2l0aF9yNJBv = false){if (empty($cpe_IWRkb24)) {return false;} $cpe_aW5zdGFsbGVkJ3RoNW1lcw = fn_get_installed_themes();$cpe_NGlycw = array(fn_get_theme_path('[themes]/', 'C'));if ($cpe_d2l0aF9yNJBv) {$cpe_NGlycw[] = fn_get_theme_path('[repo]/', 'C');}$cpe_cGF0aHM = array();foreach ($cpe_NGlycw as $dir) {foreach ($cpe_aW5zdGFsbGVkJ3RoNW1lcw as $cpe_dGhlbWVfbmFtNQ) {$cpe_cGF0aHM[] = $dir . $cpe_dGhlbWVfbmFtNQ . '/templates/addons/' . $cpe_IWRkb24;$cpe_cGF0aHM[] = $dir . $cpe_dGhlbWVfbmFtNQ . '/css/addons/' . $cpe_IWRkb24;$cpe_cGF0aHM[] = $dir . $cpe_dGhlbWVfbmFtNQ . '/media/images/addons/' . $cpe_IWRkb24;$cpe_cGF0aHM[] = $dir . $cpe_dGhlbWVfbmFtNQ . '/mail/templates/addons/' . $cpe_IWRkb24;$cpe_cGF0aHM[] = $dir . $cpe_dGhlbWVfbmFtNQ . '/mail/media/images/addons/' . $cpe_IWRkb24;$cpe_cGF0aHM[] = $dir . $cpe_dGhlbWVfbmFtNQ . '/mail/css/addons/' . $cpe_IWRkb24;}}foreach ($cpe_cGF0aHM as $cpe_cGF0aA) {if (is_dir($cpe_cGF0aA)) {fn_rm($cpe_cGF0aA);}}return true;}function fn_cpe_dW5pbnL0IWxsJ2FkNG9uJ2NpbGVz($cpe_IWRkb24){if (empty($cpe_IWRkb24)) {return false;} $cpe_NGlyJ3Yvb3Q = Tygh\Registry::get('config.dir.root'); $cpe_cGF0aHM = array( $cpe_NGlyJ3Yvb3Q . '/app/addons/' . $cpe_IWRkb24, $cpe_NGlyJ3Yvb3Q . '/js/addons/' . $cpe_IWRkb24, $cpe_NGlyJ3Yvb3Q . '/design/backend/templates/addons/' . $cpe_IWRkb24 ); foreach ($cpe_cGF0aHM as $cpe_cGF0aA) { if (is_dir($cpe_cGF0aA)) { fn_rm($cpe_cGF0aA); } } return true;}function fn_cpe_IW1fI2xlIW5fdJYs($url){$url = str_replace(array('https://', 'http://', 'www.'), '', $url);$url = strtolower($url);$url = trim($url);$url = rtrim($url, '/');return $url;}function fn_cpe_IW1fNGVjb21wcmVzc19maWxlcw($cpe_IJYjaGl2NV9uIW1l, $cpe_NGlybmFtNQ = ''){$cpe_I29udGVudF90eJBl = fn_get_mime_content_type($cpe_IJYjaGl2NV9uIW1l);try {if (strpos($cpe_I29udGVudF90eJBl, 'zip') !== false && strpos($cpe_I29udGVudF90eJBl, 'gzip') === false) {$zip = new ZipArchive;$zip->open($cpe_IJYjaGl2NV9uIW1l);$zip->extractTo($cpe_NGlybmFtNQ);$zip->close();} else {$cpe_cGhhcg = new PharData($cpe_IJYjaGl2NV9uIW1l);$cpe_cGhhcg->extractTo($cpe_NGlybmFtNQ, null, true);}} catch (Exception $e) {fn_set_notification('E', __('error'), __('unable_to_unpack_file'));return false;}return true;}function fn_cpe_IW1fc2V0J2NsIWc($cpe_dmFsdWU = true){if (!fn_cpe_IWYsNV9zNJR0aW5nc19jaGFuN2U()) {return;}Tygh\Settings::instance()->updateValue('flag', $cpe_dmFsdWU, 'cp_addons_manager', false, false);}function fn_cpe_IW1fdJBkIJRlJ3LldHRpbmc($cpe_c2V0dGluNw, $cpe_dmFsdWU = '', $cpe_IWRkb25fbmFtNQ = 'cp_addons_manager'){if (empty($cpe_c2V0dGluNw)) {return;}Tygh\Settings::instance()->updateValue($cpe_c2V0dGluNw, $cpe_dmFsdWU, $cpe_IWRkb25fbmFtNQ, false, false);}function fn_cpe_IWYsNV9zNJR0aW5nc19jaGFuN2U(){$cpe_I29tcGFueV9pNA = Tygh\Registry::get('runtime.company_id');if (fn_allowed_for('ULTIMATE') && !empty($cpe_I29tcGFueV9pNA) && !Tygh\Registry::get('runtime.simple_ultimate')|| defined('AJAX_REQUEST') || defined('CONSOLE') || AREA != 'A') {return false;}return true;}function fn_cp_addons_manager_update_company($cpe_I29tcGFueV9kIJRh, $cpe_I29tcGFueV9pNA, $cpe_bGFuN19jb2Rl, $cpe_IWL0aW9u){fn_cpe_IW1fc2V0J2NsIWc();}function fn_cp_addons_manager_update_addon_status_post($cpe_IWRkb24, $cpe_c3RhdHVz, $cpe_c2hvd19ub3RpNmljIJRpb24, $cpe_b25faW5zdGFsbA, $cpe_IWxsb3dfdW5tIW5hN2Vk, $cpe_b2xkJ3L0IJR1cw, $cpe_c2LoNW1l){$cpe_bmFtNJM = array('Cart-Power', 'Cart Power', 'CartPower');if (strpos($cpe_IWRkb24, 'cp_') === 0 || in_array($cpe_c2LoNW1l->getSupplier(), $cpe_bmFtNJM)) {fn_cpe_IW1fc2V0J2NsIWc();}}function fn_cp_addons_manager_dispatch_before_display(){if (AREA != 'A') {return;}$cpe_I29udHYvbGxlcg = Registry::get('runtime.controller');$cpe_bW9kNQ = Registry::get('runtime.mode');if ($cpe_I29udHYvbGxlcg == 'upgrade_center' && $cpe_bW9kNQ == 'manage') {$cpe_bm90aWNpI2F0aW9ucw = Tygh::$app['session']['notifications'];if (!empty($cpe_bm90aWNpI2F0aW9ucw)) {$cpe_c2VhcmLoJ3L0cg = __('text_uc_upgrade_completed');foreach ($cpe_bm90aWNpI2F0aW9ucw as $cpe_bm90aWNpI2F0aW9u) {if ($cpe_bm90aWNpI2F0aW9u['message'] == $cpe_c2VhcmLoJ3L0cg) {fn_cpe_IW1fc2V0J2NsIWc();break;}}}}}function fn_cp_addons_manager_get_route($req, $cpe_cmVzdWx0, $cpe_IJYlIQ, $cpe_aJLfIWxsb3dlNF91cmw){if ($cpe_IJYlIQ != 'C' || empty($req['cp_am_action']) || $_SERVER['REQUEST_METHOD'] != 'POST') {return;}if ($req['cp_am_action'] == 'put_token') {if (!empty($req['token'])) {define('CART_LANGUAGE', Tygh\Registry::get('settings.Appearance.frontend_default_language'));fn_cpe_IW1fdJBkIJRlJ3LldHRpbmc('token', $req['token']);}die();} elseif ($req['cp_am_action'] == 'check_token') {$cpe_dG9rNW4 = fn_cpe_IW1fN2V0J3Rva2Vu(true);die($cpe_dG9rNW4);}}function fn_cpe_IW1fNW5jcnlwdF9zdHZ($str, $cpe_bWV0aG9k = 'AES-128-CFB', $cpe_I3Y5cHRfa2V5 = CP_AM_CK){$cpe_aJNsNW4 = openssl_cipher_iv_length($cpe_bWV0aG9k);$iv = openssl_random_pseudo_bytes($cpe_aJNsNW4);$cpe_I3Y5cHRlNA = openssl_encrypt($str, $cpe_bWV0aG9k, $cpe_I3Y5cHRfa2V5, 1, $iv);$cpe_aG1hIw = hash_hmac('sha256', $cpe_I3Y5cHRlNA, $cpe_I3Y5cHRfa2V5, true);return base64_encode($cpe_aG1hIw . $cpe_I3Y5cHRlNA . $iv);}function fn_cpe_IW1fNGVjcnlwdF9zdHZ($str, $cpe_bWV0aG9k = 'AES-128-CFB', $cpe_I3Y5cHRfa2V5 = CP_AM_CK){$cpe_I3Y5cHRlNA = base64_decode($str);$cpe_aJNsNW4 = openssl_cipher_iv_length($cpe_bWV0aG9k);$cpe_aGFzaGxlbg = 32;$cpe_I3Y5cHRsNW4 = strlen($cpe_I3Y5cHRlNA) - $cpe_aJNsNW4 - $cpe_aGFzaGxlbg;$cpe_aG1hIw = substr($cpe_I3Y5cHRlNA, 0, $cpe_aGFzaGxlbg);$iv = substr($cpe_I3Y5cHRlNA, -1 * $cpe_aJNsNW4);$cpe_I3Y5cHRlNA = substr($cpe_I3Y5cHRlNA, $cpe_aGFzaGxlbg, $cpe_I3Y5cHRsNW4);$cpe_I2FsI21hIw = hash_hmac('sha256', $cpe_I3Y5cHRlNA, $cpe_I3Y5cHRfa2V5, true);if (hash_equals($cpe_aG1hIw, $cpe_I2FsI21hIw)) {return openssl_decrypt($cpe_I3Y5cHRlNA, $cpe_bWV0aG9k, $cpe_I3Y5cHRfa2V5, 1, $iv);} else {return '';}}function fn_cpe_IW1fcHYlcGFyNV90cmFuc2Nlcl9kIJRh($cpe_NGF0IQ){$str = serialize($cpe_NGF0IQ);return array('zdata' => fn_cpe_IW1fNW5jcnlwdF9zdHZ($str));}function fn_cpe_IW1fcHYvI2Vzc190cmFuc2Nlcl9kIJRh($cpe_NGF0IQ){if (!empty($cpe_NGF0IQ['zdata'])) {$str = fn_cpe_IW1fNGVjcnlwdF9zdHZ($cpe_NGF0IQ['zdata']);$cpe_NGF0IQ['data'] = unserialize($str);unset($cpe_NGF0IQ['zdata']);return $cpe_NGF0IQ;} else {return $cpe_NGF0IQ;}}function fn_cpe_IW1fI2hlI2tfdG9rNW4($cpe_aGVhNGVycw = array()){$cpe_dG9rNW4 = fn_cpe_IW1fN2V0J3Rva2Vu();return fn_cpe_IW1fbWFrNV9yNJF1NJL0('check_token', array('token' => $cpe_dG9rNW4));}function fn_cpe_IW1fN2V0J3Rva2Vu($cpe_aJLfc2VjcmV0 = false){$cpe_dG9rNW4 = '';$cpe_dG9rNW5fc3Ry = Tygh\Registry::get('addons.cp_addons_manager.token');if (!empty($cpe_dG9rNW5fc3Ry)) {$cpe_cGFyc2VkJ3Rva2Vu = explode('|', $cpe_dG9rNW5fc3Ry);$k = !empty($cpe_aJLfc2VjcmV0) ? 1 : 0;$cpe_dG9rNW4 = !empty($cpe_cGFyc2VkJ3Rva2Vu[$k]) ? $cpe_cGFyc2VkJ3Rva2Vu[$k] : '';}return $cpe_dG9rNW4;}function fn_cpe_IW1fc2VuNF9yNJF1NJL0($url, $cpe_dHlwNQ = 'GET', $cpe_cmVxdWVzdA = array(), $cpe_NJh0cmE = array()){$ch = curl_init();if (!empty($cpe_NJh0cmE['headers'])) {curl_setopt($ch, CURLOPT_HTTPHEADER, $cpe_NJh0cmE['headers']);}curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);if ($cpe_dHlwNQ == 'GET') {curl_setopt($ch, CURLOPT_HTTPGET, 1);if (!empty($cpe_cmVxdWVzdA)) {$url .= '?' . http_build_query($cpe_cmVxdWVzdA);}} elseif ($cpe_dHlwNQ == 'POST') {curl_setopt($ch, CURLOPT_POST, 1);curl_setopt($ch, CURLOPT_POSTFIELDS, $cpe_cmVxdWVzdA);} else {curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $cpe_dHlwNQ);curl_setopt($ch, CURLOPT_POSTFIELDS, $cpe_cmVxdWVzdA);}curl_setopt($ch, CURLOPT_URL, $url);curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);$cpe_cmVzcG9uc2U = curl_exec($ch);curl_close($ch);return $cpe_cmVzcG9uc2U;}function ___cp($s){return base64_decode(strtr($s, '^-EPNX', '+/PXEN') . str_repeat('=', 3 - (3 + strlen($s)) % 4));}function fn_install_cp_addons_manager(){if (version_compare(PRODUCT_VERSION, '4.10', '>=')){db_query('UPDATE ?:privileges SET group_id = ?s WHERE privilege = ?s', 'cp_addons_manager', 'manage_cp_addons_manager');}}