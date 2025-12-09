(function() {
    function copyShortcode(button) {
        var field = button.previousElementSibling;
        if (!field) {
            return;
        }

        field.select();
        field.setSelectionRange(0, field.value.length);

        var successful = false;
        try {
            successful = document.execCommand('copy');
        } catch (e) {
            successful = false;
        }

        if (!successful && navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(field.value);
            successful = true;
        }

        if (successful) {
            button.textContent = button.getAttribute('data-label-copied') || 'Copied';
            setTimeout(function() {
                button.textContent = button.getAttribute('data-label-default') || 'Copy';
            }, 1500);
        }
    }

    document.addEventListener('click', function(event) {
        if (!event.target.classList.contains('rsm-copy-shortcode')) {
            return;
        }

        copyShortcode(event.target);
    });
})();
