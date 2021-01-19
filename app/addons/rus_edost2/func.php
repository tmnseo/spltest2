<?php
if (!defined('AREA')) die('Access denied');

use Tygh\Template\Document\Variables\PickpupPointVariable;

include_once 'lib/edost_cms.php';

// удаление модуля
function fn_rus_edost2_uninstall() {
	edost_config('uninstall');
}

// проверка оплаты на наложенный платеж eDost
function fn_rus_edost2_check_cod($payment) {
	return (!empty($payment['template']) && strpos($payment['template'], 'edost2_cod.tpl') ? true : false);
}

// обработка параметров корзины на странице оформления заказа и при редактировании заказа в админке (используется в контроллерах)
function fn_rus_edost2_set_cart_param(&$cart, $mode, $admin = false) {

	$r = false;

	if ($admin) $cart['edost_admin'] = true;

	if ($admin && $_SERVER['REQUEST_METHOD'] == 'GET' && $mode == 'update' && empty($cart['edost_order_ajax']) && !empty($cart['order_id'])) {
		$s = db_get_field("SELECT data FROM ?:order_data WHERE order_id = ?i AND type = ?s", $cart['order_id'], 'L'); // G - весь заказ, L - выбранная доставка, R - валюта
		if (!empty($s)) $cart['chosen_shippings_old'] = unserialize($s);
	}

	if (!empty($_REQUEST['shipping_ids'])) $cart['edost_shipping_original'] = $_REQUEST['shipping_ids'];

	if (!empty($_REQUEST['DELIVERY_ID']) && strpos($_REQUEST['DELIVERY_ID'], '|set') !== false) $cart['edost_shipping'] = $_REQUEST['DELIVERY_ID'];
	else if (!empty($_REQUEST['edost_office']) && strpos($_REQUEST['edost_office'], '|set') !== false) $cart['edost_shipping'] = $_REQUEST['edost_office'];
	else if (!empty($_REQUEST['shipping_ids'])) foreach ($_REQUEST['shipping_ids'] as $v) if (strpos($v, 'edost:') !== false) { $cart['edost_shipping'] = $v; break; }

	if (!empty($_REQUEST['edost_office_data_parsed'])) $cart['edost_office_data_parsed'] = true;

	if (!empty($_REQUEST['is_ajax'])) $cart['edost_order_ajax'] = true;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') $r = true;

	return $r;

}

// установка параметров модуля eDost
function fn_rus_edost2_set_config(&$config, $admin = false) {

	$ar = array('sort_ascending', 'template', 'template_ico');
	foreach ($ar as $v) if (!isset($config[$v.'_original'])) $config[$v.'_original'] = $config[$v];

	if ($admin) {
		if ($config['template'] != 'off') $config['template'] = 'Y';
		$config['autoselect'] = 'Y';
	}

	if ($config['template'] == 'N') $config['sort_ascending'] = 'N';

	$config['map'] = 'Y';
	$config['template_ico'] = 'C';
	$config['SHOW_COD_NOTE'] = true;

	if ($config['template'] == 'Y') {
		$config['COMPACT'] = 'Y';
		$config['PRIORITY'] = 'B';
		if (isset($config['sort_ascending_original'])) $config['sort_ascending'] = $config['sort_ascending_original'];
	}

	return $config;

}


// событие: сохранение оригинальных товаров до модификации у них веса модулем "Служба доставки — СДЭК (rus_sdek)"
function fn_rus_edost2_calculate_cart_items(&$cart, $cart_products, $auth, $apply_cart_promotions) {
	if (!defined('EDOST_CART') || EDOST_CART != 'N') {
		$cart['edost_items'] = array();
		foreach ($cart_products as $k => $v) $cart['edost_items'][$k] = $v;
	}
}

// событие: установка статуса заказа с наложенным платежом при размещении заказа
function fn_rus_edost2_place_order($order_id, $action, &$order_status, $cart, $auth) {
	if (empty(\edost_class::$result['order']['config']) || empty($cart['payment_method_data'])) return;
	$config = \edost_class::$result['order']['config'];
	if (!empty($config['cod_status']) && fn_rus_edost2_check_cod($cart['payment_method_data'])) $order_status = $config['cod_status'];
}

// событие: заплатка, чтобы обойти требование cs-cart на обязательное присутвие у автоматической доставки 'service_params'
function fn_rus_edost2_shippings_get_shippings_list_post($group, $lang, $area, &$shippings_info) {
	if (!empty($shippings_info)) foreach ($shippings_info as $k => $v) if ($v['module'] == 'edost2') $shippings_info[$k]['service_params'] = array(0 => 0);
}

