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

function fn_cp_num2str($num)
{
    $nul = 'ноль';
    $ten = array(
        array(
            '',
            'один',
            'два',
            'три',
            'четыре',
            'пять',
            'шесть',
            'семь',
            'восемь',
            'девять'
        ),
        array(
            '',
            'одна',
            'две',
            'три',
            'четыре',
            'пять',
            'шесть',
            'семь',
            'восемь',
            'девять'
        )
    );
    $a20 = array(
        'десять',
        'одиннадцать',
        'двенадцать',
        'тринадцать',
        'четырнадцать',
        'пятнадцать',
        'шестнадцать',
        'семнадцать',
        'восемнадцать',
        'девятнадцать'
    );
    $tens = array(
        2 => 'двадцать',
        'тридцать',
        'сорок',
        'пятьдесят',
        'шестьдесят',
        'семьдесят',
        'восемьдесят',
        'девяносто'
    );
    $hundred = array(
        '',
        'сто',
        'двести',
        'триста',
        'четыреста',
        'пятьсот',
        'шестьсот',
        'семьсот',
        'восемьсот',
        'девятьсот'
    );
    $unit = array( // Units
        array(
            'копейка',
            'копейки',
            'копеек',
            1
        ),
        array(
            'рубль',
            'рубля',
            'рублей',
            0
        ),
        array(
            'тысяча',
            'тысячи',
            'тысяч',
            1
        ),
        array(
            'миллион',
            'миллиона',
            'миллионов',
            0
        ),
        array(
            'миллиард',
            'милиарда',
            'миллиардов',
            0
        )
    );
    //
    list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub) > 0) {
        foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
            if (!intval($v)) {
                continue;
            }
            $uk = sizeof($unit) - $uk - 1; // unit key
            $gender = $unit[$uk][3];
            list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2 > 1) {
                $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
            } else {
                $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            }
            // units without rub & kop
            if ($uk > 1) {
                $out[] = fn_cp_morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            }
        } //foreach
    } else {
        $out[] = $nul;
    }
    $out[] = fn_cp_morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
    $out[] = $kop . ' ' . fn_cp_morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
    return fn_cp_ucfirst(trim(preg_replace('/ {2,}/', ' ', join(' ', $out))));
}

function fn_cp_morph($n, $f1, $f2, $f5)
{
    $n = abs(intval($n)) % 100;
    if ($n > 10 && $n < 20) {
        return $f5;
    }
    $n = $n % 10;
    if ($n > 1 && $n < 5) {
        return $f2;
    }
    if ($n == 1) {
        return $f1;
    }
    return $f5;
}

function fn_cp_ucfirst($text) {
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}

function fn_cp_change_inv_date_with_m($timestamp)
{
	$m_names = array(
		1 => 'января',
		2 => 'февраля',
		3 => 'марта',
		4 => 'апреля',
		5 => 'мая',
		6 => 'июня',
		7 => 'июля',
		8 => 'августа',
		9 => 'сентября',
		10 => 'октября',
		11 => 'ноября',
		12 => 'декабря'
	);

	$d = date('d', $timestamp);
	$m = date('n', $timestamp);
	$y = date('Y', $timestamp);

	return $d . ' ' . $m_names[$m] . ' ' . $y;
}

/***********************************[hooks]***********************************/
function fn_cp_change_inv_template_document_order_context_init($document, &$order)
{
    $shipping_cost = $balance_shipping_cost = $order->data['display_shipping_cost'];
    $subtotal = $order->data['subtotal'];
    $products = &$order->data['products'];
    $count_products = count($products);
    $product_amounts = array();
    $numbers = 0;

    foreach ($products as $product_key => &$product_data) {
        $numbers++;
    	$product_data['cp_product_number'] = $numbers;
        $increment_subtotal = round($shipping_cost / ($subtotal / $product_data['display_subtotal']), 2, PHP_ROUND_HALF_DOWN);
        $increment_price = round($increment_subtotal / $product_data['amount'], 2, PHP_ROUND_HALF_DOWN);
        $product_data['original_price'] += $increment_price;
        $product_data['cp_price'] = $product_data['price'] + $increment_price;
        $product_data['display_subtotal'] = $product_data['cp_price'] * $product_data['amount'];
        $balance_shipping_cost -= round($increment_price * $product_data['amount'], 2);
        $product_amounts[$product_key] = $product_data['amount'];
    }
    
    if ($balance_shipping_cost != 0) {
        $balance_shipping_cost = round($balance_shipping_cost, 2);
        $product_amounts_count = count($product_amounts);
        $i = 1;

        foreach ($product_amounts as $product_key => $product_amount) {
            $devide = $balance_shipping_cost / $product_amount;
            $explodeDigits = explode('.', (string)$devide);

            if ((!empty($explodeDigits[1]) && strlen((string)$explodeDigits[1]) <= 2) || $product_amounts_count == $i) {
                $products[$product_key]['display_subtotal'] += $balance_shipping_cost;
                $products[$product_key]['cp_price'] += $balance_shipping_cost / $products[$product_key]['amount'];
                $products[$product_key]['original_price'] += $balance_shipping_cost / $products[$product_key]['amount'];
                break;
            }

            $i++;
        }
    }
    
    $weight = '0.000';
    if (!empty($order->data['product_groups'][0]['package_info']['W'])) {
    	$weight = $order->data['product_groups'][0]['package_info']['W'];
    }

    $order->data['cp_total_info'] = __("cp_inv_total_info", array(
    	'[number]' => $numbers,
    	'[weight]' => $weight,
    	'[total]' => $order->data['total']
    ));

    $order->data['cp_date_with_m'] = fn_cp_change_inv_date_with_m($order->data['timestamp']);
    $order->data['cp_str_total'] = fn_cp_num2str($order->data['total']);
    /*17.02.2020 gmelnikov cart-power modifs */
    if (Tygh::$app['session']['auth']['user_type'] == 'V') {
        $order->data['cp_path_to_img'] = str_replace(Registry::get('config.vendor_index'), "", fn_url());
    } else {
        $order->data['cp_path_to_img'] = str_replace(Registry::get('config.admin_index'), "", fn_url());    
    }
    /*17.02.2020 gmelnikov cart-power modifs */
    /*17.04.2020 gmelnikov cart-power modifs */
    $order->data['cp_confirm_date'] = fn_cp_change_inv_date_with_m($order->data['cp_confirm_date']);
    /*17.04.2020 gmelnikov cart-power modifs *///
    if (!empty(current($order->data['warehouse_data_points']))) {
        $wh_data_address = current($order->data['warehouse_data_points']);
        $order->data['cp_warehouse_address'] = '';
        $order->data['cp_warehouse_address'] .= !empty($wh_data_address['country_title']) ? $wh_data_address['country_title'] : '';
        $order->data['cp_warehouse_address'] .= !empty($wh_data_address['city']) ? ', ' . $wh_data_address['city'] : '';
        $order->data['cp_warehouse_address'] .= !empty($wh_data_address['pickup_address']) ? ', ' . $wh_data_address['pickup_address'] : '';
    }

}