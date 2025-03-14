jQuery(document).ready(function($) {
    $('#month-filter').on('change', function() {
        filterCards();
    });

    $('#important-filter').on('change', function() {
        filterCards();
    });

    $('#view-filter').on('change', function() {
        filterCards();
    });

    function filterCards() {
        var month = $('#month-filter').val();
        var important = $('#important-filter').val();

        $('.card-item').show();

        if (month !== 'all') {
            $('.card-item').not('[data-month="' + month + '"]').hide();
        }

        if (important !== 'all') {
            $('.card-item').not('[data-important="' + important + '"]').hide();
        }
    }

    // Custom scrolling   function
    function scrollToElement(container, target) {
        var containerTop = $(container).offset().top;
        var targetTop = $(target).offset().top;
        var scrollTop = targetTop - containerTop + $(container).scrollTop();

        $(container).animate({
            scrollTop: scrollTop
        }, 1000);
    }

    // Example usage of the custom scroll function
    $('#month-filter, #important-filter, #view-filter').on('change', function() {
        var firstVisibleCard = $('.card-item:visible').first();
        if (firstVisibleCard.length) {
            scrollToElement('.gallery-container', firstVisibleCard);
        }
    });

    // Function to set mode class and ensure event cards have the correct colors
    function setMode() {
        var hour = new Date().getHours();
        var body = document.body;

        body.classList.remove('morning-mode', 'afternoon-mode', 'evening-mode', 'night-mode');

        if (hour >= 5 && hour < 12) {
            body.classList.add('morning-mode');
        } else if (hour >= 12 && hour < 17) {
            body.classList.add('afternoon-mode');
        } else if (hour >= 17 && hour < 21) {
            body.classList.add('evening-mode');
        } else {
            body.classList.add('night-mode');
        }

        // Ensure event cards have the correct colors
        var cards = document.querySelectorAll('.farakhor-flip-card-front, .farakhor-flip-card-back');

        cards.forEach(function(card) {
            if (card.classList.contains('farakhor-flip-card-back')) {
                card.style.backgroundColor = getComputedStyle(body).getPropertyValue('--back-color');
                card.style.color = getComputedStyle(body).getPropertyValue('--back-text-color');
            } else {
                card.style.backgroundColor = getComputedStyle(body).getPropertyValue('--front-color');
                card.style.color = getComputedStyle(body).getPropertyValue('--front-text-color');
            }
        });

        var textElements = document.querySelectorAll('.persian-day, .month, .event-title, .georgian, .text');

        textElements.forEach(function(element) {
            element.style.color = getComputedStyle(body).getPropertyValue('--text-color');
        });
    }

    setMode();
});