// событие: обработка корзины
function fn_rus_edost2_calculate_cart_taxes_pre(&$cart, $cart_products, &$product_groups) {
//	\edost_class::draw_data('cart', $cart);

	$admin = false;
	if (!empty($cart['edost_admin'])) {
		$admin = true;
		unset($cart['edost_admin']);
	}

	if (empty(\edost_class::$result['order']['config'])) return;
	$config = fn_rus_edost2_set_config(\edost_class::$result['order']['config'], $admin);
	if ($config['template'] == 'off') return;

	$compact = ($config['template'] == 'Y' ? true : false);
	$sign = \edost_class::GetMessage('EDOST_DELIVERY_SIGN');

	// первая загрузка без ajax
	$start = true;
	if (!empty($cart['edost_order_ajax'])) {
		$start = false;
		unset($cart['edost_order_ajax']);
	}

    // активный тариф
	$active = false;
	if (!$start && !empty($cart['chosen_shipping'])) foreach ($cart['chosen_shipping'] as $v) $active = array('id' => $v);
	if (!empty($cart['edost_shipping_original'])) foreach ($cart['edost_shipping_original'] as $v) $active = array('id' => $v);
	if (!empty($cart['chosen_shippings_old'])) {
		foreach ($cart['chosen_shippings_old'] as $v) if (!empty($v['edost']['html_value'])) $active = \edost_class::ParseActive($v['edost']['html_value']);
		unset($cart['chosen_shippings_old']);
	}
	else if (!empty($cart['edost_shipping'])) {
		$s = explode('|set', $cart['edost_shipping']);
		$a = \edost_class::ParseActive($s[0]);
		if (isset($s[1]) || !empty($active['id']) && $active['id'] == $a['id']) $active = $a;
	}
	if (!empty($cart['edost_shipping'])) unset($cart['edost_shipping']);
//	\edost_class::draw_data('active', $active);

	// доставки заказа
	$cscart_data = array();
	foreach ($product_groups as $group_key => $group) foreach ($group['shippings'] as $v) $cscart_data[] = array(
		'id' => $v['shipping_id'],
		'shop_id' => $v['group_key'],
		'profile' => $v['service_code'],
		'automatic' => ($v['module'] == 'edost2' ? 'edost' : $v['module']),
		'title' => $v['shipping'],
		'description' => $v['description'],
		'shop_price' => $v['rate'],
//		'img_path' => ($config['template_ico'] != 'C' && !empty($v['image']['icon']['relative_path']) ? 'images/'.$v['image']['icon']['relative_path'] : ''),
	);
//	\edost_class::draw_data('cscart_data', $cscart_data);

	$format = \edost_class::FormatTariff($cscart_data, $active);
	if (!defined('EDOST_OFFICE_DATA') || EDOST_OFFICE_DATA != 'Y') unset($format['office']);
//	\edost_class::draw_data('format', $format);

	$cod = false;
	if (!empty($cart['payment_id'])) {
		$payment_info = fn_get_payment_method_data($cart['payment_id']);
		$cod = fn_rus_edost2_check_cod($payment_info);
	}
	$cart['edost_cod'] = false;
	$cod_data = false;

	$edost_shipping = false;
	foreach ($product_groups as $group_key => $group) {
		$shippings = $group['shippings'];
		$shippings_new = ($config['template'] == 'N' ? $shippings : array());

		if (!empty($format['data'])) foreach ($format['data'] as $f_key => $f) if (!empty($f['tariff'])) foreach ($f['tariff'] as $k => $v) if (isset($v['id']) && (!$compact || $compact && !empty($v['compact'])) && !isset($v['compact_cod_copy'])) {
			$id = (!empty($v['id']) ? $v['id'] : $v['html_value'].'|set');
            $s = (!empty($shippings[$id]) ? $shippings[$id] : false);

			if ($config['template'] == 'N' && ($s === false || $s['module'] != 'edost2')) continue;

			if ($s === false) $s = array(
				'shipping_id' => (!empty($id) ? $id : $v['html_value'].'|set'),
				'shipping' => (!empty($v['title']) ? $v['title'] : ''),
				'delivery_time' => (!empty($v['day']) ? $v['day'] : ''),
				'description' => (!empty($v['description']) ? $v['description'] : ''),
				'rate_calculation' => 'R',
				'service_params' => array('0' => 0),
				'destination' => 'I',
				'min_weight' => 0,
				'max_weight' => 0,
				'service_id' => 0,
				'free_shipping' => false,
				'module' => 'edost2',
				'service_code' => $v['profile'],
				'rate_info' => array(),
				'group_key' => $group_key,
				'rate' => $v['price'],
				'service_delivery_time' => (!empty($v['day']) ? $v['day'] : ''),
			);

			if ($config['template_ico'] == 'C') {
				if ($config['template_ico_original'] != 'off' && isset($v['company_ico'])) {
					$img = EDOST_ICO_PATH.'/company/'.$v['company_ico'].'.gif';
					$s['image'] = array('icon' => array('image_path' => $img, 'alt' => '', 'image_x' => 45, 'image_y' => 45, 'http_image_path' => $img, 'https_image_path' => $img, 'absolute_path' => $img, 'relative_path' => $img, 'is_high_res' => 0));
				}
			}
			else if (!empty($v['image'])) {
				$s['image'] = $v['image'];
				unset($v['image']);
			}
			if ($s['module'] == 'edost2' && !empty($v['office_map'])) $s['is_address_required'] = 'N';
			if ($s['module'] == 'edost2' || $config['template'] != 'N') $s['edost'] = $v;

			$title_original = $s['shipping'];
			$price_original = $s['rate'];
			if ($v['automatic'] == 'edost') {
				if ($compact && !empty($v['head'])) $s['shipping'] = $v['head'].(!$admin && empty($f['insurance']) && !empty($v['insurance']) ? ' ('.$sign['insurance'].')' : ''); //.' - '.($cart['edost_order_open'] ? $cart['edost_order_open'] : 'FALSE');
				if (isset($v['day'])) $s['delivery_time'] = $v['day'];
				if (isset($v['pricetotal'])) $s['rate'] = $v['pricetotal'];
			}

			if (!empty($v['checked'])) {
				if ($v['automatic'] == 'edost') $edost_shipping = true;

				$cart['chosen_shipping'][$group_key] = $cart['edost_shipping_original'][$group_key] = $id;

				$cart['shipping_cost'] = $v['price'];
				$cart['display_shipping_cost'] = $v['price'];
				$cart['shipping'] = array($id => $s);

				$p = false;
				if ((!$compact || $admin) && isset($v['to_office'])) $p = $v['price'];
				if (isset($v['pricecash']) && $v['pricecash'] >= 0) {
					$note = $warning = array();

					$cod_data = array('group' => $group_key, 'shipping' => $id);
					if (!empty($v['codplus_formatted'])) {
						$cod_data['codplus'] = $v['codplus'];
						$cod_data['codplus_formatted'] = $v['codplus_formatted'];
					}
					if ($cod !== false) {
						if (!empty($v['cod_note'])) $warning[] = $v['cod_note'];
						if (!empty($format['active']['cod_note'])) $warning[] = $format['active']['cod_note'];
						if (!empty($v['office_options'])) {
							$o = $v['office_options'] & 6;
							if ($o == 4) $note[] = $sign['paysystem_card'];
							if ($o == 6) $warning[] = $sign['paysystem_card2'];
						}
					}
					$cart['edost_cod'] = $cod_data;

					if (!empty($warning)) $cod_data['warning'] = implode('<br>', $warning);
					if (!empty($note)) $cod_data['note'] = implode('<br>', $note);

					if ($cod !== false) {
						$p = $v['pricecash'];
						$cart['shipping_cost'] = $v['pricecash'];
						$cart['display_shipping_cost'] = $v['pricecash'];
						$cart['shipping'] = array($id => $s);
					}
				}
				$c = $s;
				$c['shipping'] = $title_original;
				$c['rate'] = ($p !== false ? $p : $price_original);
				$product_groups[$group_key]['chosen_shippings'] = array($c);
			}

			$shippings_new[$id] = $s;
		}

		if ($config['template'] == 'N' && !empty($shippings_new)) foreach ($shippings_new as $k => $v) if ($v['module'] == 'edost2' && !isset($v['edost'])) unset($shippings_new[$k]);
		$product_groups[$group_key]['shippings'] = $shippings_new;
	}

	if ($admin && $cod && empty($cod_data)) $cod_data = array('warning' => $sign['admin_no_cod']);

	// пересчет хеш, если выбрана доставка eDost - чтобы магазин не выдавал предупреждение "Стоимость доставки была изменена"
	if ($edost_shipping) Tygh::$app['session']['shipping_hash'] = fn_get_shipping_hash($cart['product_groups']);

	// данные для шаблона
	$edost = array();
	$edost['format'] = $format;
	$edost['yandex_api_key'] = Tygh\Registry::get('addons.geo_maps.yandex_api_key');
	$edost['script'] = \edost_class::GetScriptData($config, 'js/addons/rus_edost2/');
	$edost['ico_path'] = EDOST_ICO_PATH;
	$edost['template_ico'] = $config['template_ico'];
	if ($config['template_ico_original'] == 'C') $edost['template_ico_style'] = true;
	if (!empty($cod_data)) $edost['cod_data'] = $cod_data;

	$edost['map_update'] = (empty($cart['edost_office_data_parsed']) || !empty($format['map_update']) ? true : false);
	if (isset($cart['edost_office_data_parsed'])) unset($cart['edost_office_data_parsed']);

	$w = \edost_class::GetWarning();
	if ($w != '') $w .= $sign['post_zip'];
	$edost['warning'] = $w;

	$ar = array('get' => 'SOA_TEMPL_GET', 'cod_tariff' => 'SOA_TEMPL_COD_TARIFF', 'door' => 'SOA_TEMPL_TARIFF_DOOR', 'post' => 'SOA_TEMPL_TARIFF_POST', 'office_unchecked' => 'SOA_TEMPL_NO_OFFICE_WARNING');
	foreach ($ar as $k => $v) $edost['message'][$k] = \edost_class::GetMessage($v);

	// параметры адаптации
	if ($config['template'] == 'Y') {
		$template_width = 0;
		$resize = array(
			'ico_row' => array('ico_row', 'edost_resize_ico', '1', 550, '1', 450, '1'),
			'ico2' => array('class', 'edost_resize_ico', 'edost_compact_normal', 380, 'edost_compact_small2'),
			'delimiter' => array('class', 'edost_main', 'edost_delimiter_normal', 680, 'edost_delimiter_small'),
			'map' => array('id', 'edost_delivery_div', 'edost_map_normal', 500, 'edost_map_hide'),
			'delivery_window' => array('id-window', 'edost_window', 'edost_window_delivery_normal', 500, 'edost_window_delivery_small', 440, 'edost_window_delivery_small2'),
		);
		$s = array();
		foreach ($resize as $k => $v) {
			$s[] = implode(':', $v);
			$c = '';
			foreach ($v as $k2 => $v2) if ($k2 > 1)
				if (!($k2%2)) $c = $v2;
				else if (empty($template_width) || $v2 < $template_width) break;
			$c = explode(',', $c);
			if ($c[0] != '' && $k != 'ico_row') $c[0] = ' '.$c[0];
			$resize[$k] = (count($c) > 1 ? $c : $c[0]);
		}
		$edost['template_data'] = implode('|', $s);
	}

	if (!empty(Tygh::$app['view']) && method_exists(Tygh::$app['view'], 'assign')) Tygh::$app['view']->assign('edost', $edost);

//	\edost_class::draw_data('cart', $cart);

}

