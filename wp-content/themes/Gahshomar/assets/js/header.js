/**
 * Header functionality for Gahshomar theme
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const body = document.body;
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            body.classList.toggle('mobile-menu-active');
        });
    }
    
    // Header Scroll Effect for Mobile
    const header = document.getElementById('gahshomar-header');
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Only apply scroll effect on mobile
        if (window.innerWidth <= 768) {
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                header.classList.add('header-scrolled');
            } else {
                header.classList.remove('header-scrolled');
            }
        }
        
        lastScrollTop = scrollTop;
    });
    
    // Update Clock Labels
    function updateClockLabels() {
        const timezoneSelect = document.getElementById('timezone-select');
        const iranLabel = document.querySelector('.iran-label');
        
        if (timezoneSelect && iranLabel) {
            const selectedOption = timezoneSelect.options[timezoneSelect.selectedIndex];
            iranLabel.textContent = 'ایران: ' + selectedOption.text;
        }
    }
    
    // Clock Functionality
    function updateClocks() {
        const now = new Date();
        
        // Update Local Time Digital Display
        const localDigitalClock = document.getElementById('local-digital-clock');
        if (localDigitalClock) {
            localDigitalClock.textContent = now.toLocaleTimeString('fa-IR');
        }
        
        // Update Local Time Hands
        updateClockHands('local-time', now);
        
        // Get selected timezone
        const timezoneSelect = document.getElementById('timezone-select');
        let selectedTimezone = 'Asia/Tehran'; // Default
        
        if (timezoneSelect) {
            selectedTimezone = timezoneSelect.value;
        }
        
        // Update International Time
        const intTime = new Date(now.toLocaleString('en-US', { timeZone: selectedTimezone }));
        const iranDigitalClock = document.getElementById('iran-digital-clock');
        if (iranDigitalClock) {
            iranDigitalClock.textContent = intTime.toLocaleTimeString('fa-IR');
        }
        
        // Update International Time Hands
        updateClockHands('international-time', intTime);
        
        // Schedule next update
        setTimeout(updateClocks, 1000);
    }
    
    // Function to update clock hands
    function updateClockHands(clockId, time) {
        const clock = document.getElementById(clockId);
        if (!clock) return;
        
        const hours = time.getHours() % 12;
        const minutes = time.getMinutes();
        const seconds = time.getSeconds();
        
        // Calculate angles
        const hourAngle = (hours * 30) + (minutes * 0.5); // 30 degrees per hour plus small adjustment for minutes
        const minuteAngle = minutes * 6; // 6 degrees per minute
        const secondAngle = seconds * 6; // 6 degrees per second
        
        // Get the hands
        const hourHand = clock.querySelector('.hour-hand');
        const minuteHand = clock.querySelector('.minute-hand');
        const secondHand = clock.querySelector('.second-hand');
        
        // Set rotation angles
        if (hourHand) hourHand.style.transform = `rotate(${hourAngle}deg)`;
        if (minuteHand) minuteHand.style.transform = `rotate(${minuteAngle}deg)`;
        if (secondHand) secondHand.style.transform = `rotate(${secondAngle}deg)`;
    }
    
    // Handle timezone selection change
    const timezoneSelect = document.getElementById('timezone-select');
    if (timezoneSelect) {
        timezoneSelect.addEventListener('change', function() {
            updateClocks();
            updateClockLabels();
        });
    }
    
    // Initialize Clock Labels
    updateClockLabels();
    
    // Initialize Clocks
    updateClocks();
});