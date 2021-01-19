(function (_, $) {
    'use strict';

    $(_.doc).ready(function () {
        function comeBack() {
            $(_.doc).on('click', '.cp-btn-return', function(e) {
                var idBlock = $(this).data('caId');
                var idSwBlock = '#sw_' + idBlock;

                $('#' + idBlock).addClass('hidden');
                $(idSwBlock).removeClass('open');
            }); 
        } 

        function personalAreaFooter() {
            $(_.doc).on('click', '#btn_personal_area_footer', function(e) {
                var personalAreaHeader = $('.top-my-account');
                var top = $('.tygh-header').offset().top;

                $('body,html').animate({scrollTop: top}, 800, 'linear');

                personalAreaHeader.find('.ty-dropdown-box__title').addClass('open');
                personalAreaHeader.find('.ty-dropdown-box__content').css('display', 'block');
            }); 
        } 

        function MobileMenu() {
            $(_.doc).on('click', '.cp-mobile-menu__header', function(e) {     
               var mobileMenu = $(this).parents('.cp-mobile-menu');
               mobileMenu.find('.cp-mobile-menu__background').removeClass('hidden');
               mobileMenu.find('.cp-mobile-menu__content').addClass('open');
               $('body').css('overflow', 'hidden');
            }); 

            $(_.doc).on('mouseup', function (e){ 
                var menuContent = $('.cp-mobile-menu__content');
                var menuBackground = $('.cp-mobile-menu__background');
                if (!menuContent.is(e.target) // если клик был не по нашему блоку
                    && menuContent.has(e.target).length === 0) { // и не по его дочерним элементам
                        menuContent.removeClass('open');
                        menuBackground.addClass('hidden');
                        $('body').css('overflow', '');
                }else if ($(e.target).hasClass('cp-mobile-menu__close')){
                    menuContent.removeClass('open');
                    menuBackground.addClass('hidden');
                    $('body').css('overflow', '');
                }
            });
        } 

        function pseudoSwitch() {
            $(_.doc).on('click', '#pseudo_switch_address', function(e) {   
                var yes = $('#sw_aa_suffix_yes');
                var no = $('#sw_aa_suffix_no');
                if (!$(this).hasClass('disabled')) {
                    if ($('.ty-address-switch').length) {
                        if ($(this).is(':checked')) {
                            if(!yes.is(':checked')) {
                                yes.trigger('click');
                            }
                        } else {
                            no.trigger('click');
                        }
                    }
                }
            }); 
        }

        function mobileProfileAdd() {
            if ($(_.doc).width() < 768) {
                $('.ty-control-group_disabled input').prop('disabled', true);
                $('#pseudo_switch_address').addClass('disabled');
            }
        }

        function paginationProfileAdd() {
            var btnFarther = $('.cp-pagination-profile-add__farther');
            var btnBack = $('.cp-pagination-profile-add__back');
            var btn = '.ty-btn__pagination-profile-add';
            $(_.doc).on('click', btn, function(e) {  
                if (!$(this).hasClass('disabled')) {
                    var numderSection = $(this).attr('data-ca-numder-section'),
                        numderSection = +numderSection,
                        idSection = '';

                    //console.log(numderSection);
                    if ($(this).hasClass('cp-pagination-profile-add__farther')) {

                        idSection = '#section_profile_add_' + (numderSection + 1);

                        if (numderSection === 1) {

                            btnFarther.attr('data-ca-numder-section', 2);
                            btnBack.attr('data-ca-numder-section', 1);
                            btnBack.removeClass('hidden');
                            $('.banner-profiles-buyer__desc').addClass('hidden');

                            if (!$(this).hasClass('selected')) {
                                btnBack.addClass('disabled');
                                btnFarther.addClass('disabled');
                            }

                        } else {
                            btnBack.attr('data-ca-numder-section', 2);
                            $('.cp-notice__profile-add').css('display', 'block');
                            btnFarther.addClass('hidden');
                            $('.ty-btn__profile-add').css('display', 'block');
                            $('.ty-profile-field__buttons.cp-emerging-section').prev('.cp-emerging-section').removeClass('hidden');
                        }
    
                    } else if ($(this).hasClass('cp-pagination-profile-add__back')) {

                        idSection = '#section_profile_add_' + numderSection;

                        if (numderSection === 1) {

                            btnFarther.attr('data-ca-numder-section', numderSection);
                            btnBack.addClass('hidden');
                            $('.banner-profiles-buyer__desc').removeClass('hidden');

                        } else {

                            btnBack.attr('data-ca-numder-section', 1);
                            btnFarther.removeClass('hidden');
                            $('.cp-notice__profile-add').css('display', 'none');
                            $('.ty-btn__profile-add').css('display', 'none');
                            $('.ty-profile-field__buttons.cp-emerging-section').prev('.cp-emerging-section').addClass('hidden');

                        }
                    }

                    $('.cp-section-profile-add.active').removeClass('active');
                    
                    $(idSection).addClass('active');
                }
            });
        }

        function clearSearch() {
            $(_.doc).on('input', '.ty-search-block .ty-search-block__input', function () {
                var inputSearch = $(this),
                    value = inputSearch.val(),
                    btnClear = inputSearch.parent().find('.ty-btn__clear-search');
                    
                if (value.length > 0) {
                    btnClear.removeClass('hidden');
                } else {
                    btnClear.addClass('hidden');
                }
            });

            $(_.doc).on('click', '.ty-btn__clear-search', function(e) {     
                var btnClear = $(this),
                    inputSearch = btnClear.parent().find('.ty-search-block__input'),
                    value = inputSearch.val();

                if (value.length > 0) {
                    inputSearch.val('');
                    btnClear.addClass('hidden');
                }
            }); 

        }
        
        function showMore() {
            $(_.doc).on('click', '.ty-features-all__item-show-more', function(e) {  
                var $this = $(this); 
                if ($this.hasClass('hide')) {
                    $(this).prev('.ty-features-all__item-desc_wrap').css('height', '');
                } else {
                    var heightBlock = $(this).prev('.ty-features-all__item-desc_wrap').attr('data-ca-height-block');
                    $(this).prev('.ty-features-all__item-desc_wrap').css('height', heightBlock + 'px');

                } 
                $(this).toggleClass('hide');
            }); 
        }

        $(_.doc).on('click', '.notification-container_order-completed .cm-notification-close', function(e) {     
            $(this).parents('.notification-container_order-completed').removeClass('notification-container_order-completed');
        }); 
        if ($('.cm-notification-container').hasClass('notification-container_order-completed') && $('.cm-notification-container').children().length == 0 ) {
            $('.cm-notification-container').removeClass('notification-container_order-completed');
        }

        $.ceEvent('on', 'ce.dialogshow', function(d, e, u) {
            var withDoc = $(_.doc).width();
            if (d.hasClass('page-popup') && withDoc < 768) {
                $('.ui-widget-overlay').hide();
                $('.tygh-content').hide();
                $('.tygh-footer').hide();
                $('html').removeClass('dialog-is-open');
            }
        });

        $('body').on('click','.ui-widget-overlay', function(e) {
            //console.log(e);
            $('.ui-dialog').filter(function () {
                return $(this).css("display") === "block";
            }).find('.ui-dialog-content').dialog('close');
        });

        $(_.doc).on('click', '.pseudo-button_close .icon-spl-close', function(e) {     
            $(this).parents('.ui-dialog').find('.ui-dialog-titlebar-close').trigger('click');
        }); 

        function aboutServiceAdvantagesСarousel () {
            var bodyWidth = $(_.doc).width();
            if (bodyWidth < 768 && $('.about-service-advantages').length) {
                $('.about-service-advantages').not('.slick-initialized').slick({
                    infinite: false,
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    dots: true,
                    arrows: false,
                    responsive: [
                        {
                        breakpoint: 767,
                            settings: {
                                slidesToShow: 1
                            }
                        }
                    ]
                });
            }
        }
        function earnPageAccordion () {
            var elm = $('#earn_page_faq_list');
            if (elm.length) {
                elm.accordion({
                    collapsible: true,
                    heightStyle: "content",
                    animate: 100,
                    header: "> li > h3"
                });
            }
        }
        $(_.doc).on('mouseup', function (e){ 
            var tooltipBox = $('.cp-tooltip-box');
            if (!tooltipBox.is(e.target) && tooltipBox.has(e.target).length === 0) {
                tooltipBox.addClass('hidden');
            }
        });
        comeBack();

        personalAreaFooter();

        MobileMenu();

        pseudoSwitch();

        mobileProfileAdd();

        paginationProfileAdd();

        clearSearch();

        showMore();

        aboutServiceAdvantagesСarousel ();

        earnPageAccordion ();
    });

    var added = false;
    $.ceEvent('one', 'ce.commoninit', function(context) {
        if (added === false) {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for(var i = 0; i < hashes.length; i++)
            {
              hash = hashes[i].split('=');
              vars.push(hash[0]);
              vars[hash[0]] = hash[1];
            }
            if (vars.cp_add_to_wishlist != null) {
                added = true;
                if (vars.but_id != null) {
                    var but_id = vars.but_id;
                    history.pushState(null, null, location.href.replace('&cp_add_to_wishlist=1&but_id='+but_id,''));
                    $("#"+but_id).click();
                }
            }
        }
    });

    $('.ty-scroller-list_no-img').parents('.cp-bicolor-background').addClass('cp-bicolor-background_no-img');

})(Tygh, Tygh.$);


