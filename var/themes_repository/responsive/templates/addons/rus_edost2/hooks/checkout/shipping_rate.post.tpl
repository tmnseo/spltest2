{if isset($shipping.edost.price_long)}
	{if $shipping.edost.free}{$price = $shipping.edost.free}{else}{$price = $shipping.edost.price_formatted}{/if}
    {$rate = $price|replace:'<br>':' ' scope=parent}
    {$delivery_time = "" scope=parent}
{elseif $shipping.edost.free}
    {$rate = $shipping.edost.free scope=parent}
{/if}

{if !empty($shipping.edost.pricetotal_original)}
    {capture name="price"}
		{if $shipping.edost.free}{$price = $shipping.edost.free}{else}{$price = $shipping.edost.price_formatted}{/if}
		{$price nofilter} <span class="edost_price_original">{$shipping.edost.pricetotal_original_formatted nofilter}</span>
    {/capture}
    {$rate = $smarty.capture.price scope=parent}
{/if}

{if isset($shipping.edost.error)}
    {$rate = "" scope=parent}
{/if}