{include file="views/profiles/components/profiles_scripts.tpl"}

{$dispatch = "profiles.update"}

{if $runtime.action}
    {$dispatch = "profiles.update.{$runtime.action}"}
{/if}

{if $runtime.mode == "add" && $settings.General.quick_registration == "Y"}

    <div class="ty-account">

        <form name="profiles_register_form" enctype="multipart/form-data" action="{""|fn_url}" method="post">
            {include file="views/profiles/components/profile_fields.tpl" section="C" nothing_extra="Y"}
            {include file="views/profiles/components/profiles_account.tpl" nothing_extra="Y" location="checkout"}

            {if $smarty.request.return_url}
                <input type="hidden" name="return_url" value="{$smarty.request.return_url}" />
            {/if}

            {hook name="profiles:account_update"}
            {/hook}

            {include file="common/image_verification.tpl" option="register" align="left" assign="image_verification"}
            {if $image_verification}
            <div class="ty-control-group">
                {$image_verification nofilter}
            </div>
            {/if}

            <div class="ty-profile-field__buttons buttons-container">
                {include file="buttons/register_profile.tpl" but_name="dispatch[{$dispatch}]"}
            </div>
        </form>
    </div>
    {capture name="mainbox_title"}{__("register_new_account")}{/capture}
{elseif $runtime.mode == "add" && $settings.General.quick_registration == "N"}
    {capture name="tabsbox"}
        <div class="ty-profile-field ty-account form-wrap" id="content_general">
            <form name="profile_form" enctype="multipart/form-data" action="{""|fn_url}" method="post">
                <input id="selected_section" type="hidden" value="general" name="selected_section"/>
                <input id="default_card_id" type="hidden" value="" name="default_cc"/>
                <input type="hidden" name="profile_id" value="{$user_data.profile_id}" />

                {if $smarty.request.return_url}
                    <input type="hidden" name="return_url" value="{$smarty.request.return_url}" />
                {/if}

                {capture name="group"}
                    
                    <div class="cp-section-profile-add cp-contact-person-section active" id="section_profile_add_1">
                        <h2 class="ty-account__title-section">{__("contact_person")}</h2>
                        {include file="views/profiles/components/profile_fields.tpl" 
                            section="C" 
                            nothing_extra=true
                            hide_clearfix=true
                            exclude=fn_cp_checkout_modifications_get_exclude_fields_by_section($profile_fields, $smarty.const.CP_SECTION_CONTACT_INFO)
                        }
                        {include file="views/profiles/components/profiles_account.tpl" nothing_extra=true}
                    </div>

                    <div class="cp-section-profile-add cp-info-company-section" id="section_profile_add_2">
                        <h2 class="ty-account__title-section">{__("cp_company_information")}</h2>
                        {include file="views/profiles/components/profile_fields.tpl" 
                            nothing_extra=true
                            hide_clearfix=true
                            show_hint=true
                            section="C"  
                            cp_add_disabled=true
                            exclude=fn_cp_checkout_modifications_get_exclude_fields_by_section($profile_fields, $smarty.const.CP_SECTION_COMPANY_INFO)
                        }
                            {if $profile_fields.B || $profile_fields.S}
                                {if $settings.General.user_multiple_profiles == "Y" && $runtime.mode == "update"}
                                    <p>{__("text_multiprofile_notice")}</p>
                                    {include file="views/profiles/components/multiple_profiles.tpl" profile_id=$user_data.profile_id}    
                                {/if}
                                {$settings.Checkout.address_position = "billing_first"}

                                {if $settings.Checkout.address_position == "billing_first"}
                                    {assign var="first_section" value="B"}
                                    {assign var="first_section_text" value=__("billing_address")}
                                    {assign var="sec_section" value="S"}
                                    {assign var="sec_section_text" value=__("shipping_address")}
                                    {assign var="body_id" value="sa"}
                                {else}
                                    {assign var="first_section" value="S"}
                                    {assign var="first_section_text" value=__("shipping_address")}
                                    {assign var="sec_section" value="B"}
                                    {assign var="sec_section_text" value=__("billing_address")}
                                    {assign var="body_id" value="ba"}
                                {/if}
                            
                                {include file="views/profiles/components/profile_fields.tpl" 
                                    section=$first_section 
                                    body_id="" 
                                    ship_to_another=true 
                                    title=$first_section_text
                                    nothing_extra=true
                                    hide_clearfix=true
                                    cp_add_disabled=true
                                }
                            
                                {include file="views/profiles/components/profile_fields.tpl" 
                                    section="C" 
                                    body_id="aa"
                                    grid_wrap="cp-actual-address"
                                    hide_clearfix=true
                                    nothing_extra=true
                                    exclude=fn_cp_checkout_modifications_get_exclude_fields_by_section($profile_fields, $smarty.const.CP_SECTION_ACTUAL_ADDRESS) 
                                    actual_address_flag=true 
                                    actual_same_as_billing=true
                                }
                        {/if}
                    </div>

                    <div class="cp-section-profile-add cp-addition-info-company-section cp-emerging-section hidden" id="section_profile_add_3">
                        <h2 class="ty-account__title-section">{__("cp_addition_company_info")}</h2>
                        {if $profile_fields.B || $profile_fields.S}
                            {include file="views/profiles/components/profile_fields.tpl" 
                                section="C" 
                                nothing_extra=true
                                hide_clearfix=true
                                grid_wrap="cp-section-profile-add"
                                exclude=fn_cp_checkout_modifications_get_exclude_fields_by_section($profile_fields, $smarty.const.CP_SECTION_ADDITION_INFO)
                            }
                        {/if}
                    </div>

                    {* <div class="cp-emerging-section hidden">
                    {hook name="profiles:account_update"}
                    {/hook}

                    {include file="common/image_verification.tpl" option="register" align="center"}
                    </div> *}

                {/capture}
                {$smarty.capture.group nofilter}

                <div class="ty-profile-field__buttons buttons-container cp-emerging-section hidden">
                    {if $runtime.mode == "add"}
                        <div class="cp-notice__profile-add">
                            <span class="icon-spl-warning"></span>
                            {__("cp_notice_profile_add")}
                        </div>
                        <div class="cp-pagination-profile-add" >
                            <span class="cp-pagination-profile-add__back ty-btn ty-btn__tertiary ty-btn__pagination-profile-add hidden" data-ca-numder-section>{__("back")}</span>
                            <span class="cp-pagination-profile-add__farther ty-btn ty-btn__secondary ty-btn__pagination-profile-add" data-ca-numder-section="1">{__("farther")}</span>
                        </div>
                        {include file="buttons/register_profile.tpl"
                            but_name="dispatch[{$dispatch}]" 
                            but_id="save_profile_but"
                            but_text=__("complete_registration") 
                            but_meta="ty-btn__primary ty-btn__profile-add"
                        }
                    {else}
                        {include file="buttons/save.tpl" but_name="dispatch[{$dispatch}]" but_meta="ty-btn__secondary" but_id="save_profile_but"}
                        <input class="ty-profile-field__reset ty-btn ty-btn__tertiary" type="reset" name="reset" value="{__("revert")}" id="shipping_address_reset"/>

                        <script type="text/javascript">
                        (function(_, $) {
                            var address_switch = $('input:radio:checked', '.ty-address-switch');
                            $("#shipping_address_reset").on("click", function(e) {
                                setTimeout(function() {
                                    address_switch.click();
                                }, 50);
                            });
                        }(Tygh, Tygh.$));
                        </script>
                    {/if}
                </div>
            </form>
        </div>
        
        {capture name="additional_tabs"}
            {if $runtime.mode == "update"}
                {if !"ULTIMATE:FREE"|fn_allowed_for}
                    {if $usergroups && !$user_data|fn_check_user_type_admin_area}
                    <div id="content_usergroups">
                        <table class="ty-table">
                        <tr>
                            <th style="width: 30%">{__("usergroup")}</th>
                            <th style="width: 30%">{__("status")}</th>
                            {if $settings.General.allow_usergroup_signup == "Y"}
                                <th style="width: 40%">{__("action")}</th>
                            {/if}
                        </tr>
                        {foreach from=$usergroups item=usergroup}
                            {if $user_data.usergroups[$usergroup.usergroup_id]}
                                {assign var="ug_status" value=$user_data.usergroups[$usergroup.usergroup_id].status}
                            {else}
                                {assign var="ug_status" value="F"}
                            {/if}
                            {if $settings.General.allow_usergroup_signup == "Y" || $settings.General.allow_usergroup_signup != "Y" && $ug_status == "A"}
                                <tr>
                                    <td>{$usergroup.usergroup}</td>
                                    <td class="ty-center">
                                        {if $ug_status == "A"}
                                            {__("active")}
                                            {assign var="_link_text" value=__("remove")}
                                            {assign var="_req_type" value="cancel"}
                                        {elseif $ug_status == "F"}
                                            {__("available")}
                                            {assign var="_link_text" value=__("join")}
                                            {assign var="_req_type" value="join"}
                                        {elseif $ug_status == "D"}
                                            {__("declined")}
                                            {assign var="_link_text" value=__("join")}
                                            {assign var="_req_type" value="join"}
                                        {elseif $ug_status == "P"}
                                            {__("pending")}
                                            {assign var="_link_text" value=__("cancel")}
                                            {assign var="_req_type" value="cancel"}
                                        {/if}
                                    </td>
                                    {if $settings.General.allow_usergroup_signup == "Y"}
                                        <td>
                                            <a class="cm-ajax" data-ca-target-id="content_usergroups" href="{"profiles.usergroups?usergroup_id=`$usergroup.usergroup_id`&type=`$_req_type`"|fn_url}">{$_link_text}</a>
                                        </td>
                                    {/if}
                                </tr>
                            {/if}
                        {/foreach}
                        </table>
                    <!--content_usergroups--></div>
                    {/if}
                {/if}

                {hook name="profiles:tabs"}
                {/hook}
            {/if}
        {/capture}

        {$smarty.capture.additional_tabs nofilter}

    {/capture}

    {if $smarty.capture.additional_tabs|trim != ""}
        {include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}
    {else}
        {$smarty.capture.tabsbox nofilter}
    {/if}

    {capture name="mainbox_title"}{__("profile_details")}{/capture}
{else}
    {capture name="tabsbox"}
        <div class="ty-profile-field ty-account form-wrap" id="content_general">
            <form name="profile_form" enctype="multipart/form-data" action="{""|fn_url}" method="post">
                <input id="selected_section" type="hidden" value="general" name="selected_section"/>
                <input id="default_card_id" type="hidden" value="" name="default_cc"/>
                <input type="hidden" name="profile_id" value="{$user_data.profile_id}" />

                {if $smarty.request.return_url}
                    <input type="hidden" name="return_url" value="{$smarty.request.return_url}" />
                {/if}

                {capture name="group"}
                    
                    {include file="views/profiles/components/profiles_account.tpl"}
                    
                    {include file="views/profiles/components/profile_fields.tpl" section="C" title=__("cp_contact_information") exclude=fn_cp_checkout_modifications_get_exclude_fields_by_section($profile_fields, $smarty.const.CP_SECTION_CONTACT_INFO)}
                   
                    {if $profile_fields.B || $profile_fields.S}
                        {if $settings.General.user_multiple_profiles == "Y" && $runtime.mode == "update"}
                            <p>{__("text_multiprofile_notice")}</p>
                            {include file="views/profiles/components/multiple_profiles.tpl" profile_id=$user_data.profile_id}    
                        {/if}
                        
                        {$settings.Checkout.address_position = "billing_first"}

                        {if $settings.Checkout.address_position == "billing_first"}
                            {assign var="first_section" value="B"}
                            {assign var="first_section_text" value=__("billing_address")}
                            {assign var="sec_section" value="S"}
                            {assign var="sec_section_text" value=__("shipping_address")}
                            {assign var="body_id" value="sa"}
                        {else}
                            {assign var="first_section" value="S"}
                            {assign var="first_section_text" value=__("shipping_address")}
                            {assign var="sec_section" value="B"}
                            {assign var="sec_section_text" value=__("billing_address")}
                            {assign var="body_id" value="ba"}
                        {/if}
                        
                        {include file="views/profiles/components/profile_fields.tpl" section=$first_section body_id="" ship_to_another=true title=$first_section_text}
                        
                        {include file="views/profiles/components/profile_fields.tpl" section="C" body_id="aa" title=__("cp_actual_address") exclude=fn_cp_checkout_modifications_get_exclude_fields_by_section($profile_fields, $smarty.const.CP_SECTION_ACTUAL_ADDRESS) actual_address_flag=true actual_same_as_billing=true}
                        
                        {include file="views/profiles/components/profile_fields.tpl" section="C" title=__("cp_addition_company_info") exclude=fn_cp_checkout_modifications_get_exclude_fields_by_section($profile_fields, $smarty.const.CP_SECTION_ADDITION_INFO)}
                    {/if}

                    {hook name="profiles:account_update"}
                    {/hook}

                    {include file="common/image_verification.tpl" option="register" align="center"}

                {/capture}
                {$smarty.capture.group nofilter}

                <div class="ty-profile-field__buttons buttons-container">
                    {if $runtime.mode == "add"}
                        {include file="buttons/register_profile.tpl" but_name="dispatch[{$dispatch}]" but_id="save_profile_but"}
                    {else}
                        {include file="buttons/save.tpl" but_name="dispatch[{$dispatch}]" but_meta="ty-btn__secondary" but_id="save_profile_but"}
                        <input class="ty-profile-field__reset ty-btn ty-btn__tertiary" type="reset" name="reset" value="{__("revert")}" id="shipping_address_reset"/>

                        <script type="text/javascript">
                        (function(_, $) {
                            var address_switch = $('input:radio:checked', '.ty-address-switch');
                            $("#shipping_address_reset").on("click", function(e) {
                                setTimeout(function() {
                                    address_switch.click();
                                }, 50);
                            });
                        }(Tygh, Tygh.$));
                        </script>
                    {/if}
                </div>
            </form>
        </div>
        
        {capture name="additional_tabs"}
            {if $runtime.mode == "update"}
                {if !"ULTIMATE:FREE"|fn_allowed_for}
                    {if $usergroups && !$user_data|fn_check_user_type_admin_area}
                    <div id="content_usergroups">
                        <table class="ty-table">
                        <tr>
                            <th style="width: 30%">{__("usergroup")}</th>
                            <th style="width: 30%">{__("status")}</th>
                            {if $settings.General.allow_usergroup_signup == "Y"}
                                <th style="width: 40%">{__("action")}</th>
                            {/if}
                        </tr>
                        {foreach from=$usergroups item=usergroup}
                            {if $user_data.usergroups[$usergroup.usergroup_id]}
                                {assign var="ug_status" value=$user_data.usergroups[$usergroup.usergroup_id].status}
                            {else}
                                {assign var="ug_status" value="F"}
                            {/if}
                            {if $settings.General.allow_usergroup_signup == "Y" || $settings.General.allow_usergroup_signup != "Y" && $ug_status == "A"}
                                <tr>
                                    <td>{$usergroup.usergroup}</td>
                                    <td class="ty-center">
                                        {if $ug_status == "A"}
                                            {__("active")}
                                            {assign var="_link_text" value=__("remove")}
                                            {assign var="_req_type" value="cancel"}
                                        {elseif $ug_status == "F"}
                                            {__("available")}
                                            {assign var="_link_text" value=__("join")}
                                            {assign var="_req_type" value="join"}
                                        {elseif $ug_status == "D"}
                                            {__("declined")}
                                            {assign var="_link_text" value=__("join")}
                                            {assign var="_req_type" value="join"}
                                        {elseif $ug_status == "P"}
                                            {__("pending")}
                                            {assign var="_link_text" value=__("cancel")}
                                            {assign var="_req_type" value="cancel"}
                                        {/if}
                                    </td>
                                    {if $settings.General.allow_usergroup_signup == "Y"}
                                        <td>
                                            <a class="cm-ajax" data-ca-target-id="content_usergroups" href="{"profiles.usergroups?usergroup_id=`$usergroup.usergroup_id`&type=`$_req_type`"|fn_url}">{$_link_text}</a>
                                        </td>
                                    {/if}
                                </tr>
                            {/if}
                        {/foreach}
                        </table>
                    <!--content_usergroups--></div>
                    {/if}
                {/if}

                {hook name="profiles:tabs"}
                {/hook}
            {/if}
        {/capture}

        {$smarty.capture.additional_tabs nofilter}

    {/capture}

    {if $smarty.capture.additional_tabs|trim != ""}
        {include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}
    {else}
        {$smarty.capture.tabsbox nofilter}
    {/if}

    {capture name="mainbox_title"}{__("profile_details")}{/capture}
{/if}