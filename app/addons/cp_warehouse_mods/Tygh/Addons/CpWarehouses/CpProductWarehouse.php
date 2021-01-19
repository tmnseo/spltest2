<?php


namespace Tygh\Addons\CpWarehouses;
use Tygh\Enum\ObjectStatuses;
use Tygh\Addons\Warehouses\ProductWarehouse;

class CpProductWarehouse extends ProductWarehouse
{
    public function isActive() 
    {   
        return fn_cp_check_warehouse_status($this->warehouse_id) === true;
    }
}