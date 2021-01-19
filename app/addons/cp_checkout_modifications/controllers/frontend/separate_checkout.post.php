<?php

use Tygh\Registry;
use Tygh\Tygh;
use Tygh\Enum\ProfileFieldSections;

defined('BOOTSTRAP') or die('Access denied');
$cart = &Tygh::$app['session']['cart'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   return;
}
if ($mode == 'cart') {
   unset(Tygh::$app['session']['is_place_all_orders']);
   unset(Tygh::$app['session']['cp_completed_orders']);
}