// событие: вывод наложки на странице оформления заказа
function fn_rus_edost2_prepare_checkout_payment_methods(&$cart, $auth, &$payment_groups) {

	if (!isset($cart['edost_cod']) || empty($cart['payment_id'])) return;

	$first = $cod = false;
	$s = (!empty($cart['edost_cod']) ? $cart['edost_cod'] : false);

	foreach ($payment_groups as $group_key => $group) foreach ($group as $k => $v)
		if (fn_rus_edost2_check_cod($v)) {
			$cod = $k;
			if ($s === false) unset($payment_groups[$group_key][$k]);
			else if (!empty($s['codplus_formatted'])) $payment_groups[$group_key][$k]['codplus_formatted'] = $s['codplus_formatted'];
		}
		else if ($first === false) $first = $v;

	if ($s === false && $cart['payment_id'] == $cod && $first !== false) $cart['payment_id'] = $v['payment_id'];

	unset($cart['edost_cod']);

}

// загрузка данных по выбранному пункту выдачи для документов и шаблона письма
function fn_rus_edost2_pickup_point_variable_init(PickpupPointVariable $instance, $order, $lang_code, &$is_selected, &$name, &$phone, &$full_address, &$open_hours_raw, &$open_hours, &$description_raw, &$description) {

	if (!empty($order['shipping'])) {
		if (is_array($order['shipping'])) $shipping = reset($order['shipping']);
		else $shipping = $order['shipping'];

		if (!empty($shipping['edost']['office'])) {
			$e = $shipping['edost'];

			$is_selected = true;
			$name = '<a target="_blank" href="'.$e['office_detailed'].'">'.$e['office_link_head'].' №'.$e['office']['code'].'</a>';
			$phone = $e['office']['tel'];

			$state = (fn_get_state_name($order['s_state'], $order['s_country'], $lang_code) ?: $order['s_state']);
			$country = fn_get_country_name($order['s_country'], $lang_code);
			if ($order['s_country'] == 'RU') {
				$country = '';
				if ($order['s_city'] == $state) $state = '';
				if ($state != '') {
					$ar = \edost_class::GetMessage('NO_REGION_CITY');
					if (array_search($order['s_city'], $ar) !== false) $state = '';
				}
			}

			$s = array_filter([$order['s_city'], $state, $country], 'fn_string_not_empty');
			$full_address = implode(', ', $s).'<br>'.$e['office']['address_full'];

			$open_hours = str_replace(', ', '<br>', $e['office']['schedule']);
			$open_hours_raw = explode(', ', $e['office']['schedule']);
		}
	}

	return;

}
