(function ($) {
    'use strict';

    function closeLightbox(state) {
        if (state.overlay) {
            state.overlay.remove();
        }
        $(document).off('.rsmLightbox');
    }

    function openLightbox(src, alt) {
        var state = {};

        state.overlay = $('<div>', {
            'class': 'rsm-lightbox-overlay',
            'role': 'dialog',
            'aria-modal': 'true'
        });

        state.image = $('<img>', {
            'class': 'rsm-lightbox-image',
            src: src,
            alt: alt || ''
        });

        state.closeBtn = $('<button>', {
            'class': 'rsm-lightbox-close',
            type: 'button',
            'aria-label': (window.rsmLightbox && window.rsmLightbox.i18nClose) ? window.rsmLightbox.i18nClose : 'Close'
        }).text('×');

        state.overlay.append(state.image).append(state.closeBtn);
        $('body').append(state.overlay);

        state.overlay.on('click', function (evt) {
            if (evt.target === this) {
                closeLightbox(state);
            }
        });

        state.closeBtn.on('click', function () {
            closeLightbox(state);
        });

        $(document).on('keyup.rsmLightbox', function (evt) {
            if (27 === evt.keyCode) {
                closeLightbox(state);
            }
        });
    }

    $(function () {
        $(document).on('click', '[data-rsm-lightbox]', function (evt) {
            var $link = $(this);
            var src = $link.attr('href');
            var alt = $link.find('img').attr('alt');

            if (!src) {
                return;
            }

            evt.preventDefault();
            openLightbox(src, alt);
        });
    });
})(jQuery);
