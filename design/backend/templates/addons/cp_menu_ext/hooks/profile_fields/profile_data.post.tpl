<div class="control-group">
    <label class="control-label">{__("numeric_field_type")}
        {include file="common/tooltip.tpl" tooltip=__("numeric_field_type_tooltip")}:</label>
    <div class="controls">
        <input type="hidden" name="field_data[numeric_field_type]" value="N" />
        <input type="checkbox" name="field_data[numeric_field_type]" value="Y" {if $field.numeric_field_type == "Y"}checked="checked"{/if} />
    </div>
</div>