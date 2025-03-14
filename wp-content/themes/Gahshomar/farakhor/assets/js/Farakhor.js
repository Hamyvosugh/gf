// تعریف توابع جهانی
window.filterByTag = function(value) {
    const cards = document.querySelectorAll('.clickable-card');
    
    cards.forEach(card => {
        const title = card.querySelector('.farakhor-event-title').textContent;
        
        if (value === 'تعطیلات') {
            card.style.display = title.includes('تعطیلات') ? '' : 'none';
        } else if (value === 'جشن') {
            card.style.display = title.includes('جشن') ? '' : 'none';
        }
    });
};
// فیلتر بر اساس ماه
window.filterByMonth = function(selectedMonth) {
    console.log("ماه انتخابی: ", selectedMonth); // چاپ ماه انتخابی
    const cards = document.querySelectorAll(".clickable-card");
    console.log("تعداد کارت‌ها قبل از فیلتر: ", cards.length); // چاپ تعداد کارت‌ها
    
    cards.forEach(card => {
        const persianDate = card.getAttribute("data-date");
        const cardMonth = persianDate ? persianDate.slice(0, 2) : null;
        
        // چاپ ماه کارت فعلی
        console.log("ماه کارت: ", cardMonth);
        
        // نمایش فقط کارت‌هایی که با ماه انتخابی مطابقت دارند
        card.style.display = (selectedMonth === "" || cardMonth === selectedMonth) ? "" : "none";
    });

    const visibleCards = [...cards].filter(card => card.style.display !== "none");
    console.log("تعداد کارت‌های قابل مشاهده پس از فیلتر: ", visibleCards.length); // چاپ تعداد کارت‌های قابل مشاهده
};

// فیلتر بر اساس شهر
window.filterByCity = function(selectedCity) {
    const cards = document.querySelectorAll('.clickable-card');
    cards.forEach(card => {
        const tag = card.getAttribute('data-tag');
        card.style.display = (selectedCity === "" || (tag && tag.includes(selectedCity))) ? '' : 'none';
    });
};

window.filterByCountry = function(selectedCountry) {
    const cards = document.querySelectorAll('.clickable-card');
    cards.forEach(card => {
        const tag = card.getAttribute('data-tag');
        card.style.display = (selectedCountry === "" || (tag && tag.includes(selectedCountry))) ? '' : 'none';
    });
};

window.filterEvents = function() {
    const searchInput = document.getElementById('search-input');
    const searchValue = searchInput.value.toLowerCase();
    const cards = document.querySelectorAll('.clickable-card');
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchValue) ? '' : 'none';
    });
};

window.resetToCurrentMonth = function() {
    const currentPersianMonth = new Date().toLocaleString('fa-IR-u-nu-latn', { month: '2-digit' });
    const monthFilter = document.getElementById('month-filter');
    if (monthFilter) {
        monthFilter.value = currentPersianMonth;
        console.log("بازنشانی به ماه جاری فارسی:", currentPersianMonth); // چاپ بازنشانی
        filterByMonth(currentPersianMonth);
    }
};

// اتصال رویدادها
document.addEventListener('DOMContentLoaded', function () {
    const currentPersianMonth = new Date().toLocaleString('fa-IR-u-nu-latn', { month: '2-digit' });
    const monthFilter = document.getElementById('month-filter');
    if (monthFilter) {
        monthFilter.value = currentPersianMonth;
        filterByMonth(currentPersianMonth); // اعمال فیلتر  اولیه
        console.log("تنظیم فیلتر پیش‌فرض برای ماه جاری فارسی:", currentPersianMonth);

        monthFilter.addEventListener("change", function() {
            const selectedMonth = this.value;
            filterByMonth(selectedMonth);
            console.log("تغییر فیلتر ماه به:", selectedMonth);
        });
    }

    const countrySelector = document.getElementById('country-selector');
    if (countrySelector) {
        countrySelector.addEventListener('change', function () {
            filterByCountry(this.value);
        });
    }

    const citySelector = document.getElementById('city-selector');
    if (citySelector) {
        citySelector.addEventListener('change', function () {
            filterByCity(this.value);
        });
    }

    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('keyup', filterEvents);
    }

    document.querySelectorAll('.farakhor-sort-buttons-container button').forEach(button => {
        button.addEventListener('click', function () {
            const tagValue = this.getAttribute('onclick').match(/filterByTag\('(.+?)'\)/);
            if (tagValue) {
                filterByTag(tagValue[1]);
            }
        });
    });

    const resetButton = document.querySelector('.farakhor-refresh-button');
    if (resetButton) {
        resetButton.addEventListener('click', resetToCurrentMonth);
    }
});

// اضافه کردن قابلیت چرخش با کلیک در دستگاه‌های موبایل
document.addEventListener('DOMContentLoaded', function() {
    const flipCards = document.querySelectorAll('.farakhor-flip-card');
    
    flipCards.forEach(card => {
        card.addEventListener('click', function() {
            // فقط در موبایل فعال می‌شود
            if (window.innerWidth <= 768) {
                this.classList.toggle('flipped');
            }
        });
    });
});