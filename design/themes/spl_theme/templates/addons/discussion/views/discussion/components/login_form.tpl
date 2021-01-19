<div id="new_discussion_post_login_form_popup" title="{__("sign_in")}">
    <div class="ty-login-popup">
        <h3>{__("discussion.please_log_in_to_write_a_review")}</h3>
        {include file="views/auth/login_form.tpl"  id="buyer_login_`$block.snapping_id`" title=__("authorization") hide_remember=true show_register=true hide_label=true show_restore_btn=true dispatch="profiles.add" is_vendor="N"}
    </div>
<!--new_discussion_post_login_form_popup--></div>
