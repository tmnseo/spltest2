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

namespace Tygh\Addons\CpManageOldProducts\Products;

/**
 * 
 */
class ExportProductManager 
{
    private $company_id = 0;
    private $not_exported_ids = [];

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function getNotExportedProductIds()
    {
        $this->not_exported_ids = db_get_fields("SELECT product_id FROM ?:products WHERE company_id = ?i AND cp_was_imported = ?s", $this->company_id, 'N');
    }

    public function setZeroInventoryForNotExportedProducts()
    {   

        if (!empty($this->not_exported_ids)) {
            foreach ($this->not_exported_ids as $_pid) {
                $this->setZeroInventory($_pid);
            }
        }
    }

    private function setZeroInventory($product_id)
    {
        $result = db_query("UPDATE ?:warehouses_products_amount SET amount = 0 WHERE product_id = ?i AND amount <> 0", $product_id);
    }
}