{if $edost.format}{$format = $edost.format}{else}{$format = false}{/if}
{if $edost.message}{$message = $edost.message}{else}{$message = false}{/if}
{if $mode != 'checkout2' && $mode != 'payment' && $format.compact == 'Y' && ($shipping.edost.format == 'door' || $shipping.edost.format == 'post') && $format.data[$shipping.edost.format].tariff}{$compact = true}{else}{$compact = false}{/if}
{if $shipping.edost.office_link}{$office = $shipping.edost.office_link}{else}{$office = false}{/if}
{if !$shipping.edost.office_map && !$compact}{$color = '#888'}{elseif $mode == 'checkout'}{$color = '#4fbe31'}{else}{$color = '#0388cc'}{/if}
{if $mode == 'checkout'}{$color2 = '#AAA'}{else}{$color2 = '#0388cc'}{/if}
{if $shipping.edost.office_address}{$address = $shipping.edost.office_address}{else}{$address = false}{/if}




{if $compact}
<div id="edost_tariff_div" style="display: none;">
	{$i = 0}

	{$f_key = $shipping.edost.format}
	{$f = $format.data.$f_key}

	<div id="edost_{$f_key}_div" data-cod="{if $f.cod}Y{else}N{/if}" class="edost_compact_div edost_format_border">
		{if $f.warning}<div class="edost_compact_hide edost_supercompact_hide edost_warning edost_format_info">{$f.warning nofilter}</div>{/if}
		{if $f.description}<div class="edost_compact_hide edost_supercompact_hide edost_format_info">{$f.description nofilter}</div>{/if}
		{if $f.insurance}<div class="edost_compact_hide edost_supercompact_hide edost_format_info"><span class="edost_insurance">{$f.insurance nofilter}</span></div>{/if}

		{foreach from=$f.tariff item=v key=k}
			{if $v.delimiter || $v.compact_cod_copy}{continue}{/if}

			{$id = "ID_DELIVERY_`$v.html_id`"}

			{if $format.day}{$day_width = 80}{else}{$day_width = 10}{/if}

			{if isset($v.company_ico) && $v.company_ico !== '' && $edost.template_ico == 'C'}
				{$ico = "`$edost.ico_path`/company/`$v.company_ico`.gif"}
			{elseif isset($v.ico) && $v.ico !== ''}
				{$ico = $v.ico}
			{else}
				{$ico = ""}
			{/if}

		{if $i != 0}
		<div class="edost_delimiter edost_delimiter_format"></div>
		{/if}
		{$i = 1}
		<div class="edost_format_tariff_main">
		<table class="edost_format_tariff" width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="edost_resize_show edost_resize_ico">
					<input style="display: none;" type="radio" id="{$id}" name="DELIVERY_ID" value="{$v.html_value}" {if $v.checked}checked="checked"{/if} onclick="submitForm()">

					{if $ico}
					<label class="edost_format_radio" for="{$id}"><img class="edost_ico" src="{$ico}" border="0"></label>
					{else}
					<div class="edost_ico"></div>
					{/if}

					<label for="{$id}">
					<span class="edost_resize_show edost_format_tariff" style="text-align: left; display: none;">{if $v.head}{$v.head}{else}{$v.company}{/if}
					{if $v.insurance != ''}
					<span class="edost_insurance edost_compact_cod_hide edost_compact_tariff_cod_hide"><br>{$v.insurance}</span>
					{/if}
					</span>
					</label>
				</td>

				<td class="edost_format_tariff edost_resize_tariff_show">
					<label for="{$id}">
						<img class="edost_ico edost_ico2" src="{$ico}" border="0">

						<span class="edost_format_tariff edost_compact_hide edost_supercompact_hide">{$v.company}</span>

						{if $v.name}
							<span class="edost_format_name edost_compact_hide edost_supercompact_hide"><br>{$v.name}</span>
						{/if}

						{if $v.insurance != ''}
						<span class="edost_insurance edost_compact_cod_hide edost_compact_tariff_cod_hide"><br>{$v.insurance}</span>
						{/if}
					</label>
				</td>

				{if !isset($v.error)}
				<td class="edost_format_price edost_resize_day edost_compact_hide edost_supercompact_hide edost_window_hide" width="{$day_width}" align="center">
					<label for="{$id}"><span class="edost_format_price edost_day">{if !empty($v.day)}{$v.day}{/if}</span></label>
				</td>
				{/if}

				<td class="edost_format_price edost_resize_show edost_resize_tariff_show2 edost_resize_price" width="105" style="text-align: center; vertical-align: center;">
					<label for="{$id}" class="edost_compact_cod_hide edost_compact_tariff_cod_hide">
						{if isset($v.free)}
						<span class="edost_format_price edost_price_free">{$v.free}</span>
						{else}
						<span class="edost_format_price edost_price"> {if isset($v.priceinfo_formatted)}{$v.pricetotal_formatted nofilter}{else}{$v.price_formatted nofilter}{/if}</span>
						{/if}

						{if isset($v.pricetotal_original)}
						<span class="edost_format_price edost_price_original">{$v.pricetotal_original_formatted nofilter}</span>
						{/if}
					</label>

					{if !empty($v.day)}
					<div><label for="{$id}" class="" style="margin: 0;"><span class="edost_format_price edost_day">{$v.day}</span></label></div>
					{/if}
				</td>
				<td class="edost_resize_tariff_show2 edost_supercompact_hide edost_order_hide edost_button_get" width="110" align="center">
					<div class="edost_button_get" onclick="window.edost_window.submit('{$id}')"><span>{$message.get}</span></div>
				</td>
			</tr>


			{if !empty($v.description) || !empty($v.warning) || !empty($v.error) || !empty($v.note) || isset($v.codplus_formatted)}
			<tr name="edost_description">
				<td colspan="6" class="edost_resize_show edost_resize_tariff_show edost_description">
					{if !empty($v.error)}
					<div class="edost_format_description edost_warning"><b>{$v.error nofilter}</b></div>
					{/if}

					{if !empty($v.warning)}
					<div class="edost_format_description edost_warning">{$v.warning nofilter}</div>
					{/if}

					{if isset($v.codplus_formatted)}
					<div class="edost_payment edost_compact_hide edost_supercompact_hide edost_compact_tariff_cod_hide edost_resize_cod2">{$message.cod_tariff}{if $v.codplus_formatted != 0} <span class="edost_bracket">(</span><span class="edost_codplus">+ {$v.codplus_formatted nofilter}</span><span class="edost_bracket">)</span>{/if}</div>
					{/if}

					{if !empty($v.note)}
						<div class="edost_format_description edost_window_hide edost_note_active">{$v.note nofilter}</div>
					{/if}

					{if !empty($v.description)}
					<div class="edost_format_description edost_description">{$v.description nofilter}</div>
					{/if}
				</td>
			</tr>
			{/if}
		</table>
		</div>
		{/foreach}
	</div>
