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

/*HOOKS*/
/*HOOKS*/
function fn_settings_variants_addons_cp_order_pretensions_order_status()
{
    $data = array(
        '' => ' -- '
    );

    foreach (fn_get_statuses(STATUSES_ORDER) as $status) {
        $data[$status['status']] = $status['description'];
    }

    return $data;
}
function fn_cp_create_zoho_request($params)
{   
    $auth = Tygh::$app['session']['auth'];
    
    if (!empty($auth['user_id'])) {
        $user_info = fn_get_user_info($auth['user_id']);
        $email = !empty($user_info['email']) ? $user_info['email'] : null;
        $phone = !empty($user_info['phone']) ? $user_info['phone'] : null;
        $firstname = !empty($user_info['firstname']) ? $user_info['firstname'] : null;
        $lastname = !empty($user_info['lastname']) ? $user_info['lastname'] : null;
        $pretension_desctiption = !empty($params['cp_data']['pretension_description']) ? $params['cp_data']['pretension_description'] : null;
        $uploads = array();

        $files_data = fn_filter_uploaded_data('fb_files'); 
        
        $Zoho = new Zoho();
        $contact = $Zoho->createContact($firstname." ".$lastname, $email, $phone);

        if (!isset($contact->id)) {
            $result = false;
        }
        if (!empty($files_data)) {
            foreach($files_data as $file) {
                /*nm method*/
                $folderPath = $_SERVER['DOCUMENT_ROOT'] . '/var/cache/misc/tmp/';
                $isRenamed = rename($file['path'], $folderPath . $file['name']);
                $pathToWork = $isRenamed ? $folderPath . $file['name'] : $file['path'];
                
                $uploads[] = $Zoho->uploadFile($pathToWork);
                unlink($pathToWork);
                /*nm method*/
            }
        }

        $result = $Zoho->newReclamation(
            $contact->id,
            $pretension_desctiption,
            $phone,
            $email,
            $uploads
        );
    }
    
}