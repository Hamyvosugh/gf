<?php
namespace Gahshomar;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_Converted_Date_Info_Widget extends Widget_Base {

    public function get_name() {
        return 'converted_date_info';
    }

    public function get_title() {
        return __('اطلاعات تاریخ برابری شده', 'gahshomar-converter');
    }

    public function get_icon() {
        return 'eicon-post-list';
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
        <div id="converted-date-info-output">
            <p> گاه‌گردانی...</p>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $(document).on('gahshomarConversionResultUpdated', function() {
                var result = window.gahshomarConversionResult || "ما کوشیده‌ایم تا بزرگترین گنجینه داده‌های تاریخی ایران را گردآوری کنیم و در دسترس شما بگذاریم. گاه‌گردان گاهشمار، نخستین دگرگونساز تاریخی برای ایرانیان است که می‌تواند دوران‌های کهن را نیز برآورد کند، اما این مورد که وارد کرده اید، شدنی نیست.";
                $('#converted-date-info-output').html('<p>' + result + '</p>');

                var day = getConvertedDay(result);
                var month = getConvertedMonth(result);
                var year = getConvertedYear(result);

                console.log("Extracted Day:", day);
                console.log("Extracted Month:", month);
                console.log("Extracted Year:", year);

                if (!isNaN(day) && !isNaN(month) && !isNaN(year)) {
                    // Call PHP functions via AJAX to get additional info
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'get_additional_info',
                            day: day,
                            month: month,
                            year: year
                        },
                        success: function(response) {
                            if (response.success) {
                                var additionalInfo = response.data.additional_info;
                                $('#converted-date-info-output').append('<p>' + additionalInfo + '</p>');
                            } else {
                                $('#converted-date-info-output').append('<p>اطلاعات اضافی یافت نشد.</p>');
                            }
                        },
                        error: function() {
                            $('#converted-date-info-output').append('<p>ما کوشیده‌ایم تا بزرگترین گنجینه داده‌های تاریخی ایران را گردآوری کنیم و در دسترس شما بگذاریم. گاه‌گردان گاهشمار، نخستین دگرگونساز تاریخی برای ایرانیان است که می‌تواند دوران‌های کهن را نیز برآورد کند، اما این مورد که وارد کرده اید، شدنی نیست.</p>');
                        }
                    });
                } else {
                    $('#converted-date-info-output').append('<p>اطلاعات اضافی یافت نشد.</p>');
                }
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
        });
        </script>
        <?php
    }
}