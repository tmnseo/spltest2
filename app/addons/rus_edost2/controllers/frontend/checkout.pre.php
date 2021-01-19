<?php
if (!defined('BOOTSTRAP')) die('Access denied');

$cart = &Tygh::$app['session']['cart'];
if (fn_rus_edost2_set_cart_param($cart, $mode)) return array(CONTROLLER_STATUS_OK);
