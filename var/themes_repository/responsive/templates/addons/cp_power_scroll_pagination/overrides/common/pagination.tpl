{assign var="id" value=$id|default:"pagination_contents"}
{assign var="pagination" value=$search|fn_generate_pagination}
{assign var='scroll_pagination' value=$location_data.location_id|fn_check_scroll_pagination}
{if $smarty.capture.pagination_open != "Y"}
	<div class="ty-pagination-container cm-pagination-container" id="{$id}">

	{if $save_current_page}
		<input type="hidden" name="page" value="{$search.page|default:$smarty.request.page}" />
	{/if}

	{if $save_current_url}
		<input type="hidden" name="redirect_url" value="{$config.current_url}" />
	{/if}
{/if}

{if $pagination.total_pages > 1}
	{if $settings.Appearance.top_pagination == "Y" && $smarty.capture.pagination_open != "Y" || $smarty.capture.pagination_open == "Y"}
	{assign var="c_url" value=$config.current_url|fn_query_remove:"page"}

	{if !$config.tweaks.disable_dhtml || $force_ajax}
		{assign var="ajax_class" value="cm-ajax"}
	{/if}

	{if $smarty.capture.pagination_open == "Y"}
	<div class="ty-pagination__bottom">
	{/if}
	<div class="ty-pagination {if ($scroll_pagination == "Y" || $settings.cp_power_scroll_pagination.general.all_pages == "Y") && $settings.cp_power_scroll_pagination.general.hide_pagination == "Y"} hidden{/if}">
		{if $pagination.prev_range}
			<a data-ca-scroll=".cm-pagination-container" href="{"`$c_url`&page=`$pagination.prev_range``$extra_url`"|fn_url}" data-ca-page="{$pagination.prev_range}" class="cm-history hidden-phone ty-pagination__item ty-pagination__range {$ajax_class}" data-ca-target-id="{$id}">{$pagination.prev_range_from} - {$pagination.prev_range_to}</a>
		{/if}
		<a data-ca-scroll=".cm-pagination-container" class="ty-pagination__item ty-pagination__btn {if $pagination.prev_page}ty-pagination__prev cm-history {$ajax_class}{/if}" {if $pagination.prev_page}href="{"`$c_url`&page=`$pagination.prev_page`"|fn_url}" data-ca-page="{$pagination.prev_page}" data-ca-target-id="{$id}"{/if}><i class="ty-pagination__text-arrow"></i>&nbsp;<span class="ty-pagination__text">{__("prev_page")}</span></a>

		<div class="ty-pagination__items">
			{foreach from=$pagination.navi_pages item="pg"}
				{if $pg != $pagination.current_page}
					<a data-ca-scroll=".cm-pagination-container" href="{"`$c_url`&page=`$pg``$extra_url`"|fn_url}" data-ca-page="{$pg}" class="cm-history ty-pagination__item {$ajax_class}" data-ca-target-id="{$id}">{$pg}</a>
				{else}
					<span class="ty-pagination__selected">{$pg}</span>
				{/if}
			{/foreach}
		</div>

		<a data-ca-scroll=".cm-pagination-container" class="ty-pagination__item ty-pagination__btn {if $pagination.next_page}pagination-next-range ty-pagination__next cm-history {$ajax_class}{/if}" {if $pagination.next_page}href="{"`$c_url`&page=`$pagination.next_page``$extra_url`"|fn_url}" data-ca-page="{$pagination.next_page}" data-ca-target-id="{$id}"{/if}><span class="ty-pagination__text">{__("next")}</span>&nbsp;<i class="ty-pagination__text-arrow"></i></a>

		{if $pagination.next_range}
			<a data-ca-scroll=".cm-pagination-container" href="{"`$c_url`&page=`$pagination.next_range``$extra_url`"|fn_url}" data-ca-page="{$pagination.next_range}" class="pagination-next-range cm-history ty-pagination__item hidden-phone ty-pagination__range {$ajax_class}" data-ca-target-id="{$id}">{$pagination.next_range_from} - {$pagination.next_range_to}</a>
		{/if}
	</div>
	{if $smarty.capture.pagination_open == "Y"}
		</div>
	{/if}
	{else}
		<div><a data-ca-scroll=".cm-pagination-container" href="" data-ca-page="{$pg}" data-ca-target-id="{$id}" class="hidden"></a></div>
	{/if}
{/if}

