<div class="control-group">
    <label class="control-label">{__("cp_is_door_delivery")}:</label>
    <div class="controls">
        <input type="hidden"
               name="shipping_data[cp_is_door_delivery]"
               value="N"
        />
        <input type="checkbox"
               name="shipping_data[cp_is_door_delivery]"
               id="cp_is_door_delivery"
               {if $shipping.cp_is_door_delivery|default:"N" == "Y"}checked="checked"{/if}
               value="Y"
        />
    </div>
</div>
<div class="control-group">
    <label class="control-label">{__("cp_is_delivery_to_TC")}:</label>
    <div class="controls">
        <input type="hidden"
               name="shipping_data[cp_is_delivery_to_TC]"
               value="N"
        />
        <input type="checkbox"
               name="shipping_data[cp_is_delivery_to_TC]"
               id="cp_is_delivery_to_TC"
               {if $shipping.cp_is_delivery_to_TC|default:"N" == "Y"}checked="checked"{/if}
               value="Y"
        />
    </div>
</div>