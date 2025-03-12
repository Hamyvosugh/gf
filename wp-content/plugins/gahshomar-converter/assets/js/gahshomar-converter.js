jQuery(document).ready(function($) {
    function initializeDaySelection() {
        var dropdownButton = $('#dropdown-button');
        var dropdownContent = $('#dropdown-content');
        var dayCells = $('#dropdown-content td');

        if (dropdownButton.length > 0 && dropdownContent.length > 0 && dayCells.length > 0) {
            dropdownButton.on('click', function() {
                dropdownContent.toggle(); // Toggle visibility
            });

            dayCells.on('click', function() {
                var selectedDayValue = $(this).attr('value');
                var selectedDayText = $(this).text().trim();
                $('#day').val(selectedDayValue);
                dropdownContent.hide();
                dropdownButton.text('روز انتخاب شده: ' + selectedDayText);
                dropdownButton.css('color', 'black'); // Ensure text color is black after selection
            });

            $(document).on('click', function(event) {
                if (!dropdownButton.is(event.target) && !dropdownContent.is(event.target) && dropdownContent.has(event.target).length === 0) {
                    dropdownContent.hide();
                }
            });
        }
    }

    function initializeCalendarSelection() {
        var calendarSelect = $('#source_calendar');

        if (calendarSelect.length > 0) {
            calendarSelect.on('change', function() {
                var selectedCalendarText = $(this).find('option:selected').text();
                var calendarDropdownButton = $(this);
                calendarDropdownButton.find('option:first').text('تقویم انتخاب شده: ' + selectedCalendarText);
                calendarDropdownButton.css('color', 'black'); // تغییر رنگ متن پس از انتخاب
            });

            $('#source_calendar').on('change', function() {
                updateMonthOptions($(this).val());
            });
        }
    }

    const gregorianMonths = {
        "1": "ژانویه", "2": "فوریه", "3": "مارس", "4": "آوریل", "5": "مه", "6": "ژوئن",
        "7": "ژوئیه", "8": "اوت", "9": "سپتامبر", "10": "اکتبر", "11": "نوامبر", "12": "دسامبر"
    };

    const jalaaliMonths = {
        "1": "فروردین", "2": "اردیبهشت", "3": "خرداد", "4": "تیر", "5": "اَمُرداد", "6": "شهریور",
        "7": "مهر", "8": "آبان", "9": "آذر", "10": "دی", "11": "بهمن", "12": "اسپند"
    };

    function updateMonthOptions(calendar) {
        const monthSelect = $('#month');
        if (monthSelect.length > 0) {
            monthSelect.empty();

            const months = (calendar === 'gregorian') ? gregorianMonths : jalaaliMonths;

            $.each(months, function(value, label) {
                monthSelect.append($('<option>', { value: value, text: label }));
            });
        }
    }

    // Initialize month options
    if ($('#source_calendar').length > 0) {
        updateMonthOptions($('#source_calendar').val());
    }
    initializeDaySelection();
    initializeCalendarSelection();

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
                    var convertedDate = response.data.output;
                    var targetCalendar = $('#target_calendar').val();
                    
                    if (targetCalendar === 'gregorian') {
                        var dateParts = convertedDate.split(' ');
                        var day = toPersianDigits(dateParts[0]);
                        var month = dateParts[1];
                        var year = toPersianDigits(dateParts[2]);
            
                        $('#conversion-result-output').html(
                            '<div class="day">' + day + '</div>' +
                            '<div class="month">' + month + '</div>' +
                            '<div class="year">' + year + '</div>'
                        );
                    } else {
                        $('#conversion-result-output').html('<p>' + toPersianDigits(convertedDate) + '</p>');
                    }
            
                    var day = getConvertedDay(convertedDate);
                    var month = getConvertedMonth(convertedDate);
                    var year = getConvertedYear(convertedDate);
            
                    if (!isNaN(day) && !isNaN(month) && !isNaN(year)) {
                        var additionalInfo = getAdditionalInfo(day, month, year);
                        $('#additional-info-output').html('<p>' + additionalInfo + '</p>');
                    } else {
                        $('#additional-info-output').html('<p>اطلاعات اضافی یافت نشد.</p>');
                    }
                } else {
                    $('#conversion-result-output').html('<p>ما کوشیده‌ایم تا بزرگترین گنجینه داده‌های تاریخی ایران را گردآوری کنیم و در دسترس شما بگذاریم. گاه‌گردان گاهشمار، نخستین دگرگونساز تاریخی برای ایرانیان است که می‌تواند دوران‌های کهن را نیز برآورد کند، اما این مورد که وارد کرده اید، شدنی نیست.</p>');
                }
            },
            error: function() {
                $('#conversion-result-output').html('<p>ما کوشیده‌ایم تا بزرگترین گنجینه داده‌های تاریخی ایران را گردآوری کنیم و در دسترس شما بگذاریم. گاه‌گردان گاهشمار، نخستین دگرگونساز تاریخی برای ایرانیان است که می‌تواند دوران‌های کهن را نیز برآورد کند، اما این مورد که وارد کرده اید، شدنی نیست.</p>');
            }
        });
    });


    function getConvertedDay(convertedDate) {
        var dateParts = convertedDate.split(' ');
        for (var i = 0; i < dateParts.length; i++) {
            var day = parseInt(dateParts[i], 10);
            if (!isNaN(day)) {
                return day;
            }
        }
        return NaN;
    }

    function getConvertedMonth(convertedDate) {
        var monthNames = [
            "فروردین", "اردیبهشت", "خرداد", "تیر", "اَمُرداد", "شهریور",
            "مهر", "آبان", "آذر", "دی", "بهمن", "اسپند",
            "ژانویه", "فوریه", "مارس", "آوریل", "مه", "ژوئن",
            "ژوئیه", "اوت", "سپتامبر", "اکتبر", "نوامبر", "دسامبر"
        ];

        for (var i = 0; i < monthNames.length; i++) {
            if (convertedDate.includes(monthNames[i])) {
                return (i % 12) + 1;
            }
        }
        return NaN;
    }

    function getConvertedYear(convertedDate) {
        var dateParts = convertedDate.split(' ');
        for (var i = 0; i < dateParts.length; i++) {
            var year = parseInt(dateParts[i], 10);
            if (!isNaN(year)) {
                return year;
            }
        }
        return NaN;
    }

    function getAdditionalInfo(day, month, year) {
        // Use the LeapYearChecker functions to get additional info
        var birthstone = getBirthstone(month);
        var chineseZodiacAnimal = getChineseZodiacAnimal(year);
        var zoroastrianDayName = getZoroastrianDayName(day);

        return 'سنگ تولد: ' + birthstone + '<br>' +
               'حیوان چینی: ' + chineseZodiacAnimal + '<br>' +
               'روز زرتشتی: ' + zoroastrianDayName;
    }

    function getBirthstone(month) {
        var birthstones = [
            "گارنت", "آمتیست", "آکوامارین", "الماس", "زمرد", "مروارید/الکساندریت",
            "یاقوت سرخ", "پریدوت", "یاقوت کبود", "اوپال/تورمالین", "توپاز/سیترین", "فیروزه/زیرکن/تانزانیت"
        ];
        return birthstones[month - 1];
    }

    function getChineseZodiacAnimal(year) {
        var chineseZodiacAnimals = [
            "موش", "گاو", "ببر", "خرگوش", "اژدها", "مار", "اسب", "بز",
            "میمون", "خروس", "سگ", "خوک"
        ];
        var animalIndex = (year - 4) % 12;
        return chineseZodiacAnimals[animalIndex];
    }

    function getZoroastrianDayName(day) {
        var zoroastrianDayNames = [
            "هرمزد", "بهمن", "اردیبهشت", "شهریور", "اسپندارمذ", "خرداد", "امرداد",
            "دی‌به‌آذر", "آذر", "آبان", "خورشید", "ماه", "تیر", "گوش", "دی‌به‌مهر",
            "مهر", "سروش", "رشن", "فروردین", "بهرام", "رام", "باد", "دی‌به‌دین",
            "دین", "اشیش‌ونگه", "اشتاد", "آسمان", "زمین", "ماه‌سپند", "انغران"
        ];
        return zoroastrianDayNames[(day - 1) % 30];
    }
});

function toPersianDigits(str) {
    return str.replace(/\d/g, function(d) {
        return '۰۱۲۳۴۵۶۷۸۹'.charAt(d);
    });
}
// تلاش برای پیدا کردن المنت با ID مشخص، برای مثال 'container-id'
var container = document.getElementById('container-id');

if (container) {
    // اگر المنت موجود بود، عملیات مورد نظر را انجام دهید
    console.log('Container:', container); // افزودن پیام در کنسول برای تأیید پیدا شدن المنت
    container.innerHTML = data;
} else {
    // اگر المنت وجود نداشت، خطایی نمایش ندهید و از آن صرف‌نظر کنید
    console.log('Info: Container not found, skipping update.');
}