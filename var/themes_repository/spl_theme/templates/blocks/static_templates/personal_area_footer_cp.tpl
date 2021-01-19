{if $auth.user_id}
    <a class="ty-btn ty-btn__fourth ty-btn__personal-area" href="{"profiles.update"|fn_url}" rel="nofollow" >{__("personal_area_footer")}</a>
{else}
    <div class="ty-btn ty-btn__fourth ty-btn__personal-area" rel="nofollow"id="btn_personal_area_footer">
        {__("personal_area_footer")}
    </div>
    {* <div  id="login_block{$block.snapping_id}" class="hidden" title="{__("sign_in")}">
        <div class="ty-login-popup">
            {include file="views/auth/login_form.tpl" style="popup" id="popup`$block.snapping_id`"}
        </div>
    </div> *}
{/if}