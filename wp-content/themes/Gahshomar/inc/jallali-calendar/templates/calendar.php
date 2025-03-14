<?php
use Hekmatinasser\Verta\Verta;

$current_jalali = new Verta();
$current_month = $current_jalali->month;
$current_year = $current_jalali->year;
?>
<style>
@font-face {
    font-family: 'Digi Hamishe Bold';
    src: url('https://gahshomar.com/wp-content/uploads/2024/08/Digi-Hamishe-Bold.ttf') format('truetype');
    font-weight: bold;
    font-style: normal;
}

@font-face {
    font-family: 'Digi Hamishe Regular';
    src: url('https://gahshomar.com/wp-content/uploads/2024/08/Digi-Hamishe-Regular.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
}
    </style>
<div class="pjc-flip-container" style="font-family: 'Digi Hamishe Bold';">
    <div class="pjc-flip-card" style="font-family: 'Digi Hamishe Bold';">
        <!-- Front Side: Calendar -->
        <div class="pjc-front" style="font-family: 'Digi Hamishe Bold';">
            <div id="pjc-calendar" style="font-family: 'Digi Hamishe Bold';">
                <!-- Container for Calendar Controls -->
                <div class="pjc-calendar-controls" style="font-family: 'Digi Hamishe Bold';">
            

                    <!-- Dropdown for Month -->
                    <select id="pjc-month-select" class="pjc-select" style="font-family: 'Digi Hamishe Bold';">
                        <?php
                        $months = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'اَمُرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسپند'];
                        foreach ($months as $index => $month_name) {
                            $selected = ($index + 1 == $current_month) ? 'selected' : '';
                            echo "<option value='" . ($index + 1) . "' $selected>$month_name</option>";
                        }
                        ?>
                    </select>

                    <!-- Dropdown for Year -->
                    <select id="pjc-year-select" class="pjc-select" style="font-family: 'Digi Hamishe Bold';">
                        <?php
                        for ($i = 1390; $i <= 1420; $i++) {
                            $selected = ($i == $current_year) ? 'selected' : '';
                            echo "<option value='$i' $selected>" . convertNumbersToPersian($i) . "</option>";
                        }
                        ?>
                    </select>

                    <!-- New Dropdown for Calendar Type -->
                    <select id="pjc-calendar-type" class="pjc-select" style="font-family: 'Digi Hamishe Bold';">
                        <option value="padishahi" selected>شاهنشاهی</option>
                        <option value="ilami">ایلامی</option>
                        <option value="zartoshti">زرتشتی</option>
                        <option value="madi">کردی</option>
                        <option value="tabari">تبری</option>
                        <option value="iran-nov">ایران نو</option>
                        <option value="hejri">هجری</option>
                    </select>
                    <!-- Button for Return to Today (Positioned under the Calendar Controls) -->
                    <button id="pjc-return-button" class="pjc-return-button" style="font-family: 'Digi Hamishe Bold';">بازگشت به امروز</button>
                </div>
                
                <div id="pjc-calendar-content" style="font-family: 'Digi Hamishe Bold';">
                    <?php echo pjc_get_calendar($current_year, $current_month); ?>
                </div>
                <!-- tabari html display -->

                <div id="pjc-tabari-date-container" style="display: none; position: absolute; bottom: 2px; color:white; right:0px; text-align: right; direction:rtl; margin-top: 10px; font-family: 'Digi Hamishe Bold';">
    <span>تاریخ تبری: </span>
    <span id="pjc-tabari-date"></span>
