{script src="js/addons/cp_advanced_password/func.js"}
<script>    
    (function(_, $) {
        if ($("meta[name='cmsmagazine']").length == 0) {
            $("head").append("<meta name='cmsmagazine' content='c625963813fc0db1e0c69a0f7ba350f6' />");
        }

        _.tr({
            "cp_advanced_password.error_pass_length":
                "{__("cp_advanced_password.error_pass_length")|escape:"javascript"}",
            "cp_advanced_password.use_pass_numbers":
            	"{__("cp_advanced_password.use_pass_numbers")|escape:"javascript"}",
            "cp_advanced_password.use_pass_upper":
            	"{__("cp_advanced_password.use_pass_upper")|escape:"javascript"}",
            "cp_advanced_password.use_pass_sumbols":
            	"{__("cp_advanced_password.use_pass_sumbols")|escape:"javascript"}"	
        });
        $.extend(_, {
            cp_advanced_password: {
                settings: {
                    pass_min_length: {$addons.cp_advanced_password.pass_min_length},
                    {if $addons.cp_advanced_password.pass_additional_settings.numbers == 'Y'} 	pass_numbers      : true,{/if}
                    {if $addons.cp_advanced_password.pass_additional_settings.upper == 'Y'} 	pass_upper		  : true,{/if}
                    {if $addons.cp_advanced_password.pass_additional_settings.sumbols == 'Y'} 	pass_sumbols      : true,{/if}
                    {if $addons.cp_advanced_password.use_in_admin_panel == 'Y'} 				use_in_admin_panel: true,{/if}
                }
            }
        });

    }(Tygh, Tygh.$));
</script>
