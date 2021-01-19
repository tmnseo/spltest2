{include file="common/letter_header.tpl"}

{__("cp_templates.hello", ["[user]" => $request_data.user]) } <br />
{__("cp_templates.info")} <br />
{__("cp_templates.support_info", ["[knowledge_base]" => $request_data.knowledge_base, [support_email]" => $request_data.support_email]) } <br />
{include file="common/letter_footer.tpl"}