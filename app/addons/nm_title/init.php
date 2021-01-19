<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

fn_register_hooks(
    'send_form',
    'cp_crm_process_payments'
);

include(Registry::get('config.dir.addons') . "/nm_title/Zoho/ZohoApi.php");
include(Registry::get('config.dir.addons') . "/nm_title/Zoho/Zoho.php");
include(Registry::get('config.dir.addons') . "/nm_title/Zoho/ZohoAuthSettings.php");
include(Registry::get('config.dir.addons') . "/nm_title/Zoho/ZohoOAuth.php");