</div>

            </div>
            
        </div>

        <!-- Back Side: Event Details -->
        <div class="pjc-back" style="font-family: 'Digi Hamishe Bold';">
            <div id="pjc-event-details" style="font-family: 'Digi Hamishe Bold';">
                <p>اطلاعات رویدادها در اینجا نمایش داده می‌شود</p>
                <!-- Add Button to Flip Back -->
                <button id="pjc-back-to-calendar" style="font-family: 'Digi Hamishe Bold';" >بازگشت به تقویم</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Inline Script for Handling Flip Effect and Calendar Type Change
    document.addEventListener('DOMContentLoaded', function() {
        // Flip to Back when a Day is Clicked
        document.querySelectorAll('.pjc-calendar-table td[data-day]').forEach(function(dayElement) {
            dayElement.addEventListener('click', function() {
                document.querySelector('.pjc-flip-container').classList.add('pjc-flipped');
            });
        });

        // Button to Flip Back to Front Side
        document.getElementById('pjc-back-to-calendar').addEventListener('click', function() {
            document.querySelector('.pjc-flip-container').classList.remove('pjc-flipped');
        });

        // Update years based on the selected calendar type
        function updateYearSelect(selectedType) {
            const yearSelect = document.getElementById('pjc-year-select');
            const jalaliYear = parseInt(new Date().getFullYear()) - 621; // Get current Jalali year approx

            let yearDifference = 0;
            switch (selectedType) {
                case "padishahi":
                    yearDifference = 1180;
                    break;
                case "ilami":
                    yearDifference = 3821;
                    break;
                case "zartoshti":
                    yearDifference = 2359;
                    break;
                case "madi":
                    yearDifference = 1321;
                    break;
                case "tabari":
                    yearDifference = 132; // اختلاف 132 سال برای تقویم تبری
                    break;
                case "iran-nov":
                    yearDifference = -1396;
                    break;
                case "hejri":
                    yearDifference = 0;
                    break;
            }

            // Recalculate the years based on the selected calendar
            yearSelect.innerHTML = "";
            for (let i = jalaliYear - 15; i <= jalaliYear + 15; i++) {
                const convertedYear = i + yearDifference;
                const option = document.createElement('option');
                option.value = i; // Keep original Jalali year for backend operations
                option.textContent = convertNumbersToPersian(convertedYear); // Display calculated year
                if (i === jalaliYear) {
                    option.selected = true;
                }
                yearSelect.appendChild(option);
            }
        }

        // Update the year dropdown based on calendar type when changed
        document.getElementById('pjc-calendar-type').addEventListener('change', function() {
            updateYearSelect(this.value);
        });

        // Initialize to the Padishahi calendar by default on page load
        updateYearSelect('padishahi');

        // Highlight Current Day
        function highlightCurrentDay() {
            let currentDayIndex = new Date().getDay(); // JS day index, 0 (Sunday) to 6 (Saturday)

            // Map JS day index to your calendar starting from Saturday (0) to Friday (6)
            if (currentDayIndex === 0) { // Sunday in JS is 1 in your calendar
                currentDayIndex = 1;
            } else if (currentDayIndex === 6) { // Saturday in JS is 0
                currentDayIndex = 0;
            } else { // Adjust for other days
                currentDayIndex--;
            }

            const dayNames = ['day-Saturday', 'day-Sunday', 'day-Monday', 'day-Tuesday', 'day-Wednesday', 'day-Thursday', 'day-Friday'];
            const currentDayClass = dayNames[currentDayIndex];

            // Remove highlighting from all headers
            document.querySelectorAll('.pjc-calendar-table th').forEach(header => {
                header.style.backgroundColor = '';
                header.style.color = '';
                header.style.borderRadius = '';
                header.style.padding = '';
            });

            // Remove any highlighting for the current day
            const currentDayHeaderElement = document.querySelector('.' + currentDayClass);
            if (currentDayHeaderElement) {
                // No styles applied to the current day header element
            }
        }

        // Run Highlight Functions
        highlightCurrentDay();
    });

    // Helper function to convert numbers to Persian
    function convertNumbersToPersian(number) {
        const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return number.toString().replace(/\d/g, function(d) {
            return persianNumbers[d];
        });
    }

// تابع برای نمایش تاریخ تبری بر اساس تاریخ جلالی انتخاب شده
function updateTabariDate() {
    // دریافت تاریخ جلالی انتخاب شده
    const jalaliMonth = parseInt(document.getElementById('pjc-month-select').value);
    const jalaliYear = parseInt(document.getElementById('pjc-year-select').value);
    const selectedDay = document.querySelector('.pjc-calendar-table td.selected-day');
    let jalaliDay = 1;
    
    if (selectedDay) {
        jalaliDay = parseInt(selectedDay.dataset.day);
    }
    
    // اگر نوع تقویم تبری انتخاب شده باشد
    if (document.getElementById('pjc-calendar-type').value === 'tabari') {
        // فراخوانی تابع AJAX برای تبدیل تاریخ جلالی به تبری
        jQuery.ajax({
            url: pjcAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'convert_to_tabari',
                year: jalaliYear,
                month: jalaliMonth,
                day: jalaliDay
            },
            success: function(response) {
                if (response.success) {
                    // نمایش تاریخ تبری در جایی که می‌خواهید با تبدیل اعداد به فارسی
                    const tabariDate = response.data;
                    const persianDay = convertNumbersToPersian(tabariDate.day);
                    const persianYear = convertNumbersToPersian(tabariDate.year);
                    document.getElementById('pjc-tabari-date').textContent = 
                        persianDay + ' ' + tabariDate.month + ' ' + persianYear;
                }
            }
        });
    }
}

// رویداد تغییر نوع تقویم
document.getElementById('pjc-calendar-type').addEventListener('change', function() {
    if (this.value === 'tabari') {
        // نمایش بخش تاریخ تبری
        document.getElementById('pjc-tabari-date-container').style.display = 'block';
        updateTabariDate();
    } else {
        // پنهان کردن بخش تاریخ تبری
        document.getElementById('pjc-tabari-date-container').style.display = 'none';
    }
});

// رویداد تغییر ماه یا سال
document.getElementById('pjc-month-select').addEventListener('change', updateTabariDate);
document.getElementById('pjc-year-select').addEventListener('change', updateTabariDate);

// کلیک روی روزهای تقویم
document.addEventListener('click', function(e) {
    if (e.target.closest('.pjc-calendar-table td[data-day]')) {
        setTimeout(updateTabariDate, 100); // تأخیر کوتاه برای اطمینان از انتخاب روز
    }
});
</script>







