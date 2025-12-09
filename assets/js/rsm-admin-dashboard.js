(function () {
    const tabs = document.querySelectorAll('.rsm-tab-button');
    const panels = document.querySelectorAll('.rsm-dashboard-panel');

    if (!tabs.length || !panels.length) {
        return;
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-rsm-tab');

            tabs.forEach((btn) => {
                btn.classList.toggle('is-active', btn === tab);
                btn.setAttribute('aria-selected', btn === tab ? 'true' : 'false');
            });

            panels.forEach((panel) => {
                const isMatch = panel.id === `rsm-tab-${target}`;
                panel.classList.toggle('is-active', isMatch);
                panel.setAttribute('aria-hidden', isMatch ? 'false' : 'true');
            });
        });
    });
})();
