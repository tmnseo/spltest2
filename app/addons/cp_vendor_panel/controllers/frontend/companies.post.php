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

use Tygh\Storage;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'products') {
    
    if (!empty($_REQUEST['company_id'])) {
        $certificates = fn_get_attachments('vendor_cert', $_REQUEST['company_id'], 'M', DESCR_SL);     
        if (!empty($certificates)) {
            foreach($certificates as &$cert) {
                if (!empty($cert['filename']) && $cert['object_type'] == 'vendor_cert') {
                    $file_path = 'vendor_cert/' . $_REQUEST['company_id'] . '/' . $cert['filename'];
                    if (Storage::instance('images')->isExist($file_path)) {
                        if (defined('HTTPS')) {
                            $cert['cert_file_path'] = Storage::instance('images')->getUrl($file_path, 'https');
                        } else {
                            $cert['cert_file_path'] = Storage::instance('images')->getUrl($file_path, 'http');
                        }
                    }
                }
            }
        }
        $vend_warranties = fn_cp_vp_get_vendor_warranties($_REQUEST['company_id'], array('items_per_page' => 0), DESCR_SL);
        $additional_vendor_data = array(
            'certificates' => $certificates,
            'warranties' => $vend_warranties
        );
        Tygh::$app['view']->assign('cp_vp_additional_data', $additional_vendor_data);
    }
}