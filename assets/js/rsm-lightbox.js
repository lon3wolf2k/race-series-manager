(function ($) {
    'use strict';

    function closeLightbox(state) {
        if (state.overlay) {
            state.overlay.remove();
        }
        $(document).off('.rsmLightbox');
    }

    function openLightbox(items, startIndex) {
        var state = {
            items: items,
            index: startIndex || 0
        };

        state.overlay = $('<div>', {
            'class': 'rsm-lightbox-overlay',
            'role': 'dialog',
            'aria-modal': 'true'
        });

        state.image = $('<img>', {
            'class': 'rsm-lightbox-image'
        });

        state.closeBtn = $('<button>', {
            'class': 'rsm-lightbox-close',
            type: 'button',
            'aria-label': (window.rsmLightbox && window.rsmLightbox.i18nClose) ? window.rsmLightbox.i18nClose : 'Close'
        }).text('×');

        state.overlay.append(state.image).append(state.closeBtn);

        if (state.items.length > 1) {
            state.prevBtn = $('<button>', {
                'class': 'rsm-lightbox-nav rsm-lightbox-prev',
                type: 'button',
                'aria-label': (window.rsmLightbox && window.rsmLightbox.i18nPrev) ? window.rsmLightbox.i18nPrev : 'Previous'
            }).text('‹');

            state.nextBtn = $('<button>', {
                'class': 'rsm-lightbox-nav rsm-lightbox-next',
                type: 'button',
                'aria-label': (window.rsmLightbox && window.rsmLightbox.i18nNext) ? window.rsmLightbox.i18nNext : 'Next'
            }).text('›');

            state.overlay.append(state.prevBtn).append(state.nextBtn);
        }

        $('body').append(state.overlay);

        function updateImage(newIndex) {
            if (newIndex < 0) {
                newIndex = state.items.length - 1;
            }
            if (newIndex >= state.items.length) {
                newIndex = 0;
            }

            state.index = newIndex;

            var current = state.items[state.index];
            state.image.attr({
                src: current.src,
                alt: current.alt || ''
            });
        }

        state.overlay.on('click', function (evt) {
            if (evt.target === this) {
                closeLightbox(state);
            }
        });

        state.closeBtn.on('click', function () {
            closeLightbox(state);
        });

        if (state.items.length > 1) {
            state.prevBtn.on('click', function (evt) {
                evt.stopPropagation();
                updateImage(state.index - 1);
            });

            state.nextBtn.on('click', function (evt) {
                evt.stopPropagation();
                updateImage(state.index + 1);
            });
        }

        $(document).on('keyup.rsmLightbox', function (evt) {
            if (27 === evt.keyCode) {
                closeLightbox(state);
                return;
            }

            if (state.items.length > 1) {
                if (37 === evt.keyCode) {
                    updateImage(state.index - 1);
                } else if (39 === evt.keyCode) {
                    updateImage(state.index + 1);
                }
            }
        });

        updateImage(state.index);
    }

    $(function () {
        $(document).on('click', '[data-rsm-lightbox]', function (evt) {
            var $link = $(this);
            var group = $link.data('rsm-lightbox');
            var $items = $('[data-rsm-lightbox="' + group + '"]');

            var items = $items.map(function () {
                var $el = $(this);
                return {
                    src: $el.attr('href'),
                    alt: $el.find('img').attr('alt')
                };
            }).get();

            var startIndex = $items.index($link);

            if (!items.length || startIndex < 0) {
                return;
            }

            evt.preventDefault();
            openLightbox(items, startIndex);
        });
    });
})(jQuery);
