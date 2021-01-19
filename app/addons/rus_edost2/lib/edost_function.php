<?
/*********************************************************************************
Пользовательские функции модуля eDost (при обновлении данный файл не переписывается)

Для подключения в файле 'edost_const.php' должна быть установлена константа:
define('EDOST_FUNCTION', 'Y');
*********************************************************************************/

class edost_function {

	// вызывается перед расчетом доставки
	public static function BeforeCalculate(&$order, &$config) {
/*
		$order - оригинальные параметры заказа
		$config - настройки модуля

		return false; // продолжить выполнение расчета
		return array('hide' => true); // отключить модуль (не производится запрос на сервер, не выводится ошибка)
		return array('data' => array( тарифы доставки )); // сбросить расчет и заменить результат массивом 'data' (формат должен соответствовать стандарту eDost)
*/
//		echo '<br><b>BeforeCalculate - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
/*
		// запись входных данных в лог
		if (!empty($order['original'])) {
			$fp = fopen(dirname(__FILE__)."/edost.log", "a");
			fwrite($fp, date("Y.m.d H:i:s").' | '.getenv('REMOTE_ADDR').' | '.edost_class::implode2(array(", ", ' | '), $order['original']).' | '.$GLOBALS['APPLICATION']->GetCurPage()."\r\n");
			fclose($fp);
		}
*/

//		echo '<br>SERVER[REQUEST_URI]:'.$_SERVER['REQUEST_URI'];
//		$_SESSION['EDOST']['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
//		unset($_SESSION['EDOST']['compact_tariff']); // сбросить выбранные доставки в компактном формате
//		unset($_SESSION['EDOST']['delivery_default']); // сбросить выбранные доставки в закладках
//		unset($_SESSION['EDOST']['office_default']); // сбросить выбранные на карте пункты выдачи

/*
		// вывести собственный тариф для указанных местоположений (вместо реального расчета)
		$ar = array('Москва');
		if (in_array($order['location']['city'], $ar)) {
			return array(
				'sizetocm' => '1', // коэффициент пересчета габаритов магазина в сантиметры
				'data' => array(
					9 => array( // тариф "СПСР Экспресс"
						'id' => 5,
						'price' => 400,
						'priceinfo' => 0,
						'pricecash' => 500,
						'transfer' => 0,
						'day' => '3-4 дня',
						'insurance' => 0,
						'company' => 'СПСР Экспресс',
						'name' => 'пеликан-стандарт',
						'format' => 'door',
						'company_id' => 1,
						'city' => '',
						'profile' => 9,
						'sort' => 4,
					)
				)
			);
		}
*/

/*
		// изменить ид и пароль от сервера eDost (например, когда у магазина несколько филиалов в разных городах, и требуется изменять город отправки в зависимости от местонахождения покупателя)
		$config['id'] = '12345';
		$config['ps'] = 'aaaaa';
*/

		// отключить модуль на странице оформления заказа
//		if (strpos($_SERVER['REQUEST_URI'], '/checkout') !== false) return array('hide' => true);

/*
		// отключить модуль для указанных местоположений
		$ar = array('Москва');
		if (in_array($order['location']['city'], $ar)) return array('hide' => true);
*/

		return false;

	}


	// вызывается после обработки параметров заказа и перед запросом на сервер eDost
	public static function BeforeCalculateRequest(&$order, &$config) {
/*
		$order - модифицированные параметры заказа
		$config - настройки модуля

		return false; // продолжить выполнение расчета
		return array('hide' => true); // отключить модуль (не производится запрос на сервер, не выводится ошибка)
		return array('data' => array( тарифы доставки )); // сбросить расчет и заменить результат массивом 'data' (формат должен соответствовать стандарту eDost)

		расчет производится по параметрам:
			$order['location'] - страна, регион, город и почтовй индекс
			$order['weight'] - вес заказа в граммах
			$order['price'] - цена заказа в рублях
			$order['size'] - массив с габаритами заказа (единица измерения должна совпадать с размерностью в личном кабинете eDost)
				Предупреждение: на выходе габариты должны быть отсортированы по возрастанию - пример: $order['size'] = array(30, 10, 20);  sort($order['size']);
*/

//		echo '<br><b>BeforeCalculateRequest - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';

//		$order['size'] = array(10, 20, 30);
//		$order['weight'] = 5;
//		$order['weight'] += 1;
//		$order['price'] = 5000;

/*
		// добавить вес на упаковку для указанных местоположений
		$ar = array('Москва');
		if (in_array($order['location']['city'], $ar)) $order['weight'] += 0.1;
*/

		return false;

	}


