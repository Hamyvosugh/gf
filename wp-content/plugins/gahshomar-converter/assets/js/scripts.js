jQuery(document).ready(function($) {
    $('#gahshomar-converter-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'gahshomar_converter',
                source_calendar: $('#source_calendar').val(),
                day: $('#day').val(),
                month: $('#month').val(),
                year: $('#year').val(),
                target_calendar: $('#target_calendar').val()
            },
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    if ($('#gregorian-date').length > 0) {
                        $('#gregorian-date').text(data.gregorian_date);
                    }
                    if ($('#jalaali-date').length > 0) {
                        $('#jalaali-date').text(data.jalaali_date);
                    }
                    // Add more fields if needed
                } else {
                    if ($('#gahshomar-converter-output').length > 0) {
                        $('#gahshomar-converter-output').html('<p>' + response.data.message + '</p>');
                    }
                }
            },
            error: function() {
                if ($('#gahshomar-converter-output').length > 0) {
                    $('#gahshomar-converter-output').html('<p>ما کوشیده‌ایم تا بزرگترین گنجینه داده‌های تاریخی ایران را گردآوری کنیم و در دسترس شما بگذاریم. گاه‌گردان گاهشمار، نخستین دگرگونساز تاریخی برای ایرانیان است که می‌تواند دوران‌های کهن را نیز برآورد کند، اما این مورد که وارد کرده اید، شدنی نیست.</p>');
                }
            }
        });
    });
});