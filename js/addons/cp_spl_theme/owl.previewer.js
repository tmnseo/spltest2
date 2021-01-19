/* previewer-description:text_owl */
(function(_, $) {
    if ($().owlCarousel == undefined) {
        $.getScript('js/lib/owlcarousel/owl.carousel.min.js');
    }

    $.cePreviewer('handlers', {
        display: function (elm) {
            //let needImageID = $(elm[0]).find('img').attr('id')
            var imageId = elm.data('caImageId');
            var elms = $('a[data-ca-image-id="' + imageId + '"] img');

            var previewer = $('<div class="ty-owl-previewer"></div>');
            var previewerContainer = $('<div class="ty-owl-previewer__container owl-carousel"></div>');

            elms.each(function (index, elm) {
                // if ($(elm).attr('id') != needImageID){
                //     return true;
                // }
                var _clonedNode = $(elm).clone(),
                    _imageContainer = $('<div class="ty-owl-previewer__image--flex-fix-wrapper"></div>');
                _clonedNode.toggleClass('ty-owl-previewer__image');

                _clonedNode.attr('srcset', '');
                _clonedNode.attr('src', $(elm).parent('a').attr('href') || _clonedNode.attr('src'));

                _clonedNode.appendTo(_imageContainer);
                _imageContainer.appendTo(previewerContainer);
            });

            previewerContainer.appendTo(previewer);
            previewer.appendTo(_.body);

            var _scrollPosition = $(document).scrollTop();
            previewer.ceDialog('open', {
                dialogClass: 'ty-owl-previewer__dialog_vendor_certificate',
                onClose: function () {
                    setTimeout(function () {
                        $.ceDialog('get_last').ceDialog('reload');
                    }, 0);
                    previewer.remove();

                    // unset scroll-prevent styles
                    $(_.body).css({
                        maxHeight: ''
                    });

                }
            });

            // set scroll-prevent styles (no Y-scroll when images slides)
            $(_.body).css({
                maxHeight: '100vh'
            });

            previewerContainer.owlCarousel({
                direction: _.language_direction,
                singleItem: true,
                slideSpeed: 100,
                autoPlay: false,
                stopOnHover: true,
                pagination: false,
                navigation: false
            });

            $('.owl-item', previewerContainer).toggleClass('ty-owl-previewer__image-container owl-previewer__image-container');

            previewerContainer.trigger('owl.goTo', $(elm).data('caImageOrder') || 0);
        }
    });
}(Tygh, Tygh.$));