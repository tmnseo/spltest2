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

namespace Tygh\Addons\CpAdvancedImport\Readers;

use Tygh\Common\OperationResult;
use Tygh\Addons\AdvancedImport\Readers\IReader;

require "app/addons/price_list/lib/phpexcel/Classes/PHPExcel.php";

class Xlsx implements IReader
{
    /** @var string $path */
    protected $path;

    /** @var array $options */
    protected $options = array();

    /** @var array $schema */
    protected $schema = array();

    /** @inheritdoc */
    public function __construct($path, array $options = array())
    {
        $this->path = $path;
        $this->options = $options;
    }

    public function getSchema()
    {
        $result = new OperationResult(false, array());
        
        if (!$this->schema) {
                
            $file_type = \PHPExcel_IOFactory::identify($this->path);
            
            $objReader = \PHPExcel_IOFactory::createReader($file_type);
            $objPHPExcel = $objReader->load($this->path);

            $_data = $this->deleteEmptyElements($objPHPExcel->getActiveSheet()->toArray());
            
            $this->schema = array_shift($_data);

        } else {
            $result->setSuccess(true);
        }

        $result->setData($this->normalizeSchema($this->schema));
        
        return $result;
    }
    public function getContents($count = null, array $schema = null)
    {   
        //fn_print_die($schema);
        $result = new OperationResult(false, array());
        $result->setData(array());

        if ($schema === null) {
            $schema = $this->getSchema()->getData();
        }

        $file_type = \PHPExcel_IOFactory::identify($this->path);
            
        $objReader = \PHPExcel_IOFactory::createReader($file_type);
        $objPHPExcel = $objReader->load($this->path);

        $_data = $this->deleteEmptyElements($objPHPExcel->getActiveSheet()->toArray());
        if (!empty($_data)) {

            array_shift($_data);

            foreach ($_data as $key => $data) {
                foreach ($data as $field_id => $field) {
                    $contents[$key][$this->normalizeSchema($this->schema)[$field_id]] = $field;
                }
                
                $result->setData($contents);
                if (!empty($count) && ($key + 1) >= $count) {
                    break;    
                }
            }
        }

        if (!$result->getData()) {
            $result->setErrors(array(
                __('advanced_import.incorrect_delimiter'),
            ));
        }

        return $result;
    }
    public function getApproximateLinesCount()
    {

    }
    public function getExtension()
    {
        return 'xlsx';
    }
    protected function normalizeSchema(array $schema)
    {
        foreach ($schema as $field_position => &$field_name) {
            $field_name = trim(str_replace('  ', ' ', $field_name));
            $field_name = mb_strtoupper($field_name);
        }
        
        return $schema;
    }
    protected function deleteEmptyElements(array $data)
    {   
        $tmp_data = $data;
        $count_keys = count(array_diff(array_shift($tmp_data),array(''))); 
        
        if (!empty($data)){
            foreach ($data as $key => &$row) {
                if (empty(current($row))){
                    unset($data[$key]);
                }else{
                    $row = array_slice($row, 0, $count_keys);
                }  
            }
        }

        return $data;
    }

}