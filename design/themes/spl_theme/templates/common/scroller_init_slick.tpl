{script src="js/addons/cp_spl_theme/lib/slick.min.js"}
<script type="text/javascript">
(function(_, $) {
    $.ceEvent('on', 'ce.commoninit', function(context) {
        var elm = context.find('#scroll_list_{$block.block_id}');

        $('.ty-float-left:contains(.ty-scroller-list),.ty-float-right:contains(.ty-scroller-list)').css('width', '100%');

        var item = {$block.properties.item_quantity|default:5},
            // default setting of carousel
            itemsDesktop = 4,
            itemsDesktopSmall = 3;
            itemsTablet = 2;

        if (item > 3) {
            itemsDesktop = item;
            itemsDesktopSmall = item - 1;
            itemsTablet = item - 2;
        } else if (item == 1) {
            itemsDesktop = itemsDesktopSmall = itemsTablet = 1;
        } else {
            itemsDesktop = item;
            itemsDesktopSmall = itemsTablet = item - 1;
        }

        if (elm.length) {
            elm.not('.slick-initialized').slick({
                infinite: false,
                slidesToShow: itemsDesktop,
                {if $block.properties.scroll_per_page == "Y"}
                slidesToScroll: itemsDesktop,
                {else}
                slidesToScroll: 1,
                {/if}
                {if $block.properties.not_scroll_automatically == "Y"}
                autoplay: false,
                {/if}
                LazyLoad: true,
                autoplaySpeed: {$block.properties.speed|default:400},
                pauseOnHover: true,
                {if $block.properties.outside_navigation == "N"}
                arrows: true,
                {/if}
                dots: false,
                responsive: [
                    {
                    breakpoint: 1199,
                        settings: {
                            slidesToShow: itemsDesktop,
                            {if $block.properties.scroll_per_page == "Y"}
                            slidesToScroll: itemsDesktop
                            {else}
                            slidesToScroll: 1
                            {/if}
                        }
                    },
                    {
                    breakpoint: 979,
                        settings: {
                            slidesToShow: itemsDesktopSmall,
                            {if $block.properties.scroll_per_page == "Y"}
                            slidesToScroll: itemsDesktopSmall
                            {else}
                            slidesToScroll: 1
                            {/if}
                        }
                    },
                    {
                    breakpoint: 767,
                        settings: {
                            slidesToShow: 1,
                            dots: false,
                            arrows: false,
                            infinite: false,
                            variableWidth: true
                        }
                    }
                ]
            });
        }
    });
}(Tygh, Tygh.$));
</script>
