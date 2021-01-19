{include file="common/letter_header.tpl"}

{__("cp_templates.please_check_request", ["[crm_href]" => $request_data.crm_href]) } <br />
{ $request_data.email }
{ $request_data.phone }
{ $request_data.user }
{ $request_data.company }
{ $request_data.inn }
{include file="common/letter_footer.tpl"}