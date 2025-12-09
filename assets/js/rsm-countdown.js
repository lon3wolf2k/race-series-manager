(function(){
    function pad(num) {
        return num < 10 ? '0' + num : '' + num;
    }

    function updateCountdown(container, labels) {
        var targetAttr = container.getAttribute('data-rsm-countdown');
        if (!targetAttr) {
            container.innerHTML = labels.unavailable;
            return;
        }

        var targetDate = new Date(targetAttr);
        if (isNaN(targetDate.getTime())) {
            container.innerHTML = labels.unavailable;
            return;
        }

        var status = container.querySelector('.rsm-countdown__status');
        var dayEl = container.querySelector('[data-countdown-unit="days"]');
        var hourEl = container.querySelector('[data-countdown-unit="hours"]');
        var minuteEl = container.querySelector('[data-countdown-unit="minutes"]');

        function render() {
            var now = new Date();
            var diff = targetDate.getTime() - now.getTime();

            if (diff <= 0) {
                container.classList.add('rsm-countdown--hidden');
                return;
            }

            var days = Math.floor(diff / (1000 * 60 * 60 * 24));
            diff -= days * 1000 * 60 * 60 * 24;
            var hours = Math.floor(diff / (1000 * 60 * 60));
            diff -= hours * 1000 * 60 * 60;
            var minutes = Math.floor(diff / (1000 * 60));

            dayEl.textContent = pad(days);
            hourEl.textContent = pad(hours);
            minuteEl.textContent = pad(minutes);

            if (status) {
                status.textContent = '';
            }
        }

        render();
        setInterval(render, 30000);
    }

    document.addEventListener('DOMContentLoaded', function(){
        var labels = window.rsmCountdown || {
            days: 'Days',
            hours: 'Hours',
            minutes: 'Minutes',
            live: 'Live now',
            unavailable: ''
        };

        var countdowns = document.querySelectorAll('.rsm-countdown');
        countdowns.forEach(function(container){
            updateCountdown(container, labels);
        });
    });
})();
