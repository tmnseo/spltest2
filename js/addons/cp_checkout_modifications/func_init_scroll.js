(function(_, $) {
    function orderSideBar() {
        var header = $('.tygh-top-panel'),
            sideBar = $('.cp-order-info__sidebar'),
            heightHeader = header.height(),
            positionBlock = sideBar.position().left;

            sideBar.css({'left': positionBlock});
            if ($("div").is(".cp-order-info__sidebar")) {
                $(window).scroll(function(){ 
                    if ($(document).scrollTop() > (heightHeader + 150)) {
                        sideBar.css({
                                    'position': 'fixed',
                                    'top': '-140px'
                                    });
                    }else{
                        sideBar.css({
                                    'position': 'static',
                                    });
                    }
                });
                $(window).resize(function() {
                    positionBlock = sideBar.position().left;
                    sideBar.css({'left': positionBlock});
                });
            }
    }
    function scrollInit() {
        $(".cp-order-info__product-list").mCustomScrollbar({
            theme:"dark-3",
            alwaysShowScrollbar: 0
        });
    }
    scrollInit();

    $(document).ready(function() {
        orderSideBar();
    });

}(Tygh, Tygh.$));