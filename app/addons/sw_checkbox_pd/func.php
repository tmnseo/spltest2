<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }



function fn_sw_checkbox_pd_update_page_post(&$page_data, $page_id)
{
 

if (isset($page_data['check_form_pd']) == false){
    $page_data['check_form_pd'] = 'N';
} 


return;

}