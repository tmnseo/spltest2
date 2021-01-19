<?php
namespace Tygh\Shippings\Services;

use Tygh\Tygh;
use Tygh\Registry;
use Tygh\Shippings\IService;
use Tygh\Shippings\IPickupService;
use Tygh\Backend\Cache;

class Edost2 implements IService, IPickupService {
    private $_allow_multithreading = false;
    public $calculation_currency = 'RUB';
    protected $shipping_info;
    private $_error_stack = array();
    public $company_id = 0;
	public static $location = false;

    public static function getInfo() {
        return [
            'name' => 'eDost',
            'tracking_url' => '',
        ];
    }

    public function getPickupMinCost() {
        return null;
    }

    public function getPickupPoints() {
        return [];
    }

    public function getPickupPointsQuantity() {
        return false;
    }

    public function allowMultithreading() {
        return $this->_allow_multithreading;
    }

    private function _internalError($error) {
        $this->_error_stack[] = $error;
    }

	// system
    public function processErrors($response) {
		return '';
    }

	// system
    public function prepareData($shipping_info) {
        $this->shipping_info = $shipping_info;
        $this->company_id = Registry::get('runtime.company_id');
    }

	// system
    public function getRequestData() {
        return array();
    }

	// system
    public function getSimpleRates() {
		return '';
    }

	// system
	public function processResponse($response) {

		$order = array();
		$info = $this->shipping_info;
		$weight_ratio = Registry::get('settings.General.weight_symbol_grams')/1000;
		$shipping_settings = $info['service_params'];
		$origination = $info['package_info']['origination'];
		$location = $info['package_info']['location'];
		$profile = $info['service_code'];
//		\edost_class::draw_data('this', $info['package_info']);

		// определение местоположения
		$s = false;
		if (!empty(self::$location)) {
			$s = self::$location['data'];
			$ar = array('country', 'state', 'city', 'zipcode');
			foreach ($ar as $v) if (empty($location[$v]) || self::$location[$v] != $location[$v]) { $s = false; break; }
		}
		if (empty($s)) {
			$country = \edost_class::GetEdostLocationID(!empty($location['country']) ? $location['country'] : 'RUS', '', 'iso2');
			$region = (!empty($location['state']) ? \edost_class::GetEdostLocationID($country, $location['state'], 'iso') : false);
			$city = \edost_class::GetCity($country, $region, !empty($location['city']) ? $location['city'] : '');
			$s = array(
				'country' => $country,
				'region' => ($region !== false ? $region : ''),
				'city' => $city,
				'zip' => (isset($location['zipcode']) ? $location['zipcode'] : ''),
			);
			self::$location = $location;
			self::$location['data'] = $s;
		}
		$order['location'] = $s;

		// загрузка товаров из корзины
		$items = array();
		if (!defined('EDOST_CART') || EDOST_CART != 'N') {
			$cart = &Tygh::$app['session']['cart'];
			$key = (isset($info['keys']['group_key']) ? $info['keys']['group_key'] : 0);
            if (isset($cart['edost_items']) && isset($cart['product_groups'][$key])) {
				foreach ($cart['product_groups'][$key]['products'] as $k => $v) if (!empty($v['amount'])) {
					if (!isset($cart['edost_items'][$k])) { $items = array(); break; }
					$s = $cart['edost_items'][$k];
					$items[] = array(
						'weight' => (isset($s['weight']) ? $s['weight'] : 0) * $weight_ratio,
						'price' => $v['price'],
						'size' => array(
							(!empty($s['shipping_params']['box_length']) ? $s['shipping_params']['box_length'] : 0),
							(!empty($s['shipping_params']['box_width']) ? $s['shipping_params']['box_width'] : 0),
							(!empty($s['shipping_params']['box_height']) ? $s['shipping_params']['box_height'] : 0),
						),
						'quantity' => $v['amount'],
					);
				}
			}
		}
		// загрузка товаров из 'package_info' (если недоступна корзина)
		if (empty($items)) if (!empty($info['package_info']['packages'])) foreach ($info['package_info']['packages'] as $v) if (!empty($v['amount'])) $items[] = array(
			'weight' => ($v['weight'] / $v['amount']) * $weight_ratio,
			'price' => $v['cost'] / $v['amount'],
			'size' => array(
				(!empty($v['shipping_params']['box_length']) ? $v['shipping_params']['box_length'] : 0),
				(!empty($v['shipping_params']['box_width']) ? $v['shipping_params']['box_width'] : 0),
				(!empty($v['shipping_params']['box_height']) ? $v['shipping_params']['box_height'] : 0),
			),
			'quantity' => $v['amount'],
		);
		$order['items'] = $items;

		$config = \edost_class::GetConfig();
		$order['config'] = fn_rus_edost2_set_config($config);
		$data = \edost_class::Calculate($order);
//		\edost_class::draw_data('result', \edost_class::$result);
//		\edost_class::draw_data('config', $config);
//		\edost_class::draw_data('order', $order);

		$r = array(
			'cost' => false,
			'error' => false,
		);

		// вывод результата
		if (isset($data['data'][$profile])) {
			$v = $data['data'][$profile];
			$r['cost'] = $v['price'];
			$r['delivery_time'] = $v['day'];
		}
		else if ($profile == 0 && empty($data['data']) && empty($data['hide']) && ($config['hide_error'] != 'Y' || $config['show_zero_tariff'] == 'Y')) {
			$r['cost'] = 0;
			$r['delivery_time'] = '';
		}

		return $r;

	}

}