	// вызывается после расчета доставки
	public static function AfterCalculate($order, $config, &$result) {
/*
		$order - модифицированный массив битрикса с параметрами расчета
		$config - настройки модуля
		$result - результат расчета
*/
//		echo '<br><b>AfterCalculate - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>AfterCalculate - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';

/*
		if (empty($result['cache'])) {
			// первый расчет (данные получены с сервера eDost)
		}
		else {
			// повторный расчет (данные были загружены из кэша магазина)
		}
*/

/*
		// удаление тарифов почты, если есть любые другие тарифы
		if (!empty($result['data'])) {
			$n = 0;
			foreach ($result['data'] as $k => $v) if ($v['format'] == 'post') $n++;
			if (count($result['data']) != $n) foreach ($result['data'] as $k => $v) if ($v['format'] == 'post') unset($result['data'][$k]);
		}
*/

/*
		// удаление тарифов СДЭК, если есть тарифы boxberry
		$company_id_delete = array(5); // id компаний которые необходимо удалить (СДЭК)
		$company_id_exists = array(30); // id компаний которые должны остаться (boxberry)
		if (!empty($result['data'])) {
			$ar = array(array('office'), array('door', 'house')); // формат доставки по которому проходит удаление
			foreach ($ar as $f) {
				$a = false;
				foreach ($result['data'] as $k => $v) if (in_array($v['company_id'], $company_id_exists) && in_array($v['format'], $f)) { $a = true; break; }
				if ($a) foreach ($result['data'] as $k => $v) if (in_array($v['company_id'], $company_id_delete) && in_array($v['format'], $f)) unset($result['data'][$k]);
			}
		}
*/

/*
		// скидка на доставку (фиксированная стоимость, блокировка наложки) в зависимости от местоположения (региона, страны)
		$ar = array(
			array(
//				'region_code' => array(22), // названия регионов для которых будет действовать скидка  -  array(22 => 'Алтайский край', 28 => 'Амурская область', 29 => 'Архангельская область', 30 => 'Астраханская область', 31 => 'Белгородская область', 32 => 'Брянская область', 33 => 'Владимирская область', 34 => 'Волгоградская область', 35 => 'Вологодская область', 36 => 'Воронежская область', 79 => 'Еврейская АО', 75 => 'Забайкальский край', 37 => 'Ивановская область', 38 => 'Иркутская область', 7 => 'Кабардино-Балкарская Республика', 39 => 'Калининградская область', 40 => 'Калужская область', 41 => 'Камчатский край', 9 => 'Карачаево-Черкесская Республика', 42 => 'Кемеровская область', 43 => 'Кировская область', 44 => 'Костромская область', 23 => 'Краснодарский край', 24 => 'Красноярский край', 45 => 'Курганская область', 46 => 'Курская область', 47 => 'Ленинградская область', 48 => 'Липецкая область', 49 => 'Магаданская область', 50 => 'Московская область', 51 => 'Мурманская область', 83 => 'Ненецкий АО', 52 => 'Нижегородская область', 53 => 'Новгородская область', 54 => 'Новосибирская область', 55 => 'Омская область', 56 => 'Оренбургская область', 57 => 'Орловская область', 58 => 'Пензенская область', 59 => 'Пермский край', 25 => 'Приморский край', 60 => 'Псковская область', 1 => 'Республика Адыгея', 4 => 'Республика Алтай', 2 => 'Республика Башкортостан', 3 => 'Республика Бурятия', 5 => 'Республика Дагестан', 6 => 'Республика Ингушетия', 8 => 'Республика Калмыкия', 10 => 'Республика Карелия', 11 => 'Республика Коми', 12 => 'Республика Марий Эл', 13 => 'Республика Мордовия', 14 => 'Республика Саха (Якутия)', 15 => 'Республика Северная Осетия - Алания', 16 => 'Республика Татарстан', 17 => 'Республика Тыва', 19 => 'Республика Хакасия', 61 => 'Ростовская область', 62 => 'Рязанская область', 63 => 'Самарская область', 64 => 'Саратовская область', 65 => 'Сахалинская область', 66 => 'Свердловская область', 67 => 'Смоленская область', 26 => 'Ставропольский край', 68 => 'Тамбовская область', 69 => 'Тверская область', 70 => 'Томская область', 71 => 'Тульская область', 72 => 'Тюменская область', 18 => 'Удмуртская Республика', 73 => 'Ульяновская область', 27 => 'Хабаровский край', 86 => 'Ханты-Мансийский АО', 74 => 'Челябинская область', 20 => 'Чеченская Республика', 21 => 'Чувашская Республика', 87 => 'Чукотский АО', 89 => 'Ямало-Ненецкий АО', 76 => 'Ярославская область', 90 => 'Байконур', 91 => 'Республика Крым', 77 => 'Москва', 78 => 'Санкт-Петербург', 92 => 'Севастополь')
//				'country_code' => array(0), // названия стран для которых будет действовать скидка  -  array(0 => Россия)
//				'tariff_id' => array(37), // id тарифа в системе eDost: http://edost.ru/kln/help.html#DeliveryCode  -  если указано, тогда параметры 'company_id' и 'format' игнорируются
//				'company_id' => array(5), // id компаний стандарта eDost для которых будет действовать скида (5 - СДЭК)  -  если масив пустой, тогда скидка действует для всех компаний доставки
//				'format' => array('office'), // формат доставки для которого будет действовать скидка ('office' - пункты выдачи)  -  если масив пустой, тогда скидка действует для всех форматов доставки
				'normal' => array( // скидка для указанных местоположений/регионов (если для указанных местоположений скидка не нужна, тогда данный массив необходимо удалить - оставить только 'invert')
//					'price_from' => 0, // стоимость заказа ОТ которой действует скидка
					'price_to' => 5000, // стоимость заказа ДО которой действует скидка
//					'discount_percent' => 20, // процент скидки
//					'discount_fix' => 0, // фиксированная скидка
//					'pricecash_discount_disable' => true, // не применять скидку для доставки с наложенным платежом
					'change' => array( // если указано, заменяет рассчитанную стоимость доставки этим значением (можно указывать оба значения, или только 'price', или только 'pricecash')
//						'price' => 100, // стоимость доставки
						'pricecash' => -1 // стоимость доставки при наложенном платеже ('-1' - наложка отключена, можно использовать для блокировки наложки в интересующих регионах)
					)
				),
//				'invert' => array( // скидка для всех остальных местоположений/регионов (если для остальных местоположений скидка не нужна, тогда данный массив необходимо удалить - оставить только 'normal')
//					'price_from' => 0, // стоимость заказа ОТ которой действует скидка
//					'price_to' => 0, // стоимость заказа ДО которой действует скидка
//					'discount_percent' => 100, // процент скидки
//					'discount_fix' => 350, // фиксированная скидка
//					'pricecash_discount_disable' => true, // не применять скидку для доставки с наложенным платежом
//					'change' => array( // если указано, заменяет рассчитанную стоимость доставки этим значением (можно указывать оба значения, или только 'price', или только 'pricecash')
//						'price' => 250, // стоимость доставки
//						'pricecash' => -1 // стоимость доставки при наложенном платеже ('-1' - наложка отключена, можно использовать для блокировки наложки в интересующих регионах)
//					)
//				),
			),
			// блокировка наложки для всех стран, кроме России
			array(
				'country_code' => array(0), // для работы данной настройки, текущий файл должен быть в кодировке магазина!
				'invert' => array(
					'change' => array(
						'pricecash' => -1 // стоимость доставки при наложенном платеже ('-1' - наложка отключена)
					)
				),
			),
			// бесплатная доставка по России от 10000 руб.
		    array(
		        'country_code' => array(0), // для работы данной настройки, текущий файл должен быть в кодировке магазина!
		        'normal' => array(
		            'price_from' => 10000, // стоимость заказа ОТ которой действует скидка
		            'change' => array(
		                'price' => 0, // стоимость доставки
		            )
		        ),
		    ),
//			array(... еще одна скидка с другими параметрами (скидок может быть сколько угодно)
		);
		if (!empty($result['data'])) {
			foreach ($ar as $param) {
				$a = (!empty($param['region_code']) && in_array($order['location']['region_code'], $param['region_code']) ||
					!empty($param['country_code']) && in_array($order['location']['country_code'], $param['country_code']) ? true : false);

				foreach ($result['data'] as $k => $v)
					if (!empty($param['tariff_id']) && in_array($v['id'], $param['tariff_id']) ||
						empty($param['tariff_id']) && (empty($param['company_id']) || !empty($param['company_id']) && in_array($v['company_id'], $param['company_id'])) && (empty($param['format']) || in_array($v['format'], $param['format']))) {

						$p = $s = $s2 = false;
						if ($a && isset($param['normal'])) $p = $param['normal'];
						if (!$a && isset($param['invert'])) $p = $param['invert'];
						if (empty($p) || isset($p['price_from']) && $order['PRICE'] <= $p['price_from'] || isset($p['price_to']) && $order['PRICE'] > $p['price_to']) continue;
						if (!empty($p['change'])) {
							if (isset($p['change']['price'])) $s = $p['change']['price'];
							if (isset($p['change']['pricecash'])) $s2 = $p['change']['pricecash'];
						}
						else {
							$s = $v['price']*(!empty($p['discount_percent']) ? (100 - $p['discount_percent'])/100 : 1) - (!empty($p['discount_fix']) ? $p['discount_fix'] : 0);
							if ($s < 0) $s = 0;
							if ($v['pricecash'] >= 0 && !isset($p['pricecash_discount_disable'])) {
								$s2 = $v['pricecash']*(!empty($p['discount_percent']) ? (100 - $p['discount_percent'])/100 : 1) - (!empty($p['discount_fix']) ? $p['discount_fix'] : 0);
								if ($s2 < 0) $s2 = 0;
							}
						}
						if ($s !== false) {
							if ($result['data'][$k]['price'] > $s) $result['data'][$k]['priceoriginal']['price'] = $result['data'][$k]['price'];
							$result['data'][$k]['price'] = $s;
						}
						if ($s2 !== false) {
							if ($result['data'][$k]['pricecash'] != -1 && $result['data'][$k]['pricecash'] > $s2) $result['data'][$k]['priceoriginal']['pricecash'] = $result['data'][$k]['pricecash'];
							$result['data'][$k]['pricecash'] = $s2;
						}
					}
			}
		}
*/

/*
		// изменение формата доставки EMS с курьера на почту
		$company_id = array(2); // id компаний которым необходимо изменить формат доставки (EMS)
		$new_format = 'post'; // новый формат доставки (блок "почта")
		if (!empty($result['data'])) foreach ($result['data'] as $k => $v) if (in_array($v['company_id'], $company_id)) $result['data'][$k]['format'] = $new_format;
*/

/*
		// заменить 'дни' на 'рабочие дни'
		if (!empty($result['data'])) foreach ($result['data'] as $k => $v) if (!empty($v['day'])) {
			$result['data'][$k]['day'] = str_replace(array('день', 'дня', 'дней'), array('рабочий день', 'рабочих дня', 'рабочих дней'), $v['day']);
		}
*/

/*
		// исключение стоимости доставки из итого (для почты и EMS)
		$id = array(1, 2, 3, 61, 62, 68); // id тарифов стандарта eDost
		if (!empty($result['data'])) foreach ($result['data'] as $k => $v)
			if (in_array($v['id'], $id) && $v['price'] > 0) {
				$result['data'][$k]['priceinfo'] = $v['price'];
				$result['data'][$k]['price'] = 0;
			}
*/

		// удаление из расчета тарифа "DPD (parcel до пункта выдачи)" (код 91)
//		if (isset($result['data']['91'])) unset($result['data']['91']);

/*
		// 50% скидка на тариф "Курьер 1" (код 61) при заказе в субботу-воскресенье (предупреждение: используется время сервера - оно может отличаться от часового пояса магазина и покупателя)
		if (isset($result['data']['61'])) {
			$result['data']['61']['price_original'] = $result['data']['61']['price'];
			$p = $result['data']['61']['price_original'];
			if (date('N') >= 6) $p = round($p*0.5);
			$result['data']['61']['price'] = $p;
		}
*/

/*
		// изменение стоимости доставки тарифа "PickPoint"
		$id = 57; // PickPoint
		if (isset($result['data'][$id])) {
			// установка фиксированной стоимости доставки для указанных местоположений
			$ar = array('0000073738', '0000103664'); // CODE местоположений
			if (in_array($order['LOCATION_TO'], $ar)) {
				$result['data'][$id]['price'] = 250; // стоимость доставки
				$result['data'][$id]['pricecash'] = 250; // стоимость доставки при наложенном платеже (-1 - отключить наложенный платеж)
			}

			// установка эксклюзивной стоимости для пунктов выдачи с типом 5
			$result['data'][$id]['priceoffice'] = array(
				5 => array(
					'type' => 5,
					'price' => $result['data'][$id]['price'] + 100, // стандартная цена доставки + 100 руб.
					'priceinfo' => 0,
					'pricecash' => 800, // наложка
				),
			);
		}
*/
	}


