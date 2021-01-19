<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

if (!defined('BOOTSTRAP')) { die('Access denied'); }
use Tygh\Registry;
if(!empty($_REQUEST['is_ajax'])&&!empty($_REQUEST['ajax_pagination'])&&$_REQUEST['ajax_pagination']=="Y") {
	  Registry::get('view')->assign('no_sorting', true);
}

if(Registry::get('addons.ab__seo_filters.status')=='A') {
    if(!empty($_REQUEST['ajax_pagination'])) {
        unset($_REQUEST['page']);
    }
}
