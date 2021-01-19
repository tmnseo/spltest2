<?php
namespace Tygh\Cpcrm;

use Tygh\Registry;
use Tygh\Commerceml\Logs;
use Tygh\Cpcrm\CrmPayments;
use Tygh\Database\Connection;
use Tygh\Bootstrap;

Class CrmHandler {
    
    public $user_data = array();
    public $import_params = array();
    public $currencies = array();
    public $company_id=0;
    

    public function __construct(Connection $db, Logs $log, $path_commerceml)
    {
        $this->db = $db;
        $this->log = $log;
        $this->path_commerceml = $path_commerceml;
        $this->path_file = 'exim/CPCRM_' . date('dmY') . '/';
        $this->currencies = Registry::get('currencies');
    }


    public function addMessageLog($message)
    {
        $this->log->write("Data : " . date("d-m-Y h:i:s") . " - " . $message);
    }

    public function showMessageError($message)
    {
        $this->addMessageLog($message);

        if (empty($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Authorization required"');
            header('HTTP/1.0 401 Unauthorized');
        }
        fn_echo($message);
    }
    
    public function checkParameterFileUpload()
    {
        $message = "";
        $log_message = "";

      
        if (!empty($_SERVER['PHP_AUTH_USER'])) {
            $_data['user_login'] = $_SERVER['PHP_AUTH_USER'];

            list($status, $user_data, $user_login, $password, $salt) = fn_auth_routines($_data, array());

            $this->import_params['user_data'] = $user_data;

            if ($user_login != $_SERVER['PHP_AUTH_USER'] || empty($user_data['password']) || $user_data['password'] != fn_generate_salted_password($_SERVER['PHP_AUTH_PW'], $salt)) {
                $message = "\n Error in login or password user";
            }



            $log_message = $this->getCompanyStore($user_data);

        } else {
            $message = "\n Enter login and password user";
        }

        if (!empty($message) || !empty($log_message)) {
            $this->showMessageError($message);
            $this->addMessageLog($log_message);

            return true;
        }

        return false;
    }


    public function getCompanyStore($user_data)
    {
        $log_message = "";
        if (PRODUCT_EDITION == 'ULTIMATE') {
            if (Registry::get('runtime.simple_ultimate')) {
                $this->company_id = Registry::get('runtime.forced_company_id');
                $this->has_stores = false;
            } else {
                if ($user_data['company_id'] == 0) {
                    $log_message = "For import used store administrator";
                    fn_echo('SHOP IS NOT SIMPLE');
                } else {
                    $this->company_id = $user_data['company_id'];
                    Registry::set('runtime.company_id', $this->company_id);
                }
            }

        } elseif ($user_data['user_type'] == 'V') {
            if ($user_data['company_id'] == 0) {
                $log_message = "For import used store administrator";
                fn_echo('SHOP IS NOT SIMPLE');
            } else {
                $this->company_id = $user_data['company_id'];
                Registry::set('runtime.company_id', $this->company_id);
            }

        } else {
            Registry::set('runtime.company_id', $this->company_id);
        }

        return $log_message;
    }


    public function exportDataCheckauth($service_exchange)
    {
        $this->addMessageLog("Send data checkauth: " . \Tygh::$app['session']->getName());

        fn_echo("success\n");

        if (!empty($service_exchange) && $service_exchange != 'exim_cml') {
            fn_echo($service_exchange . "\n");
        } else {
            fn_echo(\Tygh::$app['session']->getName() . "\n");
        }

        fn_echo(\Tygh::$app['session']->getID());

        return "success";
    }


    public function exportDataInit()
    {
        $upload_max_filesize = Bootstrap::getIniParam('upload_max_filesize', true);
        $post_max_size = Bootstrap::getIniParam('post_max_size', true);

        $file_limit = min(
            FILE_LIMIT_CP,
            fn_return_bytes($upload_max_filesize),
            fn_return_bytes($post_max_size)
        );

        $this->addMessageLog("Send file limit: " . $file_limit);

        $data_init = "zip=no";
        fn_echo("zip=no\n");
        fn_echo("file_limit=" . $file_limit . "\n");

        return $data_init;
    }

    /**
     * Generates URLs for the files and images uploaded from accounting systems.
     */
    public function getDirCommerceML()
    {
        $data_path = $this->path_file;

        $this->path_commerceml = fn_get_files_dir_path() . $data_path;
        $this->url_commerceml = Registry::get('config.http_location') . '/' . fn_get_rel_dir($this->path_commerceml);

        

        return array($this->path_commerceml, $this->url_commerceml);
    }


    public function checkFileDescription($filename)
    {
        $file_array = fn_explode('.', $filename);
        if (is_array($file_array)) {
            $type = mb_strtolower(array_pop($file_array));
            if (in_array($type, array('txt', 'json'))) {
                return true;
            }
        }

        return false;
    }

    public function createImportFile($filename)
    {
        $this->addMessageLog("Loadding data file " . $filename);

        $file_mode = 'a';
        list($path_commerceml, $url_commerceml) = $this->getDirCommerceML();

        if (!is_dir($path_commerceml)) {
            fn_mkdir($path_commerceml);
            @chmod($path_commerceml, 0777);
        }
        $file_path = $path_commerceml . $filename;


        $export_data = file_get_contents('php://input');
        //if ((!$xml_validate) || empty($export_data)) {
            //$file_mode = 'a';
       // }

        if ($this->checkFileDescription($filename)) {
            $file_mode = 'w';
        }

        $file = @fopen($file_path, $file_mode);
        if (!$file) {
            $this->addMessageLog("File " . $filename . " can not create");
            return false;
        }
        fwrite($file, $export_data);
        fclose($file);
        @chmod($file_path, 0777);

        return true;
    }


    public function getFileCommerceml($filename)
    {
        $text_message = "Parsing file data " . $filename;

        $json_data = @file_get_contents($this->path_commerceml . $filename);

        if ($json_data === false) {
            $text_message .= "\n Can not read file " . $filename;

            return array('', false, $text_message);
        }

        return array($json_data, true, $text_message);
    }


    public function importPaymentsFile($import_data)
    {
        $this->addMessageLog("Started import date  from payment file, parameter service_exchange = '" . $this->import_params['service_exchange'] . "'");

        $CrmPayments = CrmPayments::getInstance();

        $import_file = true;

        if (!isset(\Tygh::$app['session']['cp_crm'])) {
            \Tygh::$app['session']['cp_crm'] = array();
            \Tygh::$app['session']['cp_crm']['f_count_imports'] = 0;
        }

        $progress = false;

        $import_data = json_decode($import_data,true);

        if(empty($import_data)){

            $this->addMessageLog("Can't read data beacuse json_decode return empty");

            return false;
        }

        
        $answer_orders = array();

        foreach ($import_data as $data){

            if (!empty($data['amount_total'])) {
                $data['amount_total'] = str_replace(',', '.', $data['amount_total']);
            }
            if (!empty($data['amount_vat'])) {
                $data['amount_vat'] = str_replace(',', '.', $data['amount_vat']);
            }
            
            if(!$CrmPayments->validate($data)){
                continue;
            }

            if($CrmPayments->update()){

                $order_id = $CrmPayments->processPaymentData();
                $errors = $CrmPayments->getValidateErrors();

                $status_answer='';

                if(!empty($errors)){
                    $answer_orders[$data['payment_order_number']] = $status_answer=  '500';

                    $CrmPayments->status ="D";

                    $CrmPayments->update();

                }
                else{
                    $answer_orders[$data['payment_order_number']] =  $status_answer = '200';
                    if($order_id){

                        $CrmPayments->updateOrder($order_id);
                    }
                }

                fn_set_hook('cp_crm_process_payments', $order_id, $data,$status_answer);


            }
            else{
                
            }
        }
        
        return $answer_orders;
    }



    
}
