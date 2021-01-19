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

namespace  Tygh\Cpcrm;
use Tygh\Languages\Languages;



/**
 * @ignore
 */
class CrmPayments
{

    private static $_instance = null;
    protected $table_name = '?:cp_crm_payments';
    protected $payment_data = array();
    protected $fields = array();
    protected $current_payment_id =0;
    protected $validator_rules = array();

    private $validate_errors = array();

    protected $paid_after_cancel_order_status ="L";
    protected $paid_order_status ="P";
    protected $approved_order_status ="J";
    protected $cancel_order_status ="M";


    public function getPaidCancelOrderStatus(){
        return $this->paid_after_cancel_order_status;
    }

    public function getPaidOrderStatus(){
        return $this->paid_order_status;
    }


    public function getApprovedOrderStatus(){
        return $this->approved_order_status;
    }

    public function getCancelOrderStatus(){
        return $this->cancel_order_status;
    }




    public function processOrderStatus($current_status,$order_id){

        $approvedOrderStatus = $this->getApprovedOrderStatus();
        $CancelOrderStatus = $this->getCancelOrderStatus();


        $paid_cancel_status = $this->getPaidCancelOrderStatus();
        $cpaid_status = $this->getPaidOrderStatus();

        $avail_statuses = array($approvedOrderStatus,$CancelOrderStatus);

        if(in_array($current_status,$avail_statuses)){

            $change_status = false;

            if($current_status == $approvedOrderStatus){
                $change_status = $cpaid_status;
            }

            if($current_status == $CancelOrderStatus){
                $change_status = $paid_cancel_status;
            }

            if($change_status){
                fn_change_order_status($order_id,$change_status);
            }
        }


    }


