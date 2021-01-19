{if $addons.zoho_salesiq.chat_with_us == "Y"}
	{include file="buttons/button.tpl" but_meta="ty-btn__text" but_role="text" but_text=__("zoho_salesiq_ask_question")
	but_onclick="fn_add_question_to_chat('Hi, I need to know more about {$product.product} ( id:[{$product.product_id}] )');" but_icon="ty-orders__actions-icon ty-icon-chat"}
{/if}