</div>
{/if}




{if $mode == 'checkout2' || $office && $mode == 'update'}
	{if !$edost.map_update}
	<input id="edost_office_data" autocomplete="off" value='parsed' type="hidden">
	{else if !empty($format.map_json)}
	<input autocomplete="off" id="edost_office_data" value='{literal}{{/literal}"ico_path": "{$edost.ico_path}", "yandex_api_key": "{if $edost.yandex_api_key}{$edost.yandex_api_key}{/if}", "template_ico": "{$edost.template_ico}", {$format.map_json nofilter}{literal}}{/literal}' type="hidden">
	{/if}
	<input style="display: none;" id="edost_office_data_parsed" name="edost_office_data_parsed" value="" type="radio" checked="">
{/if}




{if $mode == 'checkout2' && $edost.template_ico_style}
<style>
	.litecheckout__shippings .litecheckout__shipping-method__logo-image { max-height: 45px !important; }
</style>
{/if}




{if $mode != checkout2 && ($compact || $shipping.edost.note || $shipping.edost.warning || $shipping.edost.error || $office || $mode == 'payment')}
	{if $mode == 'payment' || $mode == 'update'}
	{if $mode == 'payment'}{$w = 'width: 100%;'}{else}{$w = ''}{/if}
	<div style="{$w} {if $shipping.edost.office}padding-bottom: 1px;{/if}">
	    {if $edost.cod_data.warning}<div style="margin: 10px; text-align: center; color: #F00;">{$edost.cod_data.warning nofilter}</div>{/if}
	    {if $edost.cod_data.note}<div style="margin: 10px; text-align: center;">{$edost.cod_data.note nofilter}</div>{/if}
	</div>
	{/if}


	{if $mode == 'info'}<div class="well orders-right-pane form-horizontal" {if !empty($style)}style="{$style}"{/if}>{/if}
	{if $mode == 'update'}<div class="{if $shipping.edost.office}well{/if} orders-right-pane form-horizontal" style="margin: 8px 0 0 0;">{/if}


	{if $shipping.edost.office && ($mode == 'info' || $mode == 'update')}
		<div style="font-size: 16px;"><a target="_blank" href="{$shipping.edost.office_detailed}">{$shipping.edost.office_link_head} &#8470; {$shipping.edost.office.code}</a></div>
		<div style="font-size: 16px; margin: 8px 0;">{if $shipping.edost.office.name}{$shipping.edost.office.name}<br>{/if}{$shipping.edost.office.address_full}</div>
		<div style="font-size: 16px; margin: 8px 0;">{$shipping.edost.office.tel|replace:', ':'<br>' nofilter}</div>
		<div style="font-size: 16px; color: #888;">{$shipping.edost.office.schedule|replace:', ':'<br>' nofilter}</div>
	{/if}


	{if $office && ($mode == 'checkout' || $mode == 'update')}
		{if ($format.compact || $mode == 'update') && $shipping.edost.office_map}
			{$on_click = "edost_OpenOffice('all')"}
		{elseif !$shipping.edost.office_map}
			{$on_click = "edost_office.info(0, '`$shipping.edost.office_detailed`')"}
		{else}
			{$on_click = "edost_OpenOffice('profile_`$shipping.service_code`_`$shipping.shipping_id`')"}
		{/if}

		<div id="cscart_edost_office_div" style="display: block; width: 100%; {if $mode == 'checkout' || $shipping.edost.office}padding: 0; margin: 15px 0 0 0;{/if} {if $address}border: 0px solid #EEE;{/if} text-align: center; border-radius: 5px;">
			{if $mode == 'checkout' && $address}
				<div style="display: inline-block;"><span style="color: #AAA; font-size: 18px; vertical-align: middle;">{$shipping.edost.office_link_head}</span><br><span style="color: #000; font-size: 18px; vertical-align: middle;">{$address}</span></div>
			{/if}
			<div class="cscart_edost_office_button cscart_edost_office_button_{if $address}address{else}get{/if} cscart_edost_office_button_{if !$shipping.edost.office_map}map{elseif $mode == 'checkout'}checkout{else}backend{/if}" onclick="{$on_click nofilter}"><span>{if $shipping.edost.compact && $shipping.edost.compact_link}{$shipping.edost.compact_link nofilter}{else}{$office nofilter}{/if}</span></div>
			<input style="display: none;" id="edost_office" name="edost_office" value="{if $address}{$shipping.edost.html_value}{/if}" type="radio" checked="">
			{if !$address}
			<div id="cscart_edost_office_error" style="display: none;">{$edost.message.office_unchecked nofilter}</div>
			{/if}
		</div>
	{/if}

    {if $mode != 'payment'}
		{if $shipping.edost.error || $shipping.edost.warning || $shipping.edost.note}{$div = true}{else}{$div = false}{/if}

		{if $div}<div style="display: block; width: 100%; {if $mode == 'checkout' || $shipping.edost.office}margin: 8px 0;{/if}">{/if}

		{if $shipping.edost.error}
			<div style="display: block; width: 100%; color: #F00; margin: 5px 0; text-align: center;">
				{$shipping.edost.error nofilter}
			</div>
		{/if}

		{if $shipping.edost.warning}
			<div style="display: block; width: 100%; color: #F00; margin: 5px 0; text-align: center;">
				{$shipping.edost.warning nofilter}
			</div>
		{/if}

		{if $shipping.edost.note}
			<div style="display: block; width: 100%; color: #888; margin: 5px 0; text-align: center;">
				{$shipping.edost.note nofilter}
			</div>
		{/if}

		{if $div}</div>{/if}

		{if $compact && $shipping.edost.compact_link}
		<div style="display: block; width: 100%; margin: 15px 0 0 0; text-align: center;">
			<div class="cscart_edost_button cscart_edost_button_{if $mode == 'checkout'}checkout{else}backend{/if}" style="width: auto; max-width: 250px; margin: 5px 0 0 0;" onclick="edost_resize.start(); edost_window.set('{$f_key}', 'head={$shipping.edost.compact_head}');">
				<span>{if $v.format == 'door'}{$message.door}{else}{$message.post}{/if}</span>
			</div>
		</div>
		{/if}
	{/if}

	{if $mode == 'info' || $mode == 'update'}</div>{/if}
{/if}




{if ($mode == 'checkout2' || $mode == 'update') && $edost.warning}
	<div style="width: 100%; margin: 10px 0 {if $mode == 'checkout2'}10px{else}0{/if} 0; text-align: center; color: #F00; line-height: 18px;">{$edost.warning nofilter}</div>
{/if}




{if ($office || $compact) && ($mode == 'checkout' || $mode == 'update')}
	{if $edost.script}
		<input autocomplete="off" id="edost_script_data" value='{$edost.script|json_encode}' type="hidden">
	{/if}

	{if $edost.template_data}
		<input id="edost_template_data" value="{$edost.template_data nofilter}" type="hidden">
		<input id="edost_template_2019" name="edost_template_2019" data-ico="{$edost.template_ico}" data-compact="Y" data-priority="B" data-window_scroll_disable="N" value="" type="hidden">
	{/if}

	{script src="js/addons/rus_edost2/func.js"}
{/if}