    private function __construct()
    {
        $fields = db_get_fields("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$this->table_name."'");
        $fields = array_flip($fields);
        $this->fields = $fields;
    }

    public function setValidateError($name,$value){
        $this->validate_errors[$name] = $value;
    }


    public function getValidateErrors(){
        $validate_errors = $this->validate_errors;
        return $validate_errors;
    }

    public function setupValidatorsRules($validator_rules){
        $this->validator_rules = $validator_rules;
    }

    protected function __clone()
    {

    }

    public function getTablename()
    {
        return $this->table_name;
    }

    public function __set($name, $value)
    {
        $this->payment_data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->fields)) {
            return $this->payment_data[$name];
        }
    }

    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function validate($payment_data)
    {


        $this->current_payment_id =0;
        $this->validate_errors = array();

        if (!is_array($payment_data)) {
            return false;
        }


        if (!isset($payment_data['payment_order_date']) or !isset($payment_data['payment_order_number'])
            or !isset($payment_data['bank_account']) or !isset($payment_data['bank_bic'])
            or !isset($payment_data['payment_purpose']) or !isset($payment_data['customer_tin'])
            or !isset($payment_data['customer_name']) or !isset($payment_data['currency_id'])
            or !isset($payment_data['currency_name']) or !isset($payment_data['amount_total']) or !isset($payment_data['amount_vat'])
        ){
            return false;
        }


            $this->payment_data = $payment_data;
            $this->payment_order_date_timestamp = strtotime($payment_data['payment_order_date']);
            return true;

    }

    public static function getOrderNumberFrompuprose($value_string){
        preg_match('/\№*.*?([0-9]+)/i', $value_string, $result);


       if(isset($result[1]) && db_get_field("SELECT order_id FROM ?:orders WHERE order_id =?i",$result[1])){
           return $result[1];
       }
        else{
            return false;
        }
    }

    public  function getOrderTotalByPayment($check_number){

        $payment_data = $this->payment_data;

        $currency = $payment_data['currency_name'];
        $total =  $payment_data['amount_total'];

        if($currency!='руб.'){
            $this->setValidateError('currency',false);
            return false;
        }
        
        return $total;
    }

    public function processPaymentData(){
        $payment_data = $this->payment_data;

        if(empty($payment_data)){
            return false;
        }

        if(!empty($payment_data['payment_purpose'])){
            $check_number = self::getOrderNumberFrompuprose($payment_data['payment_purpose']);
            if(!$check_number){
                $this->setValidateError('order_id',false);
            }
        }

        $order_total = $this->getOrderTotalByPayment($check_number);
        if($check_number){
            $total = db_get_field("SELECT total FROM ?:orders WHERE order_id =?i",$check_number);
        }
        else{
            $total =-1;
        }

        $order_total  = str_replace(",",".",$order_total);

        $order_total = floatval($order_total);

        $total = floatval($total);
        
        if($order_total!=$total){
            $this->setValidateError('compare_totals',false);
        }

        return $check_number;

    }

    public function updateOrder($order_id){
        $payment_data = $this->payment_data;
        
        db_query("UPDATE ?:orders SET cp_payment_order_number =?s,cp_payment_order_date =?i,cp_payment_amount =?i WHERE order_id =?i",$payment_data['payment_order_number'],$payment_data['payment_order_date_timestamp'],$payment_data['amount_total'],$order_id);

        $current_status = db_get_field("SELECT status  FROM ?:orders WHERE order_id =?i",$order_id);
        $this->processOrderStatus($current_status,$order_id);


        //fn_change_order_status($order_id,"P");
 
    }

    public function delete($payment_id){
        db_query("DELETE FROM ".$this->table_name ." WHERE payment_id =?i",$payment_id);
    }

    public function get($payment_id){
        return db_get_row("SELECT * FROM ".$this->table_name." WHERE payment_id =?i",$payment_id);
    }

    public function checkRecorcdByField($field,$value,$predicate){

        if(!isset($this->fields[$field])){

            exit('WRONG TABLE FIELD');
        }

        return db_get_field("SELECT payment_id FROM $this->table_name WHERE $field = $predicate",$value);
    }


    public function update($payment_data=array(), $payment_id = 0)
    {

        $payment_id = $this->current_payment_id;
        
        if (!empty($this->payment_data)) {
            $payment_data = array_merge($payment_data, $this->payment_data);
        }

        if(empty($payment_id)) {
            $payment_id = $this->checkRecorcdByField("payment_order_number", $payment_data['payment_order_number'], "?i");
        }


       

        if (empty($payment_id)) {
            $payment_data['time'] = time();
            $payment_data['payment_id']  = db_query("REPLACE INTO ".$this->table_name." ?e", $payment_data);

        } else {
            db_query("UPDATE ".$this->table_name." SET ?u WHERE payment_id = ?i", $payment_data,$payment_id);
        }
        $this->current_payment_id = $payment_id;
        
        return $payment_id;
    }



    /*
     *   `payment_id` int(11) unsigned NOT NULL auto_increment,
            `time` int(11) unsigned NOT NULL default 0,
            `store_order_id` int(11) unsigned NOT NULL default 0,
            `payment_order_date` varchar(255) NOT NULL default '',
            `payment_order_number` int(11) NOT NULL default '0',
             `payment_order_date_timestamp` unsigned NOT NULL default 0,

            `bank_account` varchar(32) NOT NULL default '',
            `bank_bic` varchar(32) NOT NULL default '',
            `payment_purpose` TEXT NOT NULL default '',
            `customer_tin` varchar(32) NOT NULL default '',
            `customer_name` varchar(255) NOT NULL default '',
            `currency_id` char(5) NOT NULL default '',
            `currency_name` char(5) NOT NULL default '',
            `amount_total` decimal(12,2) NOT NULL default '0.00',
            `amount_vat` decimal(12,2) NOT NULL default '0.00',
            `status` char(1) NOT NULL default 'A',
     */

    public function getPayments($params,$items_per_page,$lang_code){

        $default_params = [
            'page'           => 1,
            'items_per_page' => $items_per_page,
        ];

        $params = array_merge($default_params, $params);

        // $fields = ['a.city_id', 'a.status', 'a.added_time', 'b.city_name'];
        $fields = ['a.*'];


        $condition = 'WHERE 1=1';

        if (!empty($params['status'])) {
            $condition .= db_quote(' AND a.status = ?s', $params['status']);
        }

        if (!empty($params['store_order_id'])) {
            $condition .= db_quote(' AND a.store_order_id = ?i', $params['store_order_id']);
        }

        if (!empty($params['payment_order_number'])) {
            $condition .= db_quote(' AND a.payment_order_number = ?i', $params['payment_order_number']);
        }

        $sorting = 'ORDER BY  a.time DESC';
        $limit = $group = '';
        if (!empty($params['items_per_page'])) {
            $params['total_items'] = db_get_field('SELECT count(*) FROM '.$this->table_name.' as a ?p', $condition);
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        }

        /**
         * Prepare params for getting states SQL query
         *
         * @param array  $params         Params list
         * @param int    $items_per_page States per page
         * @param string $lang_code      Language code
         * @param array  $fields         Fields list
         * @param array  $joins          Joins list
         * @param string $condition      Conditions query
         * @param string $group          Group condition
         * @param string $sorting        Sorting condition
         * @param string $limit          Limit condition
         */

        $payments = db_get_array(
            'SELECT ' . implode(', ', $fields) . ' FROM '.$this->table_name.' as a ?p ?p ?p ?p',
             $condition, $group, $sorting, $limit
        );

        /**
         * Actions after states list was prepared
         *
         * @param array  $params         Params list
         * @param int    $items_per_page States per page
         * @param string $lang_code      Language code
         * @param array  $states         List of selected states
         */
        return array($payments, $params);

    }
}