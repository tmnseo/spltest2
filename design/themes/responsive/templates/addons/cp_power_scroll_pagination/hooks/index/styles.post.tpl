{style src="addons/cp_power_scroll_pagination/styles.css"}

{if $addons.cp_power_scroll_pagination.hide_items_per_page_on_desktop == 'Y'}
    <style>
    @media (min-width: 1200px) {
        #sw_elm_pagination_steps {
            display: none;
        }
    }
    </style>
{/if}

{if $addons.cp_power_scroll_pagination.hide_items_per_page_on_tablet == 'Y'}
    <style>
    @media (min-width: 767px) and (max-width: 1200px) {
        #sw_elm_pagination_steps {
            display: none;
        }
    }
    </style>
{/if}

{if $addons.cp_power_scroll_pagination.hide_items_per_page_on_mobile == 'Y'}
    <style>
    @media (max-width: 767px) {
        #sw_elm_pagination_steps {
            display: none;
        }
    }
    </style>
{/if}