function showFieldsProfile() {

    if ($(document).width() > 767) {
        $('.cp-emerging-section').removeClass('hidden');
    } else {
        $('.ty-control-group_disabled input').prop('disabled', false);
        $('#pseudo_switch_address').removeClass('disabled');
        $('.ty-btn__pagination-profile-add').removeClass('disabled');
        $('.ty-btn__pagination-profile-add').addClass('selected');
    }

    $('.ty-control-group.ty-control-group_disabled').removeClass('ty-control-group_disabled');
}
function fn_cp_delete_from_wishlist(wishlist_id, product_id, warehouse_id, cp_location)
{   
    if (cp_location == 'product') {
        var redirect_url = fn_url('products.view&product_id='+product_id+'&warehouse_id='+warehouse_id);
    }else if (cp_location == 'wishlist') {
        var redirect_url = fn_url('wishlist.view');
    }else if (cp_location == 'cart') {
        var redirect_url = fn_url('checkout.cart');
    }
    $.ceAjax('request', fn_url('wishlist.delete'), {
        data: {
            cart_id: wishlist_id,
            result_ids:'add_to_cart_update*',
            redirect_url: redirect_url
        },
        method: 'get',
        callback: function (response) {
            if (cp_location == 'wishlist') {
                location.reload();
            }
        }
    });
}

function showButtonShowMore() {
    $('.ty-features-all__item-desc').each(function() {
        var $this = $(this);
        var heightBlock = $this.height();
        if (heightBlock > 49) {
            $this.parent('.ty-features-all__item-desc_wrap').next('.ty-features-all__item-show-more').removeClass('hidden');
            $this.parent('.ty-features-all__item-desc_wrap').attr('data-ca-height-block', heightBlock);
        }
    });
}

$(document).ready(function () {
    showButtonShowMore();
});