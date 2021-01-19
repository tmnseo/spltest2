<div class="control-group">
    <label for="cp_show_on_brands_{$id}_{$num}" class="control-label">{__("cp_spl_theme.cp_show_on_brands")}</label>
    <div class="controls">
        <input type="hidden" name="feature_data[variants][{$num}][cp_show_on_brands]" value="N" />
        <input type="checkbox" id="cp_show_on_brands_{$id}_{$num}" name="feature_data[variants][{$num}][cp_show_on_brands]" value="Y" {if $var.cp_show_on_brands == "Y"}checked="checked"{/if} />
    </div>
</div>