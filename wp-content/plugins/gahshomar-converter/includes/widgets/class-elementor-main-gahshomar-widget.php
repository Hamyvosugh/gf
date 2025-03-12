<?php

namespace Gahshomar;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_Main_Gahshomar_Widget extends Widget_Base {

    public function get_name() {
        return 'gahshomar_converter';
    }

    public function get_title() {
        return __('از گاهشماری', 'gahshomar-converter');
    }

    public function get_icon() {
        return 'eicon-calendar';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('محتوا', 'gahshomar-converter'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        ?>
            <div id="gahshomar-converter-wrapper" lang="fa">
        <div id="gahshomar-converter-wrapper">
            <form id="gahshomar-converter-form">
                <div class="form-row">
                    <label for="source_calendar">از گاهشماری:</label>
                    <select name="source_calendar" id="source_calendar">
                        <option value="gregorian">میلادی</option>
                        <option value="jalaali">هجری شمسی</option>
                        <option value="padeshahi">شاهنشاهی</option>
                        <option value="eilami">ایلامی</option>
                        <option value="zartoshti">زرتشتی</option>
                        <option value="madi">کردی</option>
                        <option value="iran_nov">ایران نو</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="day">روز:</label>
                    <select id="day" name="day" required></select>
                </div>
                <div class="form-row">
                    <label for="month">ماه:</label>
                    <select id="month" name="month" required></select>
                </div>
                <div class="form-row">
                    <label for="year">سال:</label>
                    <input type="number" id="year" name="year" required>
                </div>
                <div class="form-row">
                    <label for="target_calendar">به گاهشماری:</label>
                    <select name="target_calendar" id="target_calendar">
                        <option value="gregorian">میلادی</option>
                        <option value="jalaali">هجری شمسی</option>
                        <option value="padeshahi">شاهنشاهی</option>
                        <option value="eilami">ایلامی</option>
                        <option value="zartoshti">زرتشتی</option>
                        <option value="madi">کردی</option>
                        <option value="iran_nov">ایران نو</option>
                    </select>
                </div>
                <div class="form-row">
                    <input type="submit" value= فراخوانی>
                </div>
            </form>
        </div>

        <div id="conversion-result-output">
            <p> گاهاز نگاره بالا برای دگرگون‌کردن تاریخ خود بهره بگیرید، همچنین می‌توانید زادروز خود را در گاهشمارهای گوناگون ایرانی ببینید.</p>
        </div>
        <div id="additional-info-output">
            <p>اطلاعات اضافی...</p>
        </div>

        <script>
        jQuery(document).ready(function($) {
            function toPersianDigits(str) {
                return str.replace(/\d/g, function(d) {
                    return '۰۱۲۳۴۵۶۷۸۹'.charAt(d);
                });
            }

            function populateDayDropdown() {
                const daySelect = $('#day');
                daySelect.empty();

                for (let i = 1; i <= 31; i++) {
                    const persianDigit = toPersianDigits(i.toString());
                    daySelect.append($('<option>', { value: i, text: persianDigit }));
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
                monthSelect.empty();

                const months = (calendar === 'gregorian') ? gregorianMonths : jalaaliMonths;

                $.each(months, function(value, label) {
                    monthSelect.append($('<option>', { value: value, text: label }));
                });
            }

            $('#source_calendar').on('change', function() {
                updateMonthOptions($(this).val());
            });

            // Initialize month options and day dropdown
            updateMonthOptions($('#source_calendar').val());
            populateDayDropdown();

            $('#gahshomar-converter-form').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
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
                        $('#conversion-result-output').html('<p>' + toPersianDigits(convertedDate) + '</p>');

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
                "یاقوت سرخ", "پریدوت",“یاقوت کبود”, “اوپال/تورمالین”, “توپاز/سیترین”, “فیروزه/زیرکن/تانزانیت”

            ];
return birthstones[month - 1];
}
function getChineseZodiacAnimal(year) {
var chineseZodiacAnimals = [
“موش”, “گاو”, “ببر”, “خرگوش”, “اژدها”, “مار”, “اسب”, “بز”,
“میمون”, “خروس”, “سگ”, “خوک”
];
var animalIndex = (year - 4) % 12;
return chineseZodiacAnimals[animalIndex];
}    function getZoroastrianDayName(day) {
        var zoroastrianDayNames = [
            "هرمزد", "بهمن", "اردیبهشت", "شهریور", "اسپندارمذ", "خرداد", "امرداد",
            "دی‌به‌آذر", "آذر", "آبان", "خورشید", "ماه", "تیر", "گوش", "دی‌به‌مهر",
            "مهر", "سروش", "رشن", "فروردین", "بهرام", "رام", "باد", "دی‌به‌دین",
            "دین", "اشیش‌ونگه", "اشتاد", "آسمان", "زمین", "ماه‌سپند", "انغران"
        ];
        return zoroastrianDayNames[(day - 1) % 30];
    }
});
</script>
<?php
}
}
?>














?>