	// вызывается перед загрузкой данных по пунктам выдачи
	public static function BeforeGetOffice($order, &$company) {
/*
		$order - параметры заказа
		$company - коды eDost компаний доставки для которых требуется загрузить данные
*/
//		echo '<br><b>BeforeGetOffice - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>BeforeGetOffice - company:</b> <pre style="font-size: 12px">'.print_r($company, true).'</pre>';

	}


	// вызывается после загрузки данных по пунктам выдачи
	public static function AfterGetOffice($order, &$result) {
/*
		$order - параметры заказа
		$result - пункты выдачи
*/
//		echo '<br><b>AfterGetOffice - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>AfterGetOffice - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';
//		echo '<br><b>AfterGetOffice - result:</b> <pre style="font-size: 12px">'.print_r(edost_class::$result, true).'</pre>';

/*
		// сортировка пунктов выдачи по алфавиту
		if (!empty($result['data'])) foreach ($result['data'] as $k => $v) {
			$ar = array();
			foreach ($v as $v2) $ar[] = trim(str_replace(array('ул.', 'проспект', 'переулок', 'площадь'), '', $v2['address']));
			array_multisort($ar, SORT_ASC, SORT_STRING, $v);
			$ar = array();
			foreach ($v as $v2) $ar[$v2['id']] = $v2;
			$result['data'][$k] = $ar;
		}
*/

/*
		// удаление пунктов выдачи компании ПЭК и Деловые линии (останутся только терминалы)
		if (!empty($result['data'])) foreach ($result['data'] as $k => $v) if (in_array($k, array(19, 21))) foreach ($v as $k2 => $v2) if ($v2['type'] != 2) {
			unset($result['data'][$k][$k2]);
		}
*/

/*
		// отключение у адреса вывод ссылки "подробнее..." (отключение/замену необходимо дублировать в функции "AfterGetOrderOffice" !!!)
		$id = 5; // код одного из тарифов "СДЭК"
		if (!empty($result['data'][$id])) foreach ($result['data'][$id] as $k => $v) $result['data'][$id][$k]['detailed'] = 'N'; // или можно заменить на свою ссылку: 'http://myshop.ru/delivery.html?id=%id%' (%id% будет заменен id офиса) - если в запрос добавить '&frame=Y', тогда страница будет открываться в окошке (без новой вкладки)
*/

/*
		// перенос офисов СДЭК в тариф "Самовывоз 1"
		$from = 5; // код "СДЭК"
		$to = 's1'; // код "Самовывоз 1"
		if (!empty($result['data'][$from])) {
			$result['data'][$to] = $result['data'][$from];
			unset($result['data'][$from]);
			if (!empty($result['limit'])) foreach ($result['limit'] as $k => $v) if ($v['company_id'] == $from) $result['limit'][$k]['company_id'] = $to;
		}
*/

		// удаление пункта выдачи тарифа 'Самовывоз 1' (код 's1')
//		if (isset($result['data']['s1'])) unset($result['data']['s1']);


		// генерация собственного пункта выдачи для тарифа 'Самовывоз 1' (код 's1')
/*
		Требования при генерации полностью нового пункта выдачи (которого нет в системе eDost):
		1. Присвоить уникальный id (например, 1000000, 1000001, 1000002, и т.д.)
		2. Указать в параметрах один из вариантов:
			'detailed' => 'http://myshop.ru/delivery.html?id=%id%', // прописать собственную ссылку на детальную информацию (если в запрос добавить '&frame=Y', тогда страница будет открываться в окошке)
			или
			'detailed' => 'N', // отключить ссылку на детальную информацию
		3. При использовании собственной ссылки с детальной информацией, ее также необходимо генерировать в функции "AfterGetOrderOffice"
*/
/*
		$result['data']['s1'] = array(
			'12345A12345' => array( // ключ и id должны совпадать
				'id' => '12345A12345', // id с буквой "A" – это оригинальный идентификатор пункта выдачи созданного в личном кабинете eDost, и по этому идентификатору можно открыть его страницу с детальной информацией на сайте edost.ru (там адрес, описание, карта и т.д.).
				'code' => '',
				'name' => 'ТЦ Калач',
				'address' => 'Москва, ул. Академика Янгеля, д. 6, корп. 1',
				'address2' => 'оф. 5',
				'tel' => '+7-123-123-45-67',
				'schedule' => 'с 10 до 20, без выходных',
				'gps' => '37.592311,55.596037',
				'type' => 3,
				'metro' => '',
//				'detailed' => 'http://myshop.ru/delivery.html?id=%id%', // прописать собственную ссылку на детальную информацию
//				'detailed' => 'N', // отключить ссылку на детальную информацию
			),
		);
*/

/*
		// генерация пункта выдачи для тарифа 'СДЭК'
		$result['data']['5'][100000] = array(
				'id' => '100000',
				'code' => '', // если код не указан, тогда при сохранении выбранного в заказе пункта выдачи в адресе в качесте кода будет записана буква 'S' (для тарифов Самовывоз) или 'T' (для всех остальных тарифов)
				'name' => 'ТЦ Калач',
				'address' => 'Москва, ул. Академика Янгеля, д. 6, корп. 1',
				'address2' => 'оф. 5',
				'tel' => '+7-123-123-45-67',
				'schedule' => 'с 10 до 20, без выходных',
				'gps' => '37.592311,55.596037',
				'type' => 3,
				'metro' => '',
//				'detailed' => 'http://myshop.ru/delivery.html?id=%id%', // прописать собственную ссылку на детальную информацию
				'detailed' => 'N', // отключить ссылку на детальную информацию
		);
*/
	}


	// проверка IP на вхождение в диапазон
	public static function IPRange($ip, $from, $to) {
		$ip = sprintf('%u', ip2long($ip));
		$from = sprintf('%u', ip2long($from));
		$to = sprintf('%u', ip2long($to));
		return ($ip >= $from && $ip <= $to ? true : false);
	}

}
?>