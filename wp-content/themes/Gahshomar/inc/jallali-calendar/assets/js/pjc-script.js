jQuery(document).ready(function($) {
    function getBrowserCurrentDate() {
        let currentDate = new Date();
        let jalaliDate = moment(currentDate).format('jYYYY/jM/jD');
        return jalaliDate;
    }

    // Regularly check to update the calendar based on browser time
    setInterval(function() {
        let currentDate = new Date();
        let currentHours = currentDate.getHours();
        let currentMinutes = currentDate.getMinutes();

        if (currentHours === 0 && currentMinutes === 0) {
            updateCalendar($('#pjc-month-select').val(), $('#pjc-year-select').val());
        }
    }, 60000);

    // Ensure the script runs exactly at midnight and then re-checks every 24 hours
    function scheduleMidnightUpdate() {
        let now = new Date();
        let nextMidnight = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 0, 0, 0);
        let timeUntilMidnight = nextMidnight - now;

        setTimeout(function() {
            updateCalendar($('#pjc-month-select').val(), $('#pjc-year-select').val());

            // Schedule updates every 24 hours after the first midnight update
            setInterval(function() {
                updateCalendar($('#pjc-month-select').val(), $('#pjc-year-select').val());
            }, 24 * 60 * 60 * 1000); // 24 hours in milliseconds

        }, timeUntilMidnight);
    }

    scheduleMidnightUpdate();

    var currentMonth = $('#pjc-month-select').val();
    var currentYear = $('#pjc-year-select').val();

    // Dropdown event listener to update calendar dynamically
    $('#pjc-month-select, #pjc-year-select').on('change', function() {
        updateCalendar($('#pjc-month-select').val(), $('#pjc-year-select').val());
    });

    // Button to return to the current month
// Button to return to the current month
$('#pjc-return-button').on('click', function() {
    $.ajax({
        url: pjcAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'get_current_jalali'
        },
        success: function(response) {
            if(response.success) {
                $('#pjc-month-select').val(response.data.month);
                $('#pjc-year-select').val(response.data.year);
                updateCalendar(response.data.month, response.data.year);
            }
        }
    });
});

    // Function to update the calendar using AJAX
    function updateCalendar(month, year) {
        $.ajax({
            url: pjcAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'pjc_update_calendar',
                month: month,
                year: year
            },
            success: function(response) {
                if (response.success) {
                    $('#pjc-calendar-content').html(response.data);
                    highlightCurrentDay(); // Assuming this is a function that highlights 'today'
                    bindDayClickEvents(); // Make sure this doesn't bind handlers more than once
                } else {
                    console.error("Error updating calendar:", response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed:", error);
            }
        });
    }

    // Click event on day elements to show back card and fetch events from the database
    // بخش جدید برای بایند کردن رویداد کلیک به روزهای تقویم
    // Function to update the calendar using AJAX
    function updateCalendar(month, year) {
        $.ajax({
            url: pjcAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'pjc_update_calendar',
                month: month,
                year: year
            },
            success: function(response) {
                if (response.success) {
                    $('#pjc-calendar-content').html(response.data);
                    // Ensure event clicks are re-bound after updating the calendar
                    bindDayClickEvents();
                } else {
                    console.error("Error updating calendar:", response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed:", error);
            }
        });
    }

    // Click event on day elements to show back card and fetch events from the database
    // Click event on day elements to show back card and fetch events from the database
    function bindDayClickEvents() {
        $('.pjc-calendar-table td[data-day]').off('click').on('click', function() {
            const day = $(this).data('day');
            const month = $('#pjc-month-select').val();
            const year = $('#pjc-year-select').val();

            // درخواست AJAX برای دریافت اطلاعات روز انتخاب شده از دیتابیس
            $.ajax({
                url: pjcAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'pjc_get_day_events',
                    day: day,
                    month: month,
                    year: year
                },
                success: function(response) {
                    let eventContent;

                    if (response.success && response.data.trim() !== '') {
                        // تبدیل اعداد به فارسی
                        eventContent = response.data.replace(/\d+/g, function(match) {
                            return convertNumbersToPersian(match);
                        });

                        // نمایش اطلاعات دریافت شده از دیتابیس در کارت پشتی با کلاس‌های CSS مشخص و تنظیم موقعیت
                        $('#pjc-event-details').html(`
                            <p class="event-header">فراخورهای روز ${convertNumbersToPersian(day)} ${getPersianMonthName(month)}</p>
                            <div class="event-content scrollable-container">
                                ${eventContent}
                            </div>
                            <button id="pjc-back-to-calendar" class="event-back-button">بازگشت به تقویم</button>
                        `);

                        // Reset scroll to top whenever card is flipped
                        $('.scrollable-container').scrollTop(0);
                    } else {
                        // پیام برای زمانی که رویدادی موجود نیست
                        $('#pjc-event-details').html(`
                            <div class="event-content scrollable">
                                ${eventContent}
                            </div>
                            <button id="pjc-back-to-calendar" class="event-back-button">بازگشت به تقویم</button>
                        `);
                    }

                    $('.pjc-flip-container').addClass('pjc-flipped');
                },
                error: function(xhr, status, error) {
                    console.error("AJAX request failed:", error);
                }
            });
        });
    }

    // تابع کمکی برای تبدیل اعداد به اعداد فارسی
    function convertNumbersToPersian(number) {
        const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return number.toString().replace(/\d/g, function(d) {
            return persianNumbers[d];
        });
    }

    // اطمینان از اجرای اولیه رویدادهای کلیک بر روزها
    bindDayClickEvents();

    // Use delegation to handle dynamic button
    $(document).on('click', '#pjc-back-to-calendar', function() {
        $('.pjc-flip-container').removeClass('pjc-flipped');
    });

    // Helper function to get the Persian name of the month
    function getPersianMonthName(month) {
        const months = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'اَمُرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسپند'];
        return months[month - 1]; // Adjust index as the month input is in numeric format
    }
});