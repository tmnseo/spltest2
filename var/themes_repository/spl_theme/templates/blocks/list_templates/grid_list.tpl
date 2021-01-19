{if $products}

    {script src="js/tygh/exceptions.js"}
    

    {if !$no_pagination}
        {include file="common/pagination.tpl"}
    {/if}

    {if !$no_sorting}
        {include file="views/products/components/sorting.tpl"}
    {/if}

    {if !$show_empty}
        {split data=$products size=$columns|default:"2" assign="splitted_products"}
    {else}
        {split data=$products size=$columns|default:"2" assign="splitted_products" skip_complete=true}
    {/if}

    {math equation="100 / x" x=$columns|default:"2" assign="cell_width"}
    {if $item_number == "Y"}
        {assign var="cur_number" value=1}
    {/if}

    {* FIXME: Don't move this file *}
    {script src="js/tygh/product_image_gallery.js"}

    {if $settings.Appearance.enable_quick_view == 'Y'}
        {$quick_nav_ids = $products|fn_fields_from_multi_level:"product_id":"product_id"}
    {/if}
    <div class="grid-list{if $custom_class} {$custom_class}{/if}">
        {strip}
            {foreach from=$splitted_products item="sproducts" name="sprod"}
                {foreach from=$sproducts item="product" name="sproducts"}
                    <div class="ty-column{$columns}">
                        {if $product}
                            {assign var="obj_id" value=$product.product_id}
                            {assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
                            {include file="common/product_data.tpl" product=$product}
                            
                            <div class="ty-grid-list__item ty-quick-view-button__wrapper 
                                {if $settings.Appearance.enable_quick_view == 'Y' || $show_features} ty-grid-list__item--overlay{/if}">
                                {assign var="form_open" value="form_open_`$obj_id`"}
                                {$smarty.capture.$form_open nofilter}
                                {hook name="products:product_multicolumns_list"}
                                        <div class="ty-grid-list__image">
                                            {include file="views/products/components/product_icon.tpl" product=$product show_gallery=false}

                                            {assign var="product_labels" value="product_labels_`$obj_prefix``$obj_id`"}
                                            {$smarty.capture.$product_labels nofilter}
                                        </div>
                                        <div class="ty-grid-list__item-content">
                                            <div class="ty-grid-list__item-product-code">
                                                {$product.product_code}
                                            </div>

                                            <div class="ty-grid-list__item-name">
                                                {if $item_number == "Y"}
                                                    <span class="item-number">{$cur_number}.&nbsp;</span>
                                                    {math equation="num + 1" num=$cur_number assign="cur_number"}
                                                {/if}

                                                {assign var="name" value="name_$obj_id"}
                                                <bdi>{$smarty.capture.$name nofilter}</bdi>
                                            </div>

                                            <div class="ty-grid-list__item-supplier">
                                                <span class="cp-item-supplier__label">{__("supplier")}: </span>
                                                <span class="cp-item-supplier__value">
                                                    {if !$auth.user_id}
                                                        {__("available_after")} <a href="{"profiles.add"|fn_url}" rel="nofollow">{__("cp_spl.registry")}</a>
                                                    {else}
                                                        <a href="{"companies.products?company_id=`$product.company_id`"|fn_url}" class="company">{$product.company_name}</a>
                                                    {/if}
                                                </span>
                                            </div>
                                            {if $show_rating}
                                                {assign var="rating" value="rating_$obj_id"}
                                                {if $smarty.capture.$rating}
                                                    <div class="grid-list__rating">
                                                        {$smarty.capture.$rating nofilter}
                                                    </div>
                                                {/if}
                                            {/if}
                                            <div class="grid-list__item-block_flex">
                                                <div class="grid-list__item-price-delivery">
                                                    {if $product.price > 0}
                                                        <div class="ty-grid-list__price {if $product.price == 0}ty-grid-list__no-price{/if}">
                                                            <span class="cp-item-price__label">{__("price")}:</span>
                                                            <span class="cp-item-price__value">
                                                                {assign var="old_price" value="old_price_`$obj_id`"}
                                                                {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}{/if}

                                                                {assign var="price" value="price_`$obj_id`"}
                                                                {$smarty.capture.$price nofilter}

                                                                {assign var="clean_price" value="clean_price_`$obj_id`"}
                                                                {$smarty.capture.$clean_price nofilter}

                                                                {assign var="list_discount" value="list_discount_`$obj_id`"}
                                                                {$smarty.capture.$list_discount nofilter}
                                                            </span>
                                                        </div>
                                                    {/if}

                                                    {if $product.delivery_data}
                                                        <div class="ty-grid-list__delivery">
                                                            <span class="cp-item-delivery__label">{__("delivery")}:</span>
                                                            <span class="cp-item-delivery__value">
                                                            </span>
                                                        </div>
                                                    {/if}
                                                </div>

                                                <div class="ty-grid-list__buttons">
                                                    {if $settings.Appearance.enable_quick_view == 'Y'}
                                                        {include file="views/products/components/quick_view_link.tpl" quick_nav_ids=$quick_nav_ids}
                                                    {/if}
                                                    {if $show_add_to_cart}
                                                        {$add_to_cart = "add_to_cart_`$obj_id`"}
                                                        {$smarty.capture.$add_to_cart nofilter}
                                                    {/if}
                                                </div>
                                            </div>
                                        </div>
                                {/hook}
                                {assign var="form_close" value="form_close_`$obj_id`"}
                                {$smarty.capture.$form_close nofilter}
                            </div>
                        {/if}
                    </div>
                {/foreach}
                {if $show_empty && $smarty.foreach.sprod.last}
                    {assign var="iteration" value=$smarty.foreach.sproducts.iteration}
                    {capture name="iteration"}{$iteration}{/capture}
                    {hook name="products:products_multicolumns_extra"}
                    {/hook}
                    {assign var="iteration" value=$smarty.capture.iteration}
                    {if $iteration % $columns != 0}
                        {math assign="empty_count" equation="c - it%c" it=$iteration c=$columns}
                        {section loop=$empty_count name="empty_rows"}
                            <div class="ty-column{$columns}">
                                <div class="ty-product-empty">
                                    <span class="ty-product-empty__text">{__("empty")}</span>
                                </div>
                            </div>
                        {/section}
                    {/if}
                {/if}
            {/foreach}
        {/strip}
    </div>

    {if !$no_pagination}
        {include file="common/pagination.tpl"}
    {/if}

{/if}

{capture name="mainbox_title"}{$title}{/capture}