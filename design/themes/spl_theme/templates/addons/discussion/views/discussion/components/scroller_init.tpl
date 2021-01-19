{script src="js/lib/owlcarousel/owl.carousel.min.js"}
<script type="text/javascript">
(function(_, $) {
    $.ceEvent('on', 'ce.commoninit', function(context) {
        var elm = context.find('#discussion_list_{$block.block_id}');

        function outsideNav () {
            if(this.options.items >= this.itemsAmount){
                $("#owl_outside_nav_{$block.block_id}").hide();
            } else {
                $("#owl_outside_nav_{$block.block_id}").show();
            }
        }

        if (elm.length) {
            elm.owlCarousel({
                direction: '{$language_direction}',
                items: 2,
                itemsDesktop: [1199, 2],
                itemsDesktopSmall: [979, 1],
                itemsTablet: [768, 1],
                itemsMobile: [479, 1],
                scrollPerPage: true,
                autoPlay: false,
                pagination: false,
                afterInit: outsideNav,
                afterUpdate : outsideNav
            });

            $('{$prev_selector}').click(function(){
            elm.trigger('owl.prev');
            });
            $('{$next_selector}').click(function(){
            elm.trigger('owl.next');
            });
        }
    });
}(Tygh, Tygh.$));
</script>