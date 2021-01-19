<div class="btn-group" id="location_status_{$loc_id}">
    {btn type="text"
        id="premoderation_approve_{$loc_id}"
        title=__("cp_warehouses_premoderation.approve_warehouse", ["[warehouse]" => {$loc_name}])
        href="cp_warehouses_premoderation.approve?location_id={$loc_id}&return_url={$current_url}"
        icon="icon-thumbs-up"
        class="btn"
        method="POST"
    }

    {btn type="dialog"
        id="premoderation_disapprove_{$product.product_id}"
        title=__("cp_warehouses_premoderation.disapprove_warehouse", ["[warehouse]" => {$loc_name}])
        href="cp_warehouses_premoderation.decline?location_id={$loc_id}&return_url={$current_url}"
        icon="icon-thumbs-down"
        class="btn"
        target_id="disapproval_reason_{$loc_id}"
    }
<!--location_status_{$loc_id}--></div>