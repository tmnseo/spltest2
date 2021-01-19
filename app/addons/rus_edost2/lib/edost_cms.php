<?php
/*********************************************************************************
Функции сопряжения системы магазина с классом калькулятора eDost.ru
Версия 2.5.1, 01.01.2020
Автор: ООО "Айсден"
*********************************************************************************/

define('EDOST_CHARSET', 'UTF'); // кодировка сайта (UTF или WIN)
define('EDOST_CACHE', 'Y'); // простой файловый кэш
define('EDOST_ICO_PATH', 'images/edost'); // путь к иконкам

include_once 'edost_class.php';
include_once 'edost_lang.php';
edost_class::$message = $MESS;


function edost_shop_delivery($id) {

	$r = false;

	$s = fn_get_shipping_info($id, DESCR_SL);
	if (!empty($s)) {
		$r = array(
			'id' => (!empty($s['shipping_id']) ? $s['shipping_id'] : ''),
			'name' => (!empty($s['shipping']) ? $s['shipping'] : ''),
			'description' => (!empty($s['description']) ? $s['description'] : ''),
		);
		if (!empty($s['icon'])) $r['image'] = $s['icon'];
	}

	return $r;

}


function edost_hint($data) {
	return ' <a class="cm-tooltip" title="'.str_replace('"', '', $data).'"><i class="icon-question-sign"></i></a>';
}