{if $smarty.capture.pagination_open == "Y"}
	<!--{$id}--></div>
	{capture name="pagination_open"}N{/capture}
{elseif $smarty.capture.pagination_open != "Y"}
	{capture name="pagination_open"}Y{/capture}
{/if}
{if ($scroll_pagination=="Y" || $settings.cp_power_scroll_pagination.general.all_pages == "Y") && ($pagination.next_page || $pagination.next_range)}
    {if $smarty.capture.pagination_open == "N"}
        {if $settings.cp_power_scroll_pagination.general.show_more_link == "Y" && $settings.cp_power_scroll_pagination.general.show_more_link_1st != "Y"}
            <div class="show-more-ajax-pagination {if $settings.cp_power_scroll_pagination.general.hide_pagination!="Y"}cp-pagination-visible{/if}"><div class="cp-load-more" >{__('show_more')}</div></div>
            <script>
              (function(_, $) {
                $.ceEvent('on', 'ce.commoninit', function(context) {
                    var window_height = document.body.clientHeight;
                    ajaxProcess = false;
                    pagination_href = '';
                    
                    $('.show-more-ajax-pagination', context).on('click',function() {
                        {if $settings.cp_power_scroll_pagination.general.hide_pagination=='Y'}
                            if(!pagination_href) {
                                pagination_href=$('.pagination-next-range').prop('href');
                            }
                        {else}
                            pagination_href=$('.pagination-next-range').prop('href');
                        {/if}
                        var ajax_pagination = {
                            ajax_pagination: 'Y',
                        };
                        if(pagination_href) {
                            $('#{$id} .ty-pagination__next').remove();
                            ajaxProcess = true;
                            $.ceAjax('request', pagination_href, {
                                data: ajax_pagination,
                                callback: function(data) {
                                    var search_container = '<div>' + data.text + '</div>';
                                    var pagination_content = $(search_container).find('#{$id}');
                                    if($(pagination_content).length > 0) {
                                        $(pagination_content).find('.ty-sort-container').remove();
                                        ajaxProcess = false;
                                        $('#{$id}').append($(pagination_content).html()); 
                                        $.commonInit($('#{$id}'));
                                        var pgn = $('#{$id} .cp-pagination');
                                        {if $settings.cp_power_scroll_pagination.general.hide_pagination != 'Y'}
                                            $(pgn[0]).remove();
                                        {else}
                                            $('.cp-pagination').remove();
                                        {/if}
                                        {if $settings.cp_power_scroll_pagination.general.hide_pagination == 'Y'}
                                            pagination_href = $(data.text).find('.pagination-next-range').prop('href');
                                        {else}
                                            pagination_href = $('.pagination-next-range').prop('href');
                                        {/if}
                                        if(!pagination_href) {
                                            $('.show-more-ajax-pagination').addClass('hidden');
                                        }
                                    }
                                }
                            });
                        } 
                        return false;
                    });
                    $(document).ajaxComplete(function() {
                        var pagination_href = $('.pagination-next-range').prop('href');
                        if(pagination_href) {
                            $('.show-more-ajax-pagination').removeClass('hidden');
                        }
                    });
                });
                })(Tygh, Tygh.$);
            </script>
        {else if $settings.cp_power_scroll_pagination.general.show_more_link_1st == "Y"}
            <div class="show-more-ajax-pagination {if $settings.cp_power_scroll_pagination.general.hide_pagination != "Y"}cp-pagination-visible{/if}"><div class="cp-load-more" >{__('show_more')}</div></div>
            <script>
                var first_load = false;
                (function(_, $){
                    $.ceEvent('on', 'ce.commoninit', function(context) {
                        var window_height = document.body.clientHeight;
                        ajaxProcess = false;
                        pagination_href = '';
                        $('.show-more-ajax-pagination', context).on('click',function() {
                            {if $settings.cp_power_scroll_pagination.general.hide_pagination == 'Y'}
                            //if(!pagination_href) {
                                    pagination_href = $('.ty-pagination__next').prop('href');
                                //}
                            {else}
                                pagination_href = $('.ty-pagination__next').prop('href');
                            {/if}
                            var ajax_pagination = {
                                ajax_pagination: 'Y',
                            };
                            if(pagination_href) {
                                $('#{$id} .ty-pagination__next').remove();
                                ajaxProcess = true;
                                $.ceAjax('request', pagination_href, {
                                    data: ajax_pagination,
                                    callback: function(data) {
                                        var search_container = '<div>' + data.text + '</div>';
                                        var pagination_content = $(search_container).find('#{$id}');
                                        if($(pagination_content).length > 0){
                                            $(pagination_content).find('.ty-sort-container').remove();
                                            ajaxProcess = false;
                                            var pgn = $('#{$id} .cp-pagination');
                                            {if $settings.cp_power_scroll_pagination.general.hide_pagination != 'Y'}
                                                $(pgn[0]).remove();
                                            {else}
                                                $('.cp-pagination').remove();
                                            {/if}
                                            $('#{$id}').append($(pagination_content).html()); 
                                            $.commonInit($('#{$id}'));
                                            {if $settings.cp_power_scroll_pagination.general.hide_pagination == 'Y'}
                                                pagination_href = $(data.text).find('.ty-pagination__next').prop('href');
                                            {/if}
                                            $('.show-more-ajax-pagination').addClass('hidden');
                                            first_load=true;
                                        }
                                    }
                                });
                            }
                            return false;
                        });
                    });
                })(Tygh, Tygh.$);
                $(window).scroll(function() {
                    if(($(window).scrollTop() + 800 >= $("#{$id}").height()) && ajaxProcess == false && first_load == true) {
                        {if $settings.cp_power_scroll_pagination.general.hide_pagination == 'Y'}
                            //if(!pagination_href) {
                                pagination_href = $('.ty-pagination__next').prop('href');
                            //}
                        {else}
                            pagination_href = $('.ty-pagination__next').prop('href');
                        {/if}
                        var ajax_pagination = {
                            ajax_pagination: 'Y',
                        };
                        if(pagination_href) {
                            $('#{$id} .ty-pagination__next').remove();
                            ajaxProcess = true;
                             $.ceAjax('request', pagination_href, {
                                data: ajax_pagination,
                                callback: function(data) {
                                    var search_container = '<div>' + data.text + '</div>';
                                    var pagination_content = $(search_container).find('#{$id}');
                                    if($(pagination_content).length > 0){
                                        $(pagination_content).find('.ty-sort-container').remove();
                                        ajaxProcess = false;
                                            var pgn=$('#{$id} .cp-pagination');
                                        {if $settings.cp_power_scroll_pagination.general.hide_pagination != 'Y'}
                                            $(pgn[0]).remove();
                                        {else}
                                            $('.cp-pagination').remove();
                                        {/if}
                                        $('#{$id}').append($(pagination_content).html()); 
                                        $.commonInit($('#{$id}'));
                                            {if $settings.cp_power_scroll_pagination.general.hide_pagination == 'Y'}
                                                //pagination_href=$(data.text).find('.ty-pagination__next').prop('href');
                                            {/if}
                                    }
                                }
                            });
                        }
                    }
                });
            </script>
        {else}
            <script>
                var window_height = document.body.clientHeight;
                ajaxProcess = false; 
                pagination_href = '';
                $(window).scroll(function() {
                    if(($(window).scrollTop() + 800 >= $("#{$id}").height()) && ajaxProcess == false) {
                        {if $settings.cp_power_scroll_pagination.general.hide_pagination == 'Y'}
                            //if(!pagination_href) {
                                pagination_href = $('.ty-pagination__next').prop('href');
                            //}
                        {else}
                            pagination_href = $('.ty-pagination__next').prop('href');
                        {/if}
                        var ajax_pagination = {
                            ajax_pagination: 'Y',
                        };
                        if(pagination_href) {
                            $('#{$id} .ty-pagination__next').remove();
                            ajaxProcess = true;
                            $.ceAjax('request', pagination_href, {
                                data: ajax_pagination,
                                callback: function(data) {
                                    var search_container = '<div>' + data.text + '</div>';
                                    var pagination_content = $(search_container).find('#{$id}');
                                    if($(pagination_content).length > 0){
                                        $(pagination_content).find('.ty-sort-container').remove();
                                        ajaxProcess = false;
                                        var pgn = $('#{$id} .cp-pagination');
                                        {if $settings.cp_power_scroll_pagination.general.hide_pagination != 'Y'}
                                            $(pgn[0]).remove();
                                        {else}
                                            $('.cp-pagination').remove();
                                        {/if}
                                        $('#{$id}').append($(pagination_content).html()); 
                                        $.commonInit($('#{$id}'));
                                        {if $settings.cp_power_scroll_pagination.general.hide_pagination == 'Y'}
                                            //pagination_href = $(data.text).find('.ty-pagination__next').prop('href');
                                        {/if}
                                    }
                                }
                            });
                        }
                    }
                });
            </script>
        {/if}
    {/if}
{/if}
