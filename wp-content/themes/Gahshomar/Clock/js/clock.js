jQuery(document).ready(function($) {
    const body = $('body');

    const localClock = $('#local-time');
    const internationalClock = $('#international-time');
    const timezoneSelect = $('#timezone-select');

    function updateClock(clock, date) {
        const hourHand = clock.find('.hour-hand');
        const minuteHand = clock.find('.minute-hand');
        const secondHand = clock.find('.second-hand');
        const digitalClock = clock.find('.digital-clock');

        const hours = date.getHours();
        const minutes = date.getMinutes();
        const seconds = date.getSeconds();

        const hourDegrees = (hours % 12) * 30 + (minutes / 2);
        const minuteDegrees = minutes * 6;
        const secondDegrees = seconds * 6;

        hourHand.css('transform', `rotate(${hourDegrees}deg)`);
        minuteHand.css('transform', `rotate(${minuteDegrees}deg)`);
        secondHand.css('transform', `rotate(${secondDegrees}deg)`);

        digitalClock.text(`${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
    }

    function updateLocalClock() {
        const now = new Date();
        updateClock(localClock, now);
    }

    function updateInternationalClock() {
        const selectedTimezone = timezoneSelect.val() || 'Asia/Tehran';
        const now = new Date().toLocaleString("en-US", { timeZone: selectedTimezone });
        const date = new Date(now);
        updateClock(internationalClock, date);
    }

    updateLocalClock();
    updateInternationalClock();

    setInterval(updateLocalClock, 1000);
    setInterval(updateInternationalClock, 1000);
    timezoneSelect.on('change', updateInternationalClock);
});