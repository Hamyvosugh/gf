<?php
namespace Gahshomar;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_Gahshomar_Widget extends Widget_Base {

    public function get_name() {
        return 'gahshomar_converter';
    }

    public function get_title() {
        return __('مبدل تاریخ', 'gahshomar-converter');
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



     <div id="gahshomar-converter-wrapper">
    <form id="gahshomar-converter-form">
        <div class="form-row">
            <select name="source_calendar" id="source_calendar">
                <option value="" disabled selected>گزینش گاهشمار پایه</option>
                <option value="jalaali">هجری شمسی</option>
                <option value="padeshahi">شاهنشاهی</option>    
                <option value="gregorian">میلادی</option>
                <option value="eilami">ایلامی</option>
                <option value="zartoshti">زرتشتی</option>
                <option value="madi">کردی</option>
                <option value="iran_nov">ایران نو</option>
            </select>
        </div>



                 <div class="form-row" style="width: 100%;">
            <div id="day-container" style="position: relative; flex: 2; width: 100%;">
                <div id="dropdown-button" style="width: 100%; padding: 8px; background-color: white; color: black; border: 1px solid #ffffff; border-radius: 4px; text-align: right; cursor: pointer;">
                    انتخاب روز
                </div>
                <div id="dropdown-content" class="dropdown-content" style="display: none; position: absolute; background-color: #333; padding: 10px; border-radius: 4px; z-index: 1; width: 100%; max-height: 250px; overflow-y: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: center;">
                        <tr>
                    <td value="7" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۷</td>
                    <td value="6" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۶</td>
                    <td value="5" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۵</td>
                    <td value="4" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۴</td>
                    <td value="3" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۳</td>
                    <td value="2" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۲</td>
                    <td value="1" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۱</td>
                </tr>
                <tr>
                    <td value="14" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۱۴</td>
                    <td value="13" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۱۳</td>
                    <td value="12" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۱۲</td>
                    <td value="11" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۱۱</td>
                    <td value="10" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۱۰</td>
                    <td value="9" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۹</td>
                    <td value="8" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۸</td>
                </tr>
                <tr>
                    <td value="21" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۲۱</td>
                    <td value="20" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۲۰</td>
                    <td value="19" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۱۹</td>
                    <td value="18" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۱۸</td>
                    <td value="17" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۱۷</td>
                    <td value="16" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۱۶</td>
                    <td value="15" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۱۵</td>
                </tr>
                <tr>
                    <td value="28" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۲۸</td>
                    <td value="27" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۲۷</td>
                    <td value="26" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۲۶</td>
                    <td value="25" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۲۵</td>
                    <td value="24" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۲۴</td>
                    <td value="23" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۲۳</td>
                    <td value="22" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۲۲</td>
                </tr>
                <tr>
                    <td value="31" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۳۱</td>
                    <td value="30" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۳۰</td>
                    <td value="29" style="padding: 10px; color: #ffffff; cursor: pointer; border: 1px solid #555;">۲۹</td>
            
                </tr>
                            </table>
        </div>
    </div>
    <input type="hidden" id="day" name="day" required>
</div>

                <div class="form-row">
                    <select id="month" name="month" required>
                        <option value="1">فروردین</option>
                        <option value="2">اردیبهشت</option>
                        <option value="3">خرداد</option>
                        <option value="4">تیر</option>
                        <option value="5">اَمُرداد</option>
                        <option value="6">شهریور</option>
                        <option value="7">مهر</option>
                        <option value="8">آبان</option>
                        <option value="9">آذر</option>
                        <option value="10">دی</option>
                        <option value="11">بهمن</option>
                        <option value="12">اسپند</option>
                    </select>
                </div>

                <div class="form-row">
    <input type="number" id="year" name="year" placeholder="انتخاب سال - از اعداد لاتین استفاده کنید" required>
</div>

<style>
    .form-row input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }
</style>



<script>
// تابع تبدیل اعداد فارسی به لاتین
function convertPersianToLatinNumbers(input) {
    const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    const latinNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
    return input.replace(/[۰-۹]/g, function (char) {
        return latinNumbers[persianNumbers.indexOf(char)];
    });
}

// تبدیل اعداد هنگام تایپ
document.getElementById('year').addEventListener('input', function() {
    this.value = convertPersianToLatinNumbers(this.value);
});
</script>

                <div id="gahshomar-converter-wrapper2">

    <form id="gahshomar-converter-form2">
        <div class="form-row">
            <select name="target_calendar" id="target_calendar">
                <option value="" disabled selected>گزینش گاهشمار هدف</option>
                <option value="jalaali">هجری شمسی</option>
                <option value="padeshahi">شاهنشاهی</option>    
                <option value="gregorian">میلادی</option>
                <option value="eilami">ایلامی</option>
                <option value="zartoshti">زرتشتی</option>
                <option value="madi">کردی</option>
                <option value="iran_nov">ایران نو</option>
            </select>
        </div>
                <div class="form-row">
                    <input type="submit" value= فراخوانی>
                </div>
                <input type="hidden" id="birthstone" name="birthstone">
            </form>
        </div>
        <script>
        jQuery(document).ready(function($) {
            function toPersianDigits(str) {
                return str.replace(/\d/g, function(d) {
                    return '۰۱۲۳۴۵۶۷۸۹'.charAt(d);
                });
            }

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
                            var output = toPersianDigits(response.data.output);
                            window.gahshomarConversionResult = output;
                            $(document).trigger('gahshomarConversionResultUpdated', [response.data.birthstone]);
                        } else {
                            window.gahshomarConversionResult = "ما کوشیده‌ایم تا بزرگترین گنجینه داده‌های تاریخی ایران را گردآوری کنیم و در دسترس شما بگذاریم. گاه‌گردان گاهشمار، نخستین دگرگونساز تاریخی برای ایرانیان است که می‌تواند دوران‌های کهن را نیز برآورد کند، اما این مورد که وارد کرده اید، شدنی نیست.";
                            $(document).trigger('gahshomarConversionResultUpdated', [null]);
                        }
                    },
                    error: function() {
                        window.gahshomarConversionResult = "ما کوشیده‌ایم تا بزرگترین گنجینه داده‌های تاریخی ایران را گردآوری کنیم و در دسترس شما بگذاریم. گاه‌گردان گاهشمار، نخستین دگرگونساز تاریخی برای ایرانیان است که می‌تواند دوران‌های کهن را نیز برآورد کند، اما این مورد که وارد کرده اید، شدنی نیست.";
                        $(document).trigger('gahshomarConversionResultUpdated', [null]);
                    }
                });
            });
        });
        </script>
        <?php
    }
}
?>