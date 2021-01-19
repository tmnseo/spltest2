{if $is_wishlist}
<div class="ty-wishlist-item">
    <a href="{"wishlist.delete?cart_id=`$product.cart_id`"|fn_url}" class="ty-wishlist-item__remove ty-remove" title="{__("remove")}">
        {__("remove_from_favorites")}
    </a>
</div>
{/if}