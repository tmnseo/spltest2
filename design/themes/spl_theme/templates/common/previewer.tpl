{if $settings.Appearance.default_image_previewer == "owl"}
    {script src="js/addons/cp_spl_theme/owl_product.previewer.js"}
{else}
    {script src="js/tygh/previewers/`$settings.Appearance.default_image_previewer`.previewer.js"}
{/if}