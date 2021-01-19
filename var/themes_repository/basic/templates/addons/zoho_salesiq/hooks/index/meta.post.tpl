{if strpos($addons.zoho_salesiq.code, 'widget') != false || strpos($addons.zoho_salesiq.code, 'float') != false || strpos($addons.zoho_salesiq.code, 'widgetcode') != false} 
	{$addons.zoho_salesiq.code nofilter}
	{if $addons.zoho_salesiq.disable_chat == 'Y'}
		{literal}
			<script>
					if(window.$zoho){
						$zoho.salesiq.internalready = function() { 
							$zoho.salesiq.floatbutton.visible("hide");
						};
					}
			</script>
		{/literal}
	{/if}
	{if $addons.zoho_salesiq.chat_with_us == "Y"}
		{literal}
			<script>
				function fn_add_question_to_chat(question){
					var question = question+ '\nurl:'+location.href;
					if(window.$zoho){
						var $zs = $zoho.salesiq || $zoho.livedesk ; 
						 $zs.floatwindow.visible('show');
						 $zs.visitor.question(question);
						 $zs.chat.start();
					}
				}	
			</script>
		{/literal}
	{/if}
{/if}
