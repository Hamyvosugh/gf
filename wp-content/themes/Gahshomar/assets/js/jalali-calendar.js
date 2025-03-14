jQuery(document).ready(function($) {
    const localClock = $('#local-time');
    const internationalClock = $('#international-time');
    const timezoneSelect = $('#timezone-select');

    function updateClocks() {
        const now = new Date();
        localClock.text(now.toLocaleTimeString());
        const selectedTimezone = timezoneSelect.val() || 'Asia/Tehran';
        const internationalNow = new Date().toLocaleString("en-US", { timeZone: selectedTimezone });
        internationalClock.text(new Date(internationalNow).toLocaleTimeString());
        updateCallInfo(new Date(internationalNow));
    }

    function updateCallInfo(time) {
        $.get(`/wp-admin/admin-ajax.php?action=get_jalali_date&timestamp=${time.getTime()}`)
            .done(function(data) {
                const jalaliDate = data.jalali_date;
                // Use jalaliDate in your logic
                console.log('Jalali Date:', jalaliDate);
            })
            .fail(function(error) {
                console.error('Error:', error);
            });

        const hours = time.getHours();
        const day = time.getDay();
        // Your existing logic for call info
    }

    setInterval(updateClocks, 1000);
    timezoneSelect.on('change', updateClocks);
    updateClocks(); // Initial call
});