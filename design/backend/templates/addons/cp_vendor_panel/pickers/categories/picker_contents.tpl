{if !$smarty.request.extra}
<script type="text/javascript">
(function(_, $) {
    _.tr('text_items_added', '{__("text_items_added")|escape:"javascript"}');
    var display_type = '{$smarty.request.display|escape:javascript nofilter}';

    $.ceEvent('on', 'ce.formpost_categories_form', function(frm, elm) {
        var categories = {};

        if ($('input.cm-item:checked', frm).length > 0) {
            $('input.cm-item:checked', frm).each( function() {
                var id = $(this).val();
                categories[id] = $.trim($('#category_' + id).text());
            });

            {literal}
            $.cePicker('add_js_item', frm.data('caResultId'), categories, 'c', {
                '{category_id}': '%id',
                '{category}': '%item'
            });
            {/literal}

            if (display_type != 'radio') {
                $.ceNotification('show', {
                    type: 'N', 
                    title: _.tr('notice'), 
                    message: _.tr('text_items_added'), 
                    message_state: 'I'
                });
            }
        }

        return false;
    });
}(Tygh, Tygh.$));
</script>
{/if}


<form action="{$smarty.request.extra|fn_url}" data-ca-result-id="{$smarty.request.data_id}" method="post" name="categories_form">
<div class="items-container multi-level">
    {if $categories}
        <div class="table-responsive-wrapper">
            <table width="100%" class="table table-middle table--relative table-responsive">
            <thead>
                <tr>
                    <th width="1%">
                    {if $smarty.request.display == "checkbox"}
                        {include file="common/check_items.tpl"}
                    {/if}
                    </th>
                    <th>
                    {__("category")}</th>
                </tr>
            </thead>

            {foreach from=$categories item="w_cat"}
            
                {$image_size = 50}
                <tr class="">
                    
                    <td class="left first-column" width="1%" data-th="">
                        {if $smarty.request.display == "checkbox"}
                            <input type="checkbox" name="{$smarty.request.checkbox_name|default:"category_ids"}[]" value="{$w_cat.category_id}" class="cm-item" />
                        {elseif $smarty.request.display == "radio"}
                            <input type="radio" name="category_id" class="cm-item" value="{$w_cat.category_id}" />
                        {/if}
                    </td>
                    <td id="category_{$w_cat.category_id}" data-th="{__("category")}">
                        {$w_cat.category}
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
</div>

<div class="buttons-container">
    {if $smarty.request.display == "radio"}
        {assign var="but_close_text" value=__("choose")}
    {else}
        {assign var="but_close_text" value=__("add_categories_and_close")}
        {assign var="but_text" value=__("add_categories")}
    {/if}
    {include file="buttons/add_close.tpl" is_js=$smarty.request.extra|fn_is_empty}
</div>

</form>
