<?php
namespace Gahshomar;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_Chinese_Zodiac_Widget extends Widget_Base {

    public function get_name() {
        return 'chinese_zodiac';
    }

    public function get_title() {
        return __('حیوان زودیاک چینی', 'gahshomar-converter');
    }

    public function get_icon() {
        return 'eicon-date';
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
        <div id="gahshomar-chinese-zodiac-wrapper">
            <h3>حیوان زودیاک چینی:</h3>
            <p id="gahshomar-chinese-zodiac">نامشخص</p>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $(document).on('gahshomarConversionResultUpdated', function() {
                var convertedDate = window.gahshomarConversionResult;
                var year = getConvertedYear(convertedDate);
                var month = getConvertedMonth(convertedDate);
                var day = getConvertedDay(convertedDate);

                console.log('Converted Year:', year);
                console.log('Converted Month:', month);
                console.log('Converted Day:', day);

                if (year && month && day) {
                    var chineseZodiacAnimal = getChineseZodiacAnimal(year, month, day);
                    $('#gahshomar-chinese-zodiac').text(chineseZodiacAnimal);
                } else {
                    $('#gahshomar-chinese-zodiac').text("نامشخص");
                }
            });

            function getConvertedYear(convertedDate) {
                var dateParts = convertedDate.split(' ');
                return parseInt(dateParts[dateParts.length - 1], 10);
            }

            function getConvertedMonth(convertedDate) {
                var monthNames = [
                    "ژانویه", "فوریه", "مارس", "آوریل", "مه", "ژوئن",
                    "ژوئیه", "اوت", "سپتامبر", "اکتبر", "نوامبر", "دسامبر",
                    "فروردین", "اردیبهشت", "خرداد", "تیر", "اَمُرداد", "شهریور",
                    "مهر", "آبان", "آذر", "دی", "بهمن", "اسپند"
                ];

                for (var i = 0; i < monthNames.length; i++) {
                    if (convertedDate.includes(monthNames[i])) {
                        return (i % 12) + 1;
                    }
                }
                return null;
            }

            function getConvertedDay(convertedDate) {
                var dateParts = convertedDate.split(' ');
                for (var i = 0; i < dateParts.length; i++) {
                    if (!isNaN(parseInt(dateParts[i], 10))) {
                        return parseInt(dateParts[i], 10);
                    }
                }
                return null;
            }

            function getChineseZodiacAnimal(year, month, day) {
                const lunarNewYearDates = {
                    2023: '01-22',
                    2024: '02-10',
                    2025: '01-29',
                    // Add more dates as needed
                };

                if (lunarNewYearDates[year]) {
                    const lunarNewYear = lunarNewYearDates[year].split('-');
                    const lunarNewYearMonth = parseInt(lunarNewYear[0], 10);
                    const lunarNewYearDay = parseInt(lunarNewYear[1], 10);

                    if (month < lunarNewYearMonth || (month == lunarNewYearMonth && day < lunarNewYearDay)) {
                        year--;
                    }
                }

                const animals = [
                    "موش", "گاو", "ببر", "خرگوش", "اژدها", "مار", "اسب", "بز",
                    "میمون", "خروس", "سگ", "خوک"
                ];

                const index = (year - 4) % 12;
                return animals[index];
            }
        });
        </script>
        <?php
    }
}
?>