function edost_config($mode = 'get') {

	if ($mode == 'get') return Tygh\Registry::get('addons.rus_edost2.config');

    $module = array();
    $module_error = '';
    fn_trusted_vars('module');
    $new = ($mode == 'save' && !empty($_REQUEST['module']) ? $_REQUEST['module'] : false);
	$config_data = edost_class::GetMessage('EDOST_DELIVERY_CONFIG');
	$admin_sign = edost_class::GetMessage('EDOST_ADMIN');
	$control_sign = edost_class::GetMessage('EDOST_DELIVERY_CONTROL');
	$path_root = Tygh\Registry::get('config.dir.root').'/';

	$taxes = fn_get_taxes();
//	edost_class::draw_data('taxes', $taxes);

    $currencies = Tygh\Registry::get('currencies');
	if (empty($currencies[CURRENCY_RUB]) || $currencies[CURRENCY_RUB]['is_primary'] == 'N') $module_error = edost_class::GetMessage('EDOST_RUB_ERROR');
//	edost_class::draw_data('currencies', $currencies);

    // все доставки магазина
	$shippings = db_get_hash_array(
		'SELECT ?:shippings.shipping_id, ?:shippings.company_id, ?:shippings.service_id, ?:shippings.status, ?:shippings.tax_ids, ?:shipping_descriptions.shipping, ?:shipping_descriptions.description FROM ?:shippings '.
		'LEFT JOIN ?:shipping_descriptions ON ?:shipping_descriptions.shipping_id = ?:shippings.shipping_id AND ?:shipping_descriptions.lang_code = "ru"', 'shipping_id'
	);

	// все профили edost
    $services = db_get_hash_array('SELECT code, status, service_id FROM ?:shipping_services WHERE module = ?s', 'service_id', 'edost2');
	$profile = array();
	foreach ($services as $k => $v) $profile[$v['code']] = $k;
	if (!empty($shippings)) foreach ($shippings as $k => $v)
		if (!isset($services[ $v['service_id'] ])) unset($shippings[$k]);
		else {
			$s = (!empty($v['tax_ids']) ? explode(',', $v['tax_ids']) : false);
			$s = ($s !== false && isset($taxes[$s[0]]) ? $s[0] : false);
			$v['tax'] = $s;
			$v['code'] = $services[ $v['service_id'] ]['code'];
			$v['image'] = fn_get_image_pairs($v['shipping_id'], 'shipping', 'M');
			$shippings[$k] = $v;
		}
	$module[0]['shippings'] = (!empty($shippings) ? $shippings : array());
//	edost_class::draw_data('shippings', $shippings);


	// удаление модуля (отключение способов доставки eDost)
	if ($mode == 'uninstall') {
		if (!empty($shippings)) foreach ($shippings as $k => $v) if ($v['status'] !== 'D') fn_update_shipping(array('status' => 'D'), $k);
		return;
	}


	// загрузка тарифов, включенных в личном кабинете eDost
	foreach ($module as $m_key => $m) {
		if ($new === false) $c = edost_class::GetConfig($m_key);
		else {
			$n = $new[$m_key];
			$c = array();
			foreach (edost_class::$setting_key as $k => $v) $c[$k] = (isset($n['config'][$k]) ? $n['config'][$k] : '');
//			$c['param']['active'] = (!empty($n['active']) ? 'Y' : 'N');
		}
//		edost_class::draw_data('config', $c);

//		$m['active'] = (empty($c['param']['active']) || $c['param']['active'] == 'Y' ? 'Y' : '');
		$m['active'] = 'Y';
		$m['config'] = $c;

		$data = edost_class::RequestData('', $m['config']['id'], $m['config']['ps'], 'active=Y', 'delivery');
		if (isset($data['error'])) $data['error'] = edost_class::GetError($data['error']);
		$m['edost'] = $data;

		$module[$m_key] = $m;
	}
//	edost_class::draw_data('module', $module);

	foreach ($module as $m_key => $m) {
		$tariff_update = false;
		$e = (!empty($m['edost']['data']) ? $m['edost']['data'] : array());
		$s = $m['shippings'];

		// добавление нулевого тарифа
		$error = edost_class::GetMessage('EDOST_DELIVERY_ERROR');
		$e[0] = array('id' => 0, 'title' => $error['zero_tariff'], 'description' => '', 'company_id' => 0, 'profile' => 0, 'sort' => 0);

		// привязка тарифов eDost к доставкам магазина
		foreach ($s as $v)
			if (isset($e[$v['code']])) {
				if ($v['status'] == 'D') $tariff_update = true;
				$e[$v['code']] = array_merge($e[$v['code']], array('shipping_id' => $v['shipping_id'], 'title' => $v['shipping'], 'description' => $v['description'], 'tax' => $v['tax']));
			}
			else if ($v['status'] == 'A') $tariff_update = true;

		// подготовка данных для новых доставок магазина
		foreach ($e as $k => $v) if ($k != 0 && !isset($v['shipping_id'])) {
			$tariff_update = true;
			$e[$k] += array('title' => $v['company'].(!empty($v['name']) ? ' ('.$v['name'].')' : ''), 'description' => '');
		}

		// сортировка
		if (count($e) > 1) {
			$ar = array();
			foreach ($e as $v) $ar[] = $v['sort']*1000 + $v['profile'];
			array_multisort($ar, SORT_ASC, SORT_NUMERIC, $e);
			$ar = $e;
			$e = array();
			foreach ($ar as $v) $e[$v['profile']] = $v;
		}

		$module[$m_key]['edost']['data'] = $e;
		if ($tariff_update) $module[$m_key]['edost']['error'] = $admin_sign['warning']['tariff_update2'];
	}
	$module_original = $module;


	// сохранение настроек
	if ($mode == 'save') {
		// добавление профилей в базу магазина
		$set = array();
		foreach ($new as $n_key => $n) {
			$m = $module[$n_key];
			if (!empty($m['edost']['data'])) foreach ($m['edost']['data'] as $k => $v) if (!isset($profile[$k])) $set[$k] = $v;
		}
		foreach ($set as $k => $v) {
			$s = array('status' => 'A', 'module' => 'edost2', 'code' => $k, 'sp_file' => '', 'description' => $v['title'].' ['.$k.']');
			$s['service_id'] = $profile[$k] = db_query('INSERT INTO ?:shipping_services ?e', $s);
			foreach (Tygh\Languages\Languages::getAll() as $k2 => $v2) {
				$s['lang_code'] = $k2;
				db_query('INSERT INTO ?:shipping_service_descriptions ?e', $s);
			}
		}

		foreach ($new as $n_key => $n) if (isset($module[$n_key])) {
			$m = $module[$n_key];
            $e = (!empty($m['edost']['data']) ? $m['edost']['data'] : array());

			if (!empty($n['profile'])) foreach ($n['profile'] as $k => $v) {
				if (!empty($n['reload_vat'])) $tax = (isset($n['reload_vat_value']) ? intval($n['reload_vat_value']) : 0);
				else $tax = (!empty($v['tax']) ? intval($v['tax']) : 0);
				$n['profile'][$k]['tax_ids'] = (!empty($tax) && $k != 0 ? array($tax) : array());
			}

			// добавление доставок в базу магазина
			foreach ($e as $k => $v)
				if (isset($v['shipping_id'])) $m['shippings'][ $v['shipping_id'] ]['active'] = true;
				else {
					$company_id = (fn_allowed_for('ULTIMATE') ? fn_get_default_company_id() : 0);
					$s = array(
						'shipping' => (isset($n['profile'][$k]['title']) ? $n['profile'][$k]['title'] : $v['title']),
						'description' => (isset($n['profile'][$k]['description']) ? $n['profile'][$k]['description'] : ''),
						'rate_calculation' => 'R',
						'tax_ids' => (!empty($n['profile'][$k]['tax_ids']) ? $n['profile'][$k]['tax_ids'] : array()),
						'position' => 100,
						'min_weight' => '',
						'max_weight' => '',
						'status' => 'A',
						'is_address_required' => 'Y',
						'free_shipping' => 'N',
						'usergroup_ids' => 0,
						'service_id' => $profile[$k],
						'localization' => '',
						'delivery_time' => '',
						'company_id' => $company_id,
					);
					$e[$k]['new'] = true;
					$e[$k]['shipping_id'] = fn_update_shipping($s, 0, 'ru');
					if (fn_allowed_for('ULTIMATE')) db_query('INSERT INTO ?:ult_objects_sharing ?e', array('share_company_id' => $company_id, 'share_object_id' => $e[$k]['shipping_id'], 'share_object_type' => 'shippings'));
				}

			// добавление иконок в папку "images/edost"
			$company_ico = array();
			foreach ($e as $k => $v) if (!empty($v['new']) || !empty($n['reload_ico'])) $company_ico[edost_class::GetCompanyIco($v['company_id'], $v['id'])] = true;
			if (!empty($company_ico)) {
				$company_ico['s1'] = true;
				$path = $path_root.EDOST_ICO_PATH.'/company';
				foreach ($company_ico as $k => $v) {
					$ico = '/'.$k.'.gif';
					if (!empty($n['reload_ico']) || !fn_check_path($path.$ico)) {
						$s = fn_get_contents('https://edostimg.ru/img/companyico'.$ico);
						if (empty($s) || fn_put_contents($path.$ico, $s) === false) break;
					}
				}
			}
/*
			// добавление иконки к доставке
			foreach ($e as $k => $v) if (!empty($v['new']) || !empty($n['reload_ico'])) {
				$s = fn_get_url_data('https://edostimg.ru/img/companyico/'.edost_class::GetCompanyIco($v['company_id'], $v['id']).'.gif');
				if (empty($s['name'])) continue;
				$s['is_high_res'] = 'Y';
				$key = fn_update_image_pairs(array($s), array(), array(array('pair_id' => '', 'type' => 'M', 'object_id' => $v['shipping_id'], 'image_alt' => '')), $v['shipping_id'], 'shipping');
			}
*/
			// обновление доставки
			foreach ($m['shippings'] as $k => $v) {
				$s = false;
				if (!empty($v['active'])) {
					$p = (isset($n['profile'][ $v['code'] ]) ? $n['profile'][ $v['code'] ] : false);

					$s = array('status' => (!empty($m['active']) ? 'A' : 'D'));
					if (isset($p['title'])) {
						$s['shipping'] = $p['title'];
						if (isset($p['description'])) $s['description'] = $p['description'];
						$s['tax_ids'] = (!empty($p['tax_ids']) ? $p['tax_ids'] : array());
					}
				}
				else if (!empty($n['delete_disabled_tariff'])) fn_delete_shipping($k);
				else if ($v['status'] !== 'D') $s = array('status' => 'D');
				if ($s !== false) fn_update_shipping($s, $k); // fn_update_shipping($s, $k, DEFAULT_LANGUAGE);
			}
		}

		// настройки модуля
		$config = array();
		foreach ($module_original as $m_key => $m) {
			$c = '';
			if (!empty($m['config'])) {
				$c = array();
				foreach (edost_class::$setting_key as $k => $v)
					if (isset($config_data['field'][$k]['TYPE']) && $config_data['field'][$k]['TYPE'] == 'CHECKBOX') $c[] = (!empty($m['config'][$k]) ? 'Y' : '');
					else $c[] = (isset($m['config'][$k]) ? $m['config'][$k] : '');
				$c = implode(';', $c);
			}
			$m['config_string'] = $c;

			$c = edost_class::$setting_param_key;
			$c['active'] = ($m['active'] == 'Y' ? 'Y' : 'N');
			$c['module_id'] = $m_key;
			foreach ($m['shippings'] as $v) if ($v['code'] == 0) $c['zero_tariff'] = $v['shipping_id'];
			$m['param_string'] = implode(';', $c);

			$config[$m_key] = $m;
		}
		$s = array();
		foreach ($config as $k => $v) $s[$k] = $v['config_string'].';param='.$v['param_string'];
		Tygh\Settings::instance()->updateValue('config', serialize($s), 'rus_edost2');

//		edost_class::draw_data('set', $set);
//		edost_class::draw_data('profile', $profile);
//		edost_class::draw_data('services', $services);
//		edost_class::draw_data('module', $module);
//		edost_class::draw_data('_REQUEST2', $new);

		return;
	}


	// вывод настроек
	$r = '';
	$module_id = 0;

	// статусы заказов
	$order_status = array('' => $config_data['no_change']);
	$s = db_get_hash_array(
		'SELECT ?:statuses.status, ?:statuses.status_id, ?:statuses.type, ?:status_descriptions.description FROM ?:statuses '.
		'LEFT JOIN ?:status_descriptions ON ?:statuses.status_id = ?:status_descriptions.status_id AND ?:status_descriptions.lang_code = "ru" WHERE ?:statuses.type = "O"', 'status'
	);
	foreach ($s as $k => $v) $order_status[$k] = $v['description'].' ['.$k.']';
//	edost_class::draw_data('order_status', $order_status);

	// порядок сортировки
	$s = array('id', 'ps', 'host', 'hide_error', 'show_zero_tariff', 'template_script', 'send_zip', 'sort_ascending', 'cod_status', 'edost_discount', 'sale_discount', 'sale_discount_cod', 'package', 'template', 'template_ico', 'template_format', 'template_autoselect_office');
//	'admin', 'map', 'autoselect', 'hide_payment', 'template_block', 'template_block_type', 'template_cod', 'template_autoselect_office', 'template_map_inside'

	$field = array_fill_keys($s, array());
	foreach ($field as $k => $v) {
		$s = (isset($config_data['field'][$k]) ? $config_data['field'][$k] : false);
		$v['VALUE'] = (isset($module[$module_id]['config'][$k]) ? $module[$module_id]['config'][$k] : '');
		$v['TYPE'] = (isset($s['TYPE']) ? $s['TYPE'] : 'DROPDOWN');
		$v['TITLE'] = (isset($s['TITLE']) ? $s['TITLE'] : '');
		$v['DEFAULT'] = (isset(edost_class::$setting_key[$k]) ? edost_class::$setting_key[$k] : '');
		if ($v['TYPE'] == 'DROPDOWN') $v['VALUES'] = (isset($s['VALUES']) ? $s['VALUES'] : array());
		$field[$k] = $v;
	}
	$module[$module_id]['field'] = $field;
//	edost_class::draw_data('module', $module);

	$module_count = 1;
	$module_list = $module_false = $module_site = $module_new = false;
	$delimiter = '<div class="edost_delimiter" style="border-width: 1px 0 0 0; border-color: #EEE; border-style: solid;"></div>';
	$delimiter2 = '<div style="padding: 25px 0 15px 0;">'.$delimiter.'</div>';

	$r .= '
	<style>
		.tooltip { max-width: 400px !important; }
		.tooltip span.edost_hint_blue { color: #b5c9ff; }
		.edost_setting_param label { display: inline !important; }
		div.object-container { padding-bottom: 5px !important; }
		.checkbox { padding-left: 10px !important; }

		div.checkbox input[type="checkbox"]:checked + label { color: #000; }
		div.checkbox input[type="checkbox"] + label { color: #888; }
		div.checkbox input[type="checkbox"]:checked + label.red { color: #F00; }
		div.checkbox input[type="checkbox"] + label.red { color: #888; }
	</style>';

	$i = 0;
	foreach ($module as $m_key => $m) {
		$module_key_start = $m_key;
		$i++;

		$active_head = ($module_new ? '<span style="color: #F00;">'.$control_sign['new_module'].'</span>' : $admin_sign['module_config']['active_head'].' <span style="font-size: 18px; color: #888;">['.$m_key.']</span>');
		$active = '
		<div class="checkbox" style="padding-left: 40px;">
			<input id="module_active_'.$m_key.'" name="module['.$m_key.'][active]" type="checkbox"'.($m['active'] == 'Y' ? ' checked=""' : '').' onclick="edost_SetData(\'module_active\', \''.$m_key.'\')">
			<label for="module_active_'.$m_key.'" class="green" style="font-size: 18px;">'.$admin_sign['module_config']['active'].'</label>
		</div>';

		if ($module_list) {
			$r .= '
			<div id="module_active_'.$m_key.'_main" class="'.($m['active'] == 'Y' ? '' : 'edost_module_off').'" style="font-size: 20px; background: #F0F0F0; padding: 5px 10px; '.($i > 1 ? 'margin-top: 15px;' : '').'">
				<table width="100%" border="0" cellpadding="4" cellspacing="0"><tr>
					<td width="150">
						<span class="edost_module_config_head edost_module_light">'.str_replace('18px', '14px', $active_head).'</span>
						'.$active.'
					</td>
					<td width="160" class="edost_module_light">';
					if (!empty($m['CONFIG']['CONFIG']['template'])) {
						$n = $m['CONFIG']['CONFIG']['template'];
						if (!empty($n['VALUE'])) $r .= '<span class="edost_module_config_head">'.$admin_sign['module_config']['template_head'].'</span> <span class="edost_module_config_value">'.$n['OPTIONS'][$n['VALUE']].'</span>';
					}
					$r .= '
					</td>
					<td class="edost_module_light">';
					if ($module_site) $r .= '<span class="edost_module_config_head">'.$admin_sign['module_config']['site_head_small'.(count($m['site_string']) > 1 ? '2' : '')].'</span> <span class="edost_module_config_value">'.implode('<br>', $m['site_string']).'</span>';
					$r .= '
					</td>
					<td width="120" class="edost_module_light" style="text-align: right;">
						<a class="edost_office_button edost_office_button_blue" onclick="edost_SetData(\'get\', \''.$m_key.'\'); return false;" href="/bitrix/admin/edost.php?lang=ru&type=setting&module='.$m_key.'">'.$admin_sign['module_config']['setting'].'</a>
					</td>
				</tr></table>';
				if ($m['active'] == 'Y' && isset($m['edost']['error'])) $r .= '<div style="margin: 0 0 5px 5px; color: #F00; font-size: 16px;">'.$m['edost']['error'].'</div>';
			$r .= '
			</div>';
		} else {
//			if ($module_count != 0) $r .= '<div style="font-size: 20px; background: #F0F0F0; padding: 5px 10px; margin-bottom: 20px;">'.$active_head.'</div>';
//			$r .= $active;

			$r .= '<div id="module_'.$m_key.'_div" style="padding-top: 7px;"><div>';

			if (!$module_new && (isset($m['edost']['error']) || $module_error)) $r .= '<div id="module_error" style="margin: 5px 0 0 0; color: #F00; font-size: 20px; text-align: center;">'.(isset($m['edost']['error']) ? $m['edost']['error'] : $module_error).'</div>'.$delimiter2;

			if ($module_site) {
				$r .= '
					<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
						<td><div id="module_'.$m_key.'_site">
							<div style="color: #888;">'.$admin_sign['module_config']['site_head'].'</div>';
				foreach ($m['site_list'] as $k => $v) $r .= '
							<div class="checkbox" style="padding: 2px 0;">
								<input id="'.$v['code'].'" name="'.$v['code'].'" onclick="edost_SetData(\'site\', this)" data-site="'.$v['id'].'" data-start="'.($v['active'] ? 'Y' : 'N').'" style="margin: 0px;" type="checkbox" '.($v['active'] ? 'checked=""' : '').'>
								<label for="'.$v['code'].'"><b>'.$v['name'].'</b></label>
							</div>';
				$r .= '
						</div></td>
						<td style="padding-left: 15px; opacity: 0.5;">'.$admin_sign['site_warning'].'</td>
					</tr></table>
					'.$delimiter2;
			}

			foreach ($m['field'] as $k => $v) {
				$c = (isset($admin_sign['module_config'][$k]) ? $admin_sign['module_config'][$k] : false);
				$id = 'module_'.$m_key.'_'.$k;
				$name = 'module['.$m_key.'][config]['.$k.']';
				$top = ($k == 'id' ? 0 : 5);
				if (in_array($k, array('hide_error', 'control', 'template'))) {
					$r .= $delimiter2;
					$top = 0;
				}
				$style = array();
				if (in_array($k, array('host'))) $style[] = 'font-weight: normal;';
				$class = array();
				if (in_array($k, array('id', 'ps', 'host'))) $class[] = 'edost_setting_param_head';
				if ($k == 'template') $class[] = 'orange';

                $r .= '<div id="'.$id.'_div" class="checkbox edost_setting_param" style="margin-top: '.$top.'px;">';

				if ($v['TYPE'] == 'CHECKBOX') {
					$r .= '<input id="'.$id.'" name="'.$name.'" '.(!empty($c['update']) ? 'onclick="edost_SetData(\'module_update\', \''.$m_key.'\')"' : '').' style="margin: 2px 5px 0 0;" type="checkbox"'.($v['VALUE'] == 'Y' ? ' checked=""' : '').'>';
					$r .= '<label style="display: inline;" for="'.$id.'" '.($k == 'control' || $k == 'template' ? 'class="orange"' : '').'><b>'.$v['TITLE'].'</b></label>';
				}
				else $r .= '<b '.(!empty($class) ? 'class="'.implode(' ', $class).'"' : '').' '.(!empty($style) ? 'style="'.implode(' ', $style).'"' : '').'>'.$v['TITLE'].'</b>'.($v['TYPE'] == 'TEXT' ? ': ' : '');

				if ($v['TYPE'] == 'TEXT') {
					$length = (!empty($c['length']) ? $c['length'] : 40);
					$r .= '<input class="normal" id="'.$id.'" data-start="'.$v['VALUE'].'" name="'.$name.'" value="'.$v['VALUE'].'" type="text" style="padding: 0px 4px; width: '.($length*7).'px;" maxlength="'.$length.'">';
				}
				if ($v['TYPE'] == 'DROPDOWN') {
					if ($k == 'cod_status') $ar = $order_status; else $ar = $v['VALUES'];
					$r .= ' <select id="'.$id.'" name="'.$name.'" '.(!empty($c['update']) ? 'onclick="edost_SetData(\'module_update\', \''.$m_key.'\')"' : '').' style="vertical-align: baseline; max-width: 300px;">';
					foreach ($ar as $k2 => $v2) $r .= '<option value="'.$k2.'" '.($k2 == $v['VALUE'] ? 'selected=""' : '').'>'.$v2.'</option>';
					$r .= '</select>';
				}

				$r .= (!empty($c['note']) ? ' <span class="note">'.$c['note'].'</span>' : '');
				if (!empty($c['hint'])) $r .= edost_hint($c['hint']);

				$r .= '</div>';
			}

			$r .= $delimiter2;

			$c = false;
			$count = 0;
			foreach ($m['edost']['data'] as $p) if (isset($p['company_id'])) $count++;
			$r .= '<div id="setting_module_tariff_'.$m_key.'" class="checkbox" style="padding-right: 10px;"><table width="100%" border="0" cellpadding="2" cellspacing="0">';
			foreach ($m['edost']['data'] as $p) if (isset($p['company_id'])) {
				$name = 'module['.$m_key.'][profile]['.$p['profile'].']';

				$ico = EDOST_ICO_PATH.'/company/'.edost_class::GetCompanyIco($p['company_id'], $p['id']).'.gif';
//				if ($m['config']['template_ico'] == 'C') $ico = EDOST_ICO_PATH.'/company/'.edost_class::GetCompanyIco($p['company_id'], $p['id']).'.gif';
//				else if (!empty($p['shipping_id']) && !empty($m['shippings'][$p['shipping_id']]['image'])) $ico = $m['shippings'][$p['shipping_id']]['image']['icon']['image_path'];

				if (empty($ico) || !fn_check_path($path_root.$ico)) $ico = '<div style="display: inline-block; width: 22px;"></div>';
				else $ico = '<img class="edost_ico" style="width: 20px; vertical-align: middle; padding-right: 2px; padding-bottom: 3px;" src="'.$ico.'" border="0">';
				if (!empty($p['shipping_id'])) $ico = '<a class="edost_link" href="admin.php?dispatch=shippings.update&shipping_id='.$p['shipping_id'].'&selected_section=general" target="_blank">'.$ico.'</a>';

				if ($p['company_id'] == 0) $r .= '<tr style="padding-top: 10px; font-size: 14px; color: #888;"><td></td><td>'.$admin_sign['zero_tariff'].' '.edost_hint($admin_sign['zero_tariff_hint']).'</td><tr>';

				if ($c && $c != $p['company_id']) $r .= '<tr style="height: 8px;"><td></td><tr>';

				$r .= '<tr style="margin-top: '.($c && $c != $p['company_id'] ? 10 : 4).'px;">
					<td width="22">'.$ico.'</td>
					<td width="40%"><input class="normal" name="'.$name.'[title]" value="'.str_replace('"', '&quot;', $p['title']).'" type="text" style="width: 100%;" maxlength="100"></td>
					<td style="padding-left: 5px;"><input class="normal" name="'.$name.'[description]" value="'.str_replace('"', '&quot;', $p['description']).'" type="text" style="margin-left: 10px; width: 100%;" maxlength="2000"></td>';
				if (empty($taxes) || $p['company_id'] == 0) $r .= '<td width="10"></td>';
				else {
					$r .= '<td width="120" style="padding-left: 25px;"><select name="'.$name.'[tax]" style="width: 100%;"><option value="0"></option>';
					foreach ($taxes as $k2 => $v2) $r .= '<option value="'.$k2.'" '.(isset($p['tax']) && $p['tax'] !== false && $k2 == $p['tax'] ? 'selected=""' : '').'>'.$v2['tax'].'</option>';
					$r .= '</select></td>';
				}
				$r .= '</tr>';

				if ($c === false && $count > 1) {
					$r .= '<tr style="height: 30px; font-size: 14px; color: #888; vertical-align: bottom;">
						<td></td>
						<td width="40%">'.$admin_sign['tariff_title'].' '.edost_hint($admin_sign['tariff_title_hint']).'</td>
						<td style="padding-left: 15px;">'.$admin_sign['tariff_description'].'</td>';
					if (!empty($taxes)) $r .= '<td width="80" style="padding-left: 25px;">'.$admin_sign['tariff_vat'].'</td>';
					$r .= '</tr>';
				}

				$c = $p['company_id'];
			}
			$r .= '</table></div>';

			$r .= $delimiter2;

			$ar = array();
			$ar[] = 'reload_ico';
			if (!empty($taxes)) $ar[] = 'reload_vat';
			$ar[] = 'delete_disabled_tariff';
			foreach ($ar as $v) {
				$name = 'module['.$m_key.']['.$v.']';
				$id = str_replace(array('][', '[', ']'), '_', substr($name, 0, -1));
				$onclick = ($v == 'reload_vat' ? 'onclick="var E = document.getElementById(\'module_0_reload_vat_value\'); if (E) E.style.display = (this.checked ? \'inline\' : \'none\');"' : '');
				$r .= '<div class="checkbox edost_setting_param" style="margin-top: 5px;">';
				$r .= '<input id="'.$id.'" name="'.$name.'" style="margin: 2px 5px 0 0;" type="checkbox" '.$onclick.'> <label class="red" for="'.$id.'"><b>'.$admin_sign['module_config'][$v].'</b></label>';
				if ($v == 'reload_vat') {
					$r .= '<select id="module_'.$m_key.'_'.$v.'_value" name="module['.$m_key.']['.$v.'_value]" style="width: 140px; margin: -15px 0 -15px 10px; display: none;"><option value="0">'.$admin_sign['module_config']['vat_zero'].'</option>';
					foreach ($taxes as $k2 => $v2) $r .= '<option value="'.$k2.'">'.$v2['tax'].'</option>';
					$r .= '</select>';
				}
				$r .= '</div>';
			}

			$r .= '</div></div>';
		}
	}

	return $r;

}


class edost_currency {
	public static $class;
	public static $active;
	public static $base = 'RUB';
	public static $shop;

	public static function convert($v, $currency = 'base') {
		return fn_format_price_by_currency($v, $currency == 'base' ? 'RUB' : $currency, $currency == 'base' ? CART_PRIMARY_CURRENCY : 'RUB');
	}
	public static function format($v) {
		return Tygh::$app['formatter']->asPrice($v, CART_PRIMARY_CURRENCY, true, true);
	}
}


class edost_cache {
	private $id = false;
	private $time = false;
	private $date = false;
	public static $class;
	public static $path;

	// иницилизация кэша и выдача данных ($id - ключ кэширования,  $time - время кэширования в секундах)
	function get($id, $time) {

		$data = false;
		$this->id = urlencode($id);
		$this->time = $time;

		if (defined('EDOST_CACHE')) {
			// простой файловый кэш
			$ar = array();
			$ar[] = $this->date = date('Ymd');
			$ar[] = date('Ymd', strtotime('-1 day'));
			foreach ($ar as $v) {
				$data = edost_class::ReadData(self::$path.'/'.$v.'/'.$this->id, true);
				if (empty($data['time_write']) || $data['time_write'] + $data['time_save'] < time()) $data = false;
				else {
					$data = $data['data'];
					break;
				}
			}
		}
//		else {
//	        $data = self::$class->cache->get($this->id);
//		}

		return $data;

	}

	// запись данных в кэш ($data - массив с данными)
	function set($data) {

		if (defined('EDOST_CACHE')) {
			// простой файловый кэш
			if (!file_exists(self::$path)) mkdir(self::$path);

			$data = array('time_write' => time(), 'time_save' => $this->time, 'data' => $data);
			$s = edost_cache::$path.'/'.$this->date;
			if (!file_exists($s)) mkdir($s);
			edost_class::WriteData($s.'/'.$this->id, $data, true);
		}
//		else {
//    	    self::$class->cache->set($this->id, $data);
//		}

	}

	// удаление кэша
	public static function free() {
		if (empty(self::$path) || strpos(self::$path, 'edost2') === false || !file_exists(self::$path)) return;
		self::delete_dir(self::$path);
	}

	// удаление директории (со всеми файлами)
	private static function delete_dir($src) {
		$dir = opendir($src);
		while (false !== ($file = readdir($dir))) if ($file != '.' && $file != '..') {
			$full = $src.'/'.$file;
			if (is_dir($full)) self::delete_dir($full); else unlink($full);
		}
		closedir($dir);
		rmdir($src);
	}
}

edost_cache::$path = Tygh\Registry::get('config.dir.cache_misc').'edost/';

?>