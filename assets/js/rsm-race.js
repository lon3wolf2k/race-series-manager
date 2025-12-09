(function () {
    function openPrintWindow(title, styles, bodyHtml) {
        var printWindow = window.open('', '', 'width=900,height=700');
        if (!printWindow) {
            return;
        }

        printWindow.document.write('<html><head><title>' + title + '</title>');
        printWindow.document.write('<style>' + styles + '</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h1>' + title + '</h1>');
        printWindow.document.write(bodyHtml);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }

    function handlePrintClick(event) {
        var trigger = event.target.closest('[data-rsm-print-target],[data-rsm-print-images]');
        if (!trigger) {
            return;
        }

        var titleSelector = trigger.getAttribute('data-rsm-print-title');
        var heading = titleSelector ? document.querySelector(titleSelector) : null;
        var printTitle = heading ? heading.textContent.trim() : document.title;

        var tableSelector = trigger.getAttribute('data-rsm-print-target');
        if (tableSelector) {
            var table = document.querySelector(tableSelector);
            if (!table) {
                return;
            }

            event.preventDefault();

            var tableStyles = '\
                body { font-family: Arial, sans-serif; padding: 24px; color: #111; }\
                h1 { font-size: 20px; margin: 0 0 12px; }\
                table { width: 100%; border-collapse: collapse; font-size: 13px; }\
                th, td { border: 1px solid #d1d5db; padding: 8px 10px; text-align: left; }\
                thead th { background: #f3f4f6; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; }\
                @media print { body { padding: 0; } }';

            openPrintWindow(printTitle, tableStyles, table.outerHTML);
            return;
        }

        var imageSelector = trigger.getAttribute('data-rsm-print-images');
        if (imageSelector) {
            var imageContainer = document.querySelector(imageSelector);
            if (!imageContainer) {
                return;
            }

            event.preventDefault();

            var imagesHtml = imageContainer.innerHTML;
            var imageStyles = '\
                body { font-family: Arial, sans-serif; padding: 24px; color: #111; }\
                h1 { font-size: 20px; margin: 0 0 14px; }\
                h3 { font-size: 15px; margin: 0 0 8px; font-weight: 700; color: #111; }\
                .rsm-print-block { margin-bottom: 18px; }\
                .rsm-print-image { max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 10px 26px rgba(0,0,0,0.08); margin: 0 0 6px; }\
                @media print { body { padding: 0; } }';

            openPrintWindow(printTitle, imageStyles, imagesHtml);
        }
    }

    document.addEventListener('click', handlePrintClick);
})();
