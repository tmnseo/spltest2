<?php


namespace Tygh\Addons\CpWarehouses;
use Tygh\Addons\Warehouses\ProductStock;
use Tygh\Addons\CpWarehouses\ProductWarehouse;

class CpProductStock extends ProductStock
{

    public function __construct($product_id, array $warehouses_amounts)
    {
        $this->product_id = (int) $product_id;
        $this->cpInitializeAmounts($warehouses_amounts);
    }

    public function getWarehousesForPickupInDestination($destination_id)
    {
        $warehouses = array_filter($this->getActiveWarehouses(), function ($warehouse) use ($destination_id) {
            /** @var \Tygh\Addons\Warehouses\ProductWarehouse $warehouse */
            //return $warehouse->isAvailForPickupInDestination($destination_id);
            return true;
        });

        $warehouses = $this->sortByDestinationPosition($warehouses, $destination_id);

        return array_values($warehouses);
    }

    private function cpInitializeAmounts(array $warehouses_amounts)
    {
        foreach ($warehouses_amounts as $warehouse) {
            $warehouse_data = [
                'amount'                   => $warehouse['amount'],
                'position'                 => $warehouse['position'],
                'product_id'               => $this->getProductId(),
                'store_type'               => $warehouse['store_type'],
                'warehouse_id'             => $warehouse['warehouse_id'],
                'main_destination_id'      => $warehouse['main_destination_id'],
                'pickup_destination_ids'   => $warehouse['pickup_destinations_ids'],
                'shipping_destination_ids' => $warehouse['shipping_destinations_ids'],
                'destinations'             => $this->initializeDestinations($warehouse['destinations']),
                'status'                   => $warehouse['status'],
            ];
            $this->product_warehouses[] = new CpProductWarehouse($warehouse_data);
        }

        return $this;
    }

    protected function getActiveWarehouses()
    {   
        return array_filter($this->product_warehouses, function(CpProductWarehouse $warehouse) {    
            return $warehouse->isActive();
        });
    }
}
