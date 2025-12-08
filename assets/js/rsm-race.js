(function () {
    function handlePrintClick(event) {
        var trigger = event.target.closest('[data-rsm-print-target]');
        if (!trigger) {
            return;
        }

        var selector = trigger.getAttribute('data-rsm-print-target');
        if (!selector) {
            return;
        }

        var table = document.querySelector(selector);
        if (!table) {
            return;
        }

        event.preventDefault();

        var printWindow = window.open('', '', 'width=900,height=700');
        if (!printWindow) {
            return;
        }

        var heading = document.querySelector(trigger.getAttribute('data-rsm-print-title'));
        var tableTitle = heading ? heading.textContent.trim() : document.title;

        var styles = '\
            body { font-family: Arial, sans-serif; padding: 24px; color: #111; }\
            h1 { font-size: 20px; margin: 0 0 12px; }\
            table { width: 100%; border-collapse: collapse; font-size: 13px; }\
            th, td { border: 1px solid #d1d5db; padding: 8px 10px; text-align: left; }\
            thead th { background: #f3f4f6; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; }\
            @media print { body { padding: 0; } }';

        printWindow.document.write('<html><head><title>' + tableTitle + '</title>');
        printWindow.document.write('<style>' + styles + '</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h1>' + tableTitle + '</h1>');
        printWindow.document.write(table.outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }

    document.addEventListener('click', handlePrintClick);
})();
