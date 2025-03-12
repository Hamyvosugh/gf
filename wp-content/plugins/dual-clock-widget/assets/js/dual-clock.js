jQuery(document).ready(function($) {
    const localClock = $('#dcw-local-time');
    const internationalClock = $('#dcw-international-time');
    const timezoneSelect = $('#dcw-timezone-select, #dcw-timezone-select-standalone');

    const timezones = getAvailableTimezones(); 
    
    timezoneSelect.each(function() {
        for (const [zone, name] of Object.entries(timezones)) {
            const option = $('<option></option>').val(zone).text(name);
            $(this).append(option);
        }
        $(this).val("Asia/Tehran");
    });

    function updateClock(clock, date) {
        const hourHand = clock.find('.dcw-hour-hand');
        const minuteHand = clock.find('.dcw-minute-hand');
        const secondHand = clock.find('.dcw-second-hand');
        const digitalClock = clock.find('.dcw-digital-clock');

        const hours = date.getHours();
        const minutes = date.getMinutes();
        const seconds = date.getSeconds();

        const hourDegrees = (hours % 12) * 30 + (minutes / 2);
        const minuteDegrees = minutes * 6;
        const secondDegrees = seconds * 6;

        hourHand.css('transform', `rotate(${hourDegrees}deg)`);
        minuteHand.css('transform', `rotate(${minuteDegrees}deg)`);
        secondHand.css('transform', `rotate(${secondDegrees}deg)`);

        digitalClock.text(
            `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
        );
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

function getAvailableTimezones() {
    return {
        "Pacific/Midway": "ایالات متحده: میدوی",
        "Pacific/Honolulu": "ایالات متحده: هونولولو",
        "America/Juneau": "ایالات متحده: جونو",
        "America/Los_Angeles": "ایالات متحده: لس آنجلس",
        "America/Tijuana": "ایالات متحده: تیجوانا",
        "America/Denver": "ایالات متحده: دنور",
        "America/Phoenix": "ایالات متحده: فینیکس",
        "America/Chihuahua": "مکزیک: چیهواهوا",
        "America/Mazatlan": "مکزیک: مازاتلان",
        "America/Chicago": "ایالات متحده: شیکاگو",
        "America/Regina": "کانادا: رجینا",
        "America/Mexico_City": "مکزیک: مکزیکو سیتی",
        "America/Monterrey": "مکزیک: مونتری",
        "America/Guatemala": "گواتمالا: گواتمالا",
        "America/New_York": "ایالات متحده: نیویورک",
        "America/Indianapolis": "ایالات متحده: ایندیاناپولیس",
        "America/Bogota": "کلمبیا: بوگوتا",
        "America/Lima": "پرو: لیما",
        "America/Halifax": "کانادا: هالیفاکس",
        "America/Caracas": "ونزوئلا: کاراکاس",
        "America/La_Paz": "بولیوی: لاپاز",
        "America/Santiago": "شیلی: سانتیاگو",
        "America/St_Johns": "کانادا: سنت جونز",
        "America/Sao_Paulo": "برزیل: سائو پائولو",
        "America/Argentina/Buenos_Aires": "آرژانتین: بوینس آیرس",
        "America/Guyana": "گویان: گویان",
        "America/Godthab": "گرینلند: گادتاب",
        "Atlantic/Azores": "پرتغال: آزور",
        "Atlantic/Cape_Verde": "کیپ ورد: کیپ ورد",
        "Europe/London": "انگلستان: لندن",
        "Europe/Lisbon": "پرتغال: لیسبون",
        "Africa/Casablanca": "مراکش: کازابلانکا",
        "Africa/Monrovia": "لیبریا: مونروویا",
        "Europe/Amsterdam": "هلند: آمستردام",
        "Europe/Belgrade": "صربستان: بلگراد",
        "Europe/Berlin": "آلمان: برلین",
        "Europe/Bratislava": "اسلواکی: براتیسلاوا",
        "Europe/Brussels": "بلژیک: بروکسل",
        "Europe/Budapest": "مجارستان: بوداپست",
        "Europe/Copenhagen": "دانمارک: کپنهاگ",
        "Europe/Dublin": "ایرلند: دوبلین",
        "Europe/Ljubljana": "اسلوونی: لیوبلیانا",
        "Europe/Madrid": "اسپانیا: مادرید",
        "Europe/Paris": "فرانسه: پاریس",
        "Europe/Prague": "جمهوری چک: پراگ",
        "Europe/Rome": "ایتالیا: رم",
        "Europe/Sarajevo": "بوسنی: سارایوو",
        "Europe/Skopje": "مقدونیه: اسکوپیه",
        "Europe/Stockholm": "سوئد: استکهلم",
        "Europe/Vienna": "اتریش: وین",
        "Europe/Warsaw": "لهستان: ورشو",
        "Europe/Zagreb": "کرواسی: زاگرب",
        "Europe/Athens": "یونان: آتن",
        "Europe/Bucharest": "رومانی: بخارست",
        "Africa/Cairo": "مصر: قاهره",
        "Africa/Harare": "زیمبابوه: حراره",
        "Europe/Helsinki": "فنلاند: هلسینکی",
        "Europe/Istanbul": "ترکیه: استانبول",
        "Asia/Jerusalem": "اسرائیل: اورشلیم",
        "Europe/Kiev": "اوکراین: کیف",
        "Europe/Minsk": "بلاروس: مینسک",
        "Europe/Riga": "لتونی: ریگا",
        "Europe/Sofia": "بلغارستان: صوفیه",
        "Europe/Tallinn": "استونی: تالین",
        "Europe/Vilnius": "لیتوانی: ویلنیوس",
        "Asia/Baghdad": "عراق: بغداد",
        "Asia/Kuwait": "کویت: کویت",
        "Africa/Nairobi": "کنیا: نایروبی",
        "Asia/Riyadh": "عربستان: ریاض",
        "Asia/Tehran": "ایران: تهران",
        "Asia/Muscat": "عمان: مسقط",
        "Asia/Baku": "آذربایجان: باکو",
        "Asia/Yerevan": "ارمنستان: ایروان",
        "Asia/Tbilisi": "گرجستان: تفلیس",
        "Asia/Kabul": "افغانستان: کابل",
        "Asia/Yekaterinburg": "روسیه: یکاترینبورگ",
        "Asia/Karachi": "پاکستان: کراچی",
        "Asia/Tashkent": "ازبکستان: تاشکند",
        "Asia/Kolkata": "هند: کلکته",
        "Asia/Kathmandu": "نپال: کاتماندو",
        "Asia/Almaty": "قزاقستان: آلماتی",
        "Asia/Dhaka": "بنگلادش: داکا",
        "Asia/Novosibirsk": "روسیه: نووسیبیرسک",
        "Asia/Bangkok": "تایلند: بانکوک",
        "Asia/Jakarta": "اندونزی: جاکارتا",
        "Asia/Krasnoyarsk": "روسیه: کراسنویارسک",
        "Asia/Shanghai": "چین: شانگهای",
        "Asia/Chongqing": "چین: چونگ‌کینگ",
        "Asia/Hong_Kong": "چین: هنگ‌کنگ",
        "Asia/Urumqi": "چین: اورومچی",
        "Asia/Kuala_Lumpur": "مالزی: کوالالامپور",
        "Asia/Singapore": "سنگاپور: سنگاپور",
        "Asia/Taipei": "تایوان: تایپه",
        "Australia/Perth": "استرالیا: پرت",
        "Asia/Irkutsk": "روسیه: ایرکوتسک",
        "Asia/Seoul": "کره جنوبی: سئول",
        "Asia/Tokyo": "ژاپن: توکیو",
        "Asia/Yakutsk": "روسیه: یاکوتسک",
        "Australia/Darwin": "استرالیا: داروین",
        "Australia/Adelaide": "استرالیا: آدلاید",
        "Australia/Sydney": "استرالیا: سیدنی",
        "Australia/Brisbane": "استرالیا: بریزبن",
        "Australia/Hobart": "استرالیا: هوبارت",
        "Asia/Vladivostok": "روسیه: ولادی‌وستوک",
        "Pacific/Guam": "گوام: گوام",
        "Pacific/Port_Moresby": "پاپوآ گینه نو: پورت مورسبی",
        "Asia/Magadan": "روسیه: ماگادان",
        "Asia/Kamchatka": "روسیه: کامچاتکا",
        "Pacific/Fiji": "فیجی: فیجی",
        "Pacific/Auckland": "نیوزلند: آوکلند",
        "Pacific/Tongatapu": "تونگا: تونگاتاپو"
    };
}

