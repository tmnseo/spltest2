<?php


use Tygh\Registry;
use Tygh\Commerceml\RusEximCommerceml;
use Tygh\Cpcrm\CrmHandler;
use \Tygh\Database\Connection;


require_once(Registry::get('config.dir.addons'). 'rus_exim_1c/Tygh/Commerceml/Logs.php');


if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (!empty($_SERVER['PHP_AUTH_USER'])) {
    $data['user_login'] = $_SERVER['PHP_AUTH_USER'];
    list($status, $user_data, $user_login, $password, $salt) = fn_auth_routines($data, array());
    fn_cp_commerceml_change_company_store($user_data);
}

$params = $_REQUEST;
$type = $mode = $service_exchange = '';
if (isset($params['type'])) {
    $type = $params['type'];
}

if (isset($params['mode'])) {
    $mode = $params['mode'];
}

if (isset($params['service_exchange'])) {
    $service_exchange = $params['service_exchange'];
}

$manual = !empty($params['manual']);

$path_file = 'exim/CPCRM_' . date('dmY') . '/';
$path = fn_get_files_dir_path() . $path_file;
$path_commerceml = fn_get_files_dir_path();

$log = new \Tygh\Commerceml\Logs($path_file, $path);

$exim_commerceml = new CrmHandler(Tygh::$app['db'], $log, $path_commerceml);
$exim_commerceml->import_params['service_exchange'] = $service_exchange;
$exim_commerceml->import_params['manual'] = $manual;


if ($exim_commerceml->checkParameterFileUpload()) {
    exit;
}

$filename = (!empty($params['filename'])) ? fn_basename($params['filename']) : '';
$lang_code = (!empty($s_commerceml['exim_1c_lang'])) ? $s_commerceml['exim_1c_lang'] : CART_LANGUAGE;

$exim_commerceml->getDirCommerceML();
$exim_commerceml->import_params['lang_code'] = $lang_code;


define('FILE_LIMIT_CP', 1024 * 1024 * 50);


if ($type == 'import') {
    if ($mode == 'checkauth') {
        $exim_commerceml->exportDataCheckauth($service_exchange);

    } elseif ($mode == 'init') {
        $exim_commerceml->exportDataInit();

    } elseif ($mode == 'file') {
        if ($exim_commerceml->createImportFile($filename) === false) {
            fn_echo("failure");
            exit;
        }
        fn_echo("success\n");

    } elseif ($mode == 'import') {
        $fileinfo = pathinfo($filename);

        list($json_data, $d_status, $text_message) = $exim_commerceml->getFileCommerceml($filename);

        $exim_commerceml->addMessageLog($text_message);
        if ($d_status === false) {
            fn_echo("failure");
            exit;
        }
        $answer = $exim_commerceml->importPaymentsFile($json_data);

        $answer = json_encode($answer);

        echo $answer;
        
    }
} 
exit;
