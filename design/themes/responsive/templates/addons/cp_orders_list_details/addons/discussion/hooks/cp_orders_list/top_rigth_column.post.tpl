{$discussion=$o.order_id|fn_get_discussion:"O"}
{if $addons.discussion.order_initiate == "Y" && !$discussion}
    <div class="cp-oc__orders-item_top-link">
        <a class="" href="{"orders.initiate_discussion?order_id=`$o.order_id`"|fn_url}">{__("start_communication")}</a>
    </